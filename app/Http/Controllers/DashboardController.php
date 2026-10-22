<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Monitor;
use App\Models\MonitorCheck;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $organizationIds = $request->user()->organizations()->pluck('organizations.id');
        $periodStart = now()->subDay();
        $monitors = Monitor::query()
            ->whereIn('organization_id', $organizationIds)
            ->with('latestCheck')
            ->orderBy('name')
            ->get();
        $checks = MonitorCheck::query()
            ->whereIn('monitor_id', $monitors->modelKeys())
            ->where('checked_at', '>=', $periodStart)
            ->latest('checked_at')
            ->with('monitor:id,name,target_url')
            ->get();
        $checksWithResponseTime = $checks->whereNotNull('response_time_ms');
        $totalChecks = $checks->count();
        $upChecks = $checks->where('status', 'up')->count();

        return Inertia::render('Dashboard', [
            'period' => ['label' => 'Last 24 hours', 'since' => $periodStart->toIso8601String()],
            'stats' => [
                'activeMonitors' => $monitors->where('is_enabled', true)->count(),
                'totalMonitors' => $monitors->count(),
                'healthyMonitors' => $monitors->filter(fn (Monitor $monitor) => $monitor->latestCheck?->status === 'up')->count(),
                'openIncidents' => Incident::query()
                    ->whereIn('monitor_id', $monitors->modelKeys())
                    ->where('status', 'open')
                    ->count(),
                'averageResponseTime' => $checksWithResponseTime->avg('response_time_ms'),
                'uptimePercentage' => $totalChecks > 0 ? round(($upChecks / $totalChecks) * 100, 2) : null,
            ],
            'recentChecks' => $checks->take(10)->map(fn (MonitorCheck $check) => [
                'id' => $check->id,
                'name' => $check->monitor?->name,
                'url' => $check->monitor?->target_url,
                'status' => $check->status,
                'httpStatus' => $check->http_status,
                'responseTime' => $check->response_time_ms,
                'checkedAt' => $check->checked_at?->toIso8601String(),
            ])->values(),
            'responseTimeHistory' => $checksWithResponseTime->take(20)->reverse()->values()->map(fn (MonitorCheck $check) => [
                'checkedAt' => $check->checked_at?->toIso8601String(),
                'responseTime' => $check->response_time_ms,
            ]),
        ]);
    }
}
