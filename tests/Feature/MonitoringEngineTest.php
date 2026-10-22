<?php

namespace Tests\Feature;

use App\Exceptions\RetryableMonitorCheckException;
use App\Jobs\CheckMonitor;
use App\Models\Monitor;
use App\Models\Organization;
use App\Models\User;
use App\Services\Monitoring\MonitorChecker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MonitoringEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_get_check_is_recorded(): void
    {
        Http::fake(['https://example.com/*' => Http::response('ok', 200)]);
        $monitor = Monitor::factory()->create(['target_url' => 'https://example.com/health']);

        $check = app(MonitorChecker::class)->check($monitor);

        $this->assertSame('up', $check->status);
        $this->assertSame(200, $check->http_status);
        $this->assertNotNull($check->response_time_ms);
        Http::assertSent(fn ($request) => $request->method() === 'GET');
    }

    public function test_head_check_and_unexpected_status_are_recorded(): void
    {
        Http::fake(['https://example.com/*' => Http::response('', 204)]);
        $monitor = Monitor::factory()->create(['target_url' => 'https://example.com/health', 'method' => 'HEAD', 'expected_status' => 200]);

        $check = app(MonitorChecker::class)->check($monitor);

        $this->assertSame('unexpected_status', $check->status);
        $this->assertSame(204, $check->http_status);
        Http::assertSent(fn ($request) => $request->method() === 'HEAD');
    }

    public function test_private_destinations_are_rejected(): void
    {
        $monitor = Monitor::factory()->create(['target_url' => 'http://127.0.0.1/admin']);

        $check = app(MonitorChecker::class)->check($monitor);

        $this->assertSame('down', $check->status);
        $this->assertStringContainsString('Private', $check->error_message);
        Http::assertNothingSent();
    }

    public function test_connection_failures_are_recorded_as_down(): void
    {
        Http::fake(['https://example.com/*' => Http::failedConnection()]);
        $monitor = Monitor::factory()->create(['target_url' => 'https://example.com/health']);

        $check = app(MonitorChecker::class)->check($monitor);

        $this->assertSame('down', $check->status);
        $this->assertSame('Connection failed or timed out.', $check->error_message);
    }

    public function test_invalid_urls_are_recorded_as_down_without_a_request(): void
    {
        $monitor = Monitor::factory()->create(['target_url' => 'ftp://example.com/file']);

        $check = app(MonitorChecker::class)->check($monitor);

        $this->assertSame('down', $check->status);
        $this->assertStringContainsString('HTTP or HTTPS', $check->error_message);
        Http::assertNothingSent();
    }

    public function test_due_monitors_are_dispatched_to_the_queue(): void
    {
        Queue::fake();
        $organization = Organization::factory()->create();
        $user = User::factory()->create();
        $organization->users()->attach($user, ['role' => 'owner']);
        $monitor = Monitor::factory()->for($organization)->create(['next_check_at' => now()->subMinute()]);

        $this->artisan('monitors:dispatch-due')->assertSuccessful();

        Queue::assertPushed(CheckMonitor::class, fn (CheckMonitor $job) => $job->monitor->is($monitor));
    }

    public function test_infrastructure_failures_are_retried_and_recorded_after_final_attempt(): void
    {
        Http::fake(['https://example.com/*' => Http::failedConnection()]);
        $monitor = Monitor::factory()->create(['target_url' => 'https://example.com/health']);
        $job = new CheckMonitor($monitor);

        $this->assertSame(3, $job->tries);
        $this->assertSame([10, 60], $job->backoff());

        try {
            $job->handle(app(MonitorChecker::class));
            $this->fail('The infrastructure failure should be retryable.');
        } catch (RetryableMonitorCheckException $exception) {
            $job->failed($exception);
        }

        $this->assertDatabaseHas('monitor_checks', ['monitor_id' => $monitor->id, 'status' => 'down']);
    }
}
