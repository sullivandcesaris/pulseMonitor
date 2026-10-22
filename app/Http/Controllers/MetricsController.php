<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Monitor;
use App\Models\MonitorCheck;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class MetricsController extends Controller
{
    public function __invoke(): Response
    {
        abort_unless(config('observability.prometheus_enabled'), 404);

        $lines = [
            '# HELP pulsemonitor_monitors_total Registered monitors.',
            '# TYPE pulsemonitor_monitors_total gauge',
            'pulsemonitor_monitors_total '.Monitor::count(),
            '# HELP pulsemonitor_monitors_enabled Enabled monitors.',
            '# TYPE pulsemonitor_monitors_enabled gauge',
            'pulsemonitor_monitors_enabled '.Monitor::where('is_enabled', true)->count(),
            '# HELP pulsemonitor_incidents_open Open incidents.',
            '# TYPE pulsemonitor_incidents_open gauge',
            'pulsemonitor_incidents_open '.Incident::where('status', 'open')->count(),
            '# HELP pulsemonitor_checks_completed_total Checks completed in the last hour.',
            '# TYPE pulsemonitor_checks_completed_total gauge',
            'pulsemonitor_checks_completed_total '.MonitorCheck::where('checked_at', '>=', now()->subHour())->count(),
            '# HELP pulsemonitor_http_requests_total HTTP requests completed.',
            '# TYPE pulsemonitor_http_requests_total counter',
            'pulsemonitor_http_requests_total '.Redis::get('pulsemonitor:metrics:http_requests_total'),
            '# HELP pulsemonitor_http_request_duration_ms_last Duration of the latest HTTP request.',
            '# TYPE pulsemonitor_http_request_duration_ms_last gauge',
            'pulsemonitor_http_request_duration_ms_last '.Redis::get('pulsemonitor:metrics:http_request_duration_ms_last'),
            '# HELP pulsemonitor_failed_jobs_total Failed queued jobs.',
            '# TYPE pulsemonitor_failed_jobs_total gauge',
            'pulsemonitor_failed_jobs_total '.DB::table('failed_jobs')->count(),
            '# HELP pulsemonitor_queue_jobs_pending Pending Redis queue jobs.',
            '# TYPE pulsemonitor_queue_jobs_pending gauge',
            'pulsemonitor_queue_jobs_pending{queue="monitoring"} '.Redis::llen('queues:monitoring'),
            'pulsemonitor_queue_jobs_pending{queue="notifications"} '.Redis::llen('queues:notifications'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; version=0.0.4']);
    }
}
