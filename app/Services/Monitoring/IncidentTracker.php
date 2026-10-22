<?php

namespace App\Services\Monitoring;

use App\Models\Monitor;
use App\Models\MonitorCheck;
use Illuminate\Support\Facades\DB;

class IncidentTracker
{
    public function process(MonitorCheck $check): void
    {
        DB::transaction(function () use ($check): void {
            $monitor = Monitor::query()->lockForUpdate()->findOrFail($check->monitor_id);
            $latestCheck = $monitor->checks()->latest('checked_at')->latest('id')->first();

            if (! $latestCheck || $latestCheck->isNot($check)) {
                return;
            }

            $incident = $monitor->incidents()->where('status', 'open')->latest('started_at')->first();

            if ($check->status === 'up') {
                if ($incident && $this->healthyStreak($monitor) >= config('monitoring.recovery_threshold')) {
                    $resolvedAt = $check->checked_at;
                    $incident->update([
                        'status' => 'resolved',
                        'resolved_at' => $resolvedAt,
                        'duration_seconds' => max(0, $incident->started_at->diffInSeconds($resolvedAt)),
                    ]);
                }

                return;
            }

            $failureStreak = $this->failureStreak($monitor);
            if ($incident) {
                $incident->update(['failure_count' => $failureStreak, 'last_error' => $check->error_message]);
            } elseif ($failureStreak >= config('monitoring.failure_threshold')) {
                $monitor->incidents()->create([
                    'status' => 'open',
                    'failure_count' => $failureStreak,
                    'last_error' => $check->error_message,
                    'started_at' => $check->checked_at,
                ]);
            }
        });
    }

    private function failureStreak(Monitor $monitor): int
    {
        $streak = 0;
        foreach ($monitor->checks()->latest('checked_at')->latest('id')->get() as $check) {
            if ($check->status === 'up') {
                break;
            }
            $streak++;
        }

        return $streak;
    }

    private function healthyStreak(Monitor $monitor): int
    {
        $streak = 0;
        foreach ($monitor->checks()->latest('checked_at')->latest('id')->get() as $check) {
            if ($check->status !== 'up') {
                break;
            }
            $streak++;
        }

        return $streak;
    }
}
