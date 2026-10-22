<?php

namespace Tests\Feature;

use App\Jobs\SendIncidentNotification;
use App\Mail\IncidentNotificationMail;
use App\Models\Incident;
use App\Models\Monitor;
use App\Models\Organization;
use App\Models\User;
use App\Services\Monitoring\MonitorChecker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class IncidentNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_opened_incident_queues_one_notification_per_enabled_user(): void
    {
        Queue::fake();
        Http::fake(['https://example.com/*' => Http::response('', 500)]);
        [$organization] = $this->organizationWithUser();
        $monitor = Monitor::factory()->for($organization)->create(['target_url' => 'https://example.com/health']);
        $checker = app(MonitorChecker::class);

        $checker->check($monitor);
        $checker->check($monitor->fresh());
        $checker->check($monitor->fresh());

        Queue::assertPushed(SendIncidentNotification::class, 1);
    }

    public function test_notification_delivery_is_recorded_and_duplicate_sends_are_skipped(): void
    {
        Mail::fake();
        [$organization, $user] = $this->organizationWithUser();
        $monitor = Monitor::factory()->for($organization)->create();
        $incident = Incident::factory()->for($monitor)->create();
        $job = new SendIncidentNotification($incident->id, $user->id, 'opened');

        $job->handle();
        $job->handle();

        Mail::assertSent(IncidentNotificationMail::class, 1);
        $this->assertDatabaseHas('notification_deliveries', ['incident_id' => $incident->id, 'user_id' => $user->id, 'event' => 'opened', 'status' => 'sent']);
    }

    /** @return array{Organization, User} */
    private function organizationWithUser(): array
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();
        $organization->users()->attach($user, ['role' => 'owner']);

        return [$organization, $user];
    }
}
