<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationalReliabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_reports_database_and_redis_status(): void
    {
        $response = $this->getJson(route('health'));

        $response->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('checks.database', true)
            ->assertJsonPath('checks.redis', true);
    }

    public function test_responses_include_a_correlation_id(): void
    {
        $response = $this->withHeader('X-Correlation-ID', 'test-correlation-id')->get('/');

        $response->assertOk()->assertHeader('X-Correlation-ID', 'test-correlation-id');
    }

    public function test_prometheus_metrics_are_disabled_by_default(): void
    {
        $this->get(route('metrics'))->assertNotFound();
    }

    public function test_prometheus_metrics_can_be_enabled_for_scraping(): void
    {
        config(['observability.prometheus_enabled' => true]);

        $this->get(route('metrics'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; version=0.0.4; charset=utf-8')
            ->assertSee('pulsemonitor_monitors_total');
    }
}
