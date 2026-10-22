<?php

namespace Database\Factories;

use App\Models\Incident;
use App\Models\Monitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Incident> */
class IncidentFactory extends Factory
{
    protected $model = Incident::class;

    public function definition(): array
    {
        return [
            'monitor_id' => Monitor::factory(),
            'status' => 'open',
            'failure_count' => 1,
            'last_error' => fake()->sentence(),
            'started_at' => now(),
            'resolved_at' => null,
        ];
    }
}
