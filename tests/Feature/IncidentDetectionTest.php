<?php

namespace Tests\Feature;

use App\Models\Monitor;
use App\Models\MonitorCheck;
use App\Services\Monitoring\IncidentTracker;
use App\Services\Monitoring\MonitorChecker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IncidentDetectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_incident_opens_after_consecutive_failures(): void
    {
        Http::fake(['https://example.com/*' => Http::sequence()->pushStatus(500)->pushStatus(500)->pushStatus(500)]);
        $monitor = Monitor::factory()->create(['target_url' => 'https://example.com/health']);

        $checker = app(MonitorChecker::class);
        $checker->check($monitor);
        $checker->check($monitor->fresh());
        $checker->check($monitor->fresh());

        $this->assertDatabaseHas('incidents', [
            'monitor_id' => $monitor->id,
            'status' => 'open',
            'failure_count' => 3,
        ]);
        $this->assertSame(1, $monitor->incidents()->count());
    }

    public function test_temporary_failure_does_not_open_an_incident(): void
    {
        Http::fake(['https://example.com/*' => Http::sequence()->pushStatus(500)->pushStatus(200)]);
        $monitor = Monitor::factory()->create(['target_url' => 'https://example.com/health']);
        $checker = app(MonitorChecker::class);

        $checker->check($monitor);
        $checker->check($monitor->fresh());

        $this->assertDatabaseCount('incidents', 0);
    }

    public function test_recovery_resolves_the_open_incident_and_records_duration(): void
    {
        Http::fake(['https://example.com/*' => Http::sequence()->pushStatus(500)->pushStatus(500)->pushStatus(500)->pushStatus(200)]);
        $monitor = Monitor::factory()->create(['target_url' => 'https://example.com/health']);
        $checker = app(MonitorChecker::class);

        $checker->check($monitor);
        $checker->check($monitor->fresh());
        $checker->check($monitor->fresh());
        $checker->check($monitor->fresh());

        $this->assertDatabaseHas('incidents', ['monitor_id' => $monitor->id, 'status' => 'resolved']);
        $this->assertNotNull($monitor->incidents()->first()->duration_seconds);
    }

    public function test_duplicate_failure_processing_does_not_create_duplicate_incidents(): void
    {
        Http::fake(['https://example.com/*' => Http::response('', 500)]);
        $monitor = Monitor::factory()->create(['target_url' => 'https://example.com/health']);
        $checker = app(MonitorChecker::class);

        $checker->check($monitor);
        $checker->check($monitor->fresh());
        $checker->check($monitor->fresh());
        $checker->check($monitor->fresh());

        $this->assertSame(1, $monitor->incidents()->count());
    }

    public function test_out_of_order_checks_only_allow_the_latest_check_to_transition_state(): void
    {
        $monitor = Monitor::factory()->create();
        $older = MonitorCheck::factory()->for($monitor)->create(['status' => 'down', 'checked_at' => now()->subMinutes(3)]);
        MonitorCheck::factory()->for($monitor)->create(['status' => 'down', 'checked_at' => now()->subMinutes(2)]);
        $latest = MonitorCheck::factory()->for($monitor)->create(['status' => 'down', 'checked_at' => now()->subMinute()]);

        $tracker = app(IncidentTracker::class);
        $tracker->process($older);
        $this->assertDatabaseCount('incidents', 0);

        $tracker->process($latest);
        $this->assertDatabaseHas('incidents', ['monitor_id' => $monitor->id, 'status' => 'open']);
    }
}
