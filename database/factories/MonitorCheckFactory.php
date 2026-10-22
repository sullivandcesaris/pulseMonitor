<?php

namespace Database\Factories;

use App\Models\Monitor;
use App\Models\MonitorCheck;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MonitorCheck> */
class MonitorCheckFactory extends Factory
{
    protected $model = MonitorCheck::class;

    public function definition(): array
    {
        return [
            'monitor_id' => Monitor::factory(),
            'status' => 'up',
            'http_status' => 200,
            'response_time_ms' => fake()->numberBetween(20, 500),
            'error_message' => null,
            'checked_at' => now(),
        ];
    }
}
