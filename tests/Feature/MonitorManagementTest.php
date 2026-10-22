<?php

namespace Tests\Feature;

use App\Models\Monitor;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitorManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_monitors_from_their_organizations(): void
    {
        [$user, $organization] = $this->monitorUser();
        $monitor = Monitor::factory()->for($organization)->create();

        $response = $this->actingAs($user)->get(route('monitors.index'));

        $response->assertOk();
        $response->assertSee($monitor->name);
    }

    public function test_user_can_create_a_monitor_for_their_organization(): void
    {
        [$user, $organization] = $this->monitorUser();

        $response = $this->actingAs($user)->post(route('monitors.store'), [
            'name' => 'Public API', 'target_url' => 'https://example.com/health', 'method' => 'GET',
            'interval_seconds' => 60, 'expected_status' => 200, 'timeout_seconds' => 10, 'is_enabled' => true,
        ]);

        $response->assertRedirect(route('monitors.index'));
        $this->assertDatabaseHas('monitors', ['organization_id' => $organization->id, 'name' => 'Public API']);
    }

    public function test_user_cannot_access_another_organizations_monitor(): void
    {
        [$user] = $this->monitorUser();
        $monitor = Monitor::factory()->create();

        $this->actingAs($user)->get(route('monitors.edit', $monitor))->assertForbidden();
    }

    public function test_user_can_update_and_delete_an_owned_monitor(): void
    {
        [$user, $organization] = $this->monitorUser();
        $monitor = Monitor::factory()->for($organization)->create();

        $this->actingAs($user)->put(route('monitors.update', $monitor), [
            'name' => 'Updated monitor', 'target_url' => 'https://example.com/status', 'method' => 'HEAD',
            'interval_seconds' => 300, 'expected_status' => 204, 'timeout_seconds' => 15, 'is_enabled' => false,
        ])->assertRedirect(route('monitors.index'));

        $this->assertDatabaseHas('monitors', ['id' => $monitor->id, 'name' => 'Updated monitor', 'method' => 'HEAD']);

        $this->actingAs($user)->delete(route('monitors.destroy', $monitor))->assertRedirect(route('monitors.index'));
        $this->assertDatabaseMissing('monitors', ['id' => $monitor->id]);
    }

    /** @return array{User, Organization} */
    private function monitorUser(): array
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $organization->users()->attach($user, ['role' => 'owner']);

        return [$user, $organization];
    }
}
