<?php

return [
    'allowed_ports' => array_map('intval', explode(',', env('MONITOR_ALLOWED_PORTS', '80,443'))),
    'connect_timeout' => (int) env('MONITOR_CONNECT_TIMEOUT', 10),
    'allow_redirects' => false,
];
