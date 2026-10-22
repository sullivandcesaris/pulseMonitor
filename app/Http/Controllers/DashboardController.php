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
        $periodKey = $request->string('period')->toString();
        $periodHours = match ($periodKey) {
            '6h' => 6,
            '7d' => 24 * 7,
            default => 24,
        };
        $selectedMonitorId = $request->integer('monitor_id') ?: null;
        $monitors = Monitor::query()
            ->whereIn('organization_id', $organizationIds)
            ->when($selectedMonitorId, fn ($query) => $query->whereKey($selectedMonitorId))
            ->with('latestCheck')
            ->orderBy('name')
            ->get();
        $periodStart = now()->subHours($periodHours);
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
            'period' => ['key' => $periodHours === 6 ? '6h' : ($periodHours === 24 ? '24h' : '7d'), 'label' => $periodHours === 6 ? 'Last 6 hours' : ($periodHours === 24 ? 'Last 24 hours' : 'Last 7 days'), 'since' => $periodStart->toIso8601String()],
            'monitors' => $monitors->map(fn (Monitor $monitor) => ['id' => $monitor->id, 'name' => $monitor->name])->values(),
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
            'coverage' => [
                'checks' => $totalChecks,
                'expectedChecks' => $monitors->sum(fn (Monitor $monitor) => max(1, (int) ceil($periodStart->diffInSeconds(now()) / $monitor->interval_seconds))),
                'hasData' => $totalChecks > 0,
            ],
        ]);
    }
}
