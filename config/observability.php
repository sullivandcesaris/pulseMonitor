<?php

return [
    'prometheus_enabled' => (bool) env('PROMETHEUS_ENABLED', false),
    'correlation_header' => 'X-Correlation-ID',
];
