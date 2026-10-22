<?php

namespace Database\Factories;

use App\Models\Monitor;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Monitor> */
class MonitorFactory extends Factory
{
    protected $model = Monitor::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->words(2, true),
            'target_url' => fake()->url(),
            'method' => 'GET',
            'interval_seconds' => 60,
            'expected_status' => 200,
            'timeout_seconds' => 10,
            'is_enabled' => true,
            'last_checked_at' => null,
            'next_check_at' => now()->addMinute(),
        ];
    }
}
