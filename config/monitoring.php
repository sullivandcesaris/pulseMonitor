<?php

return [
    'allowed_ports' => array_map('intval', explode(',', env('MONITOR_ALLOWED_PORTS', '80,443'))),
    'connect_timeout' => (int) env('MONITOR_CONNECT_TIMEOUT', 10),
    'allow_redirects' => false,
    'failure_threshold' => (int) env('MONITOR_FAILURE_THRESHOLD', 3),
    'recovery_threshold' => (int) env('MONITOR_RECOVERY_THRESHOLD', 1),
    'retry_infrastructure_failures' => (bool) env('MONITOR_RETRY_INFRASTRUCTURE_FAILURES', true),
];
