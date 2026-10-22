<?php

namespace Database\Seeders;

use App\Models\Monitor;
use App\Models\Organization;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(['email' => 'test@example.com'], [
            'name' => 'Test User',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        $organization = Organization::firstOrCreate(
            ['slug' => 'acme-monitoring'],
            ['name' => 'Acme Monitoring'],
        );

        $organization->users()->syncWithoutDetaching([$user->id => ['role' => 'owner']]);

        Monitor::updateOrCreate(
            ['organization_id' => $organization->id, 'name' => 'PulseMonitor Homepage'],
            [
                'target_url' => 'http://localhost:8000',
                'method' => 'GET',
                'interval_seconds' => 60,
                'expected_status' => 200,
                'timeout_seconds' => 10,
                'is_enabled' => true,
            ],
        );
    }
}
