<?php

use Illuminate\Support\Str;

return [
    'name' => env('HORIZON_NAME', env('APP_NAME', 'PulseMonitor')),
    'domain' => env('HORIZON_DOMAIN'),
    'path' => env('HORIZON_PATH', 'horizon'),
    'use' => 'default',
    'prefix' => env('HORIZON_PREFIX', Str::slug(env('APP_NAME', 'pulsemonitor'), '_').'_horizon:'),
    'middleware' => ['web'],
    'waits' => [
        'redis:monitoring' => 60,
        'redis:notifications' => 60,
    ],
    'trim' => [
        'recent' => 60,
        'pending' => 60,
        'completed' => 60,
        'recent_failed' => 10080,
        'failed' => 10080,
        'monitored' => 10080,
    ],
    'metrics' => ['trim_snapshots' => ['job' => 24, 'queue' => 24]],
    'fast_termination' => false,
    'memory_limit' => 64,
    'defaults' => [
        'supervisor-monitoring' => [
            'connection' => 'redis',
            'queue' => ['monitoring'],
            'balance' => 'auto',
            'autoScalingStrategy' => 'time',
            'maxProcesses' => 1,
            'maxTime' => 0,
            'maxJobs' => 0,
            'memory' => 128,
            'tries' => 3,
            'timeout' => 120,
            'nice' => 0,
        ],
        'supervisor-notifications' => [
            'connection' => 'redis',
            'queue' => ['notifications'],
            'balance' => 'auto',
            'autoScalingStrategy' => 'time',
            'maxProcesses' => 1,
            'maxTime' => 0,
            'maxJobs' => 0,
            'memory' => 128,
            'tries' => 3,
            'timeout' => 120,
            'nice' => 0,
        ],
    ],
    'environments' => [
        'production' => [
            'supervisor-monitoring' => ['maxProcesses' => 10, 'balanceMaxShift' => 1, 'balanceCooldown' => 3],
            'supervisor-notifications' => ['maxProcesses' => 3],
        ],
        'local' => [
            'supervisor-monitoring' => ['maxProcesses' => 3],
            'supervisor-notifications' => ['maxProcesses' => 1],
        ],
    ],
    'silenced' => [],
    'silenced_tags' => [],
    'watch' => ['app', 'bootstrap', 'config', 'database', 'resources', 'routes', 'composer.lock', 'composer.json', '.env'],
];
