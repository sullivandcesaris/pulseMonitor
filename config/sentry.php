<?php

return [
    'dsn' => env('SENTRY_LARAVEL_DSN'),
    'environment' => env('SENTRY_ENVIRONMENT', env('APP_ENV')),
    'traces_sample_rate' => (float) env('SENTRY_TRACES_SAMPLE_RATE', 0),
    'send_default_pii' => false,
];
