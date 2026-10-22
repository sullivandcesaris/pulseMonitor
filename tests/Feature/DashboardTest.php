<?php

namespace Tests\Feature;

use App\Models\Incident;
use App\Models\Monitor;
use App\Models\MonitorCheck;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_data_only_for_the_users_organizations(): void
    {
        [$user, $organization] = $this->dashboardUser();
        $monitor = Monitor::factory()->for($organization)->create(['name' => 'Owned API']);
        MonitorCheck::factory()->for($monitor)->create(['status' => 'up', 'response_time_ms' => 120]);
        Monitor::factory()->create(['name' => 'Other organization API']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Owned API');
        $response->assertDontSee('Other organization API');
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard', false)
            ->where('stats.uptimePercentage', 100)
            ->where('stats.averageResponseTime', 120)
            ->where('recentChecks.0.name', 'Owned API'));
    }

    public function test_dashboard_displays_open_incidents_and_unknown_availability_without_checks(): void
    {
        [$user, $organization] = $this->dashboardUser();
        $monitor = Monitor::factory()->for($organization)->create();
        Incident::factory()->for($monitor)->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard', false)
            ->where('stats.uptimePercentage', null)
            ->where('stats.openIncidents', 1));
    }

    public function test_incident_history_is_scoped_to_the_users_organizations(): void
    {
        [$user, $organization] = $this->dashboardUser();
        $ownedMonitor = Monitor::factory()->for($organization)->create(['name' => 'Owned monitor']);
        Incident::factory()->for($ownedMonitor)->create(['last_error' => 'Owned outage']);
        $otherMonitor = Monitor::factory()->create(['name' => 'Private monitor']);
        Incident::factory()->for($otherMonitor)->create(['last_error' => 'Private outage']);

        $response = $this->actingAs($user)->get(route('incidents.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Incidents/Index', false)
            ->has('incidents', 1)
            ->where('incidents.0.lastError', 'Owned outage'));
    }

    /** @return array{User, Organization} */
    private function dashboardUser(): array
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $organization->users()->attach($user, ['role' => 'owner']);

        return [$user, $organization];
    }
}
