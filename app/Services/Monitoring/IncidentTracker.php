<?php

namespace App\Services\Monitoring;

use App\Jobs\SendIncidentNotification;
use App\Models\Monitor;
use App\Models\MonitorCheck;
use Illuminate\Support\Facades\DB;

class IncidentTracker
{
    public function process(MonitorCheck $check): void
    {
        $notificationEvent = null;
        $incidentId = null;

        DB::transaction(function () use ($check, &$notificationEvent, &$incidentId): void {
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
                    $notificationEvent = 'recovered';
                    $incidentId = $incident->id;
                }

                return;
            }

            $failureStreak = $this->failureStreak($monitor);
            if ($incident) {
                $incident->update(['failure_count' => $failureStreak, 'last_error' => $check->error_message]);
            } elseif ($failureStreak >= config('monitoring.failure_threshold')) {
                $incident = $monitor->incidents()->create([
                    'status' => 'open',
                    'failure_count' => $failureStreak,
                    'last_error' => $check->error_message,
                    'started_at' => $check->checked_at,
                ]);
                $notificationEvent = 'opened';
                $incidentId = $incident->id;
            }
        });

        if ($notificationEvent && $incidentId) {
            $monitor = Monitor::with('organization.users')->findOrFail($check->monitor_id);
            foreach ($monitor->organization->users as $user) {
                if ($user->alert_notifications_enabled) {
                    SendIncidentNotification::dispatch($incidentId, $user->id, $notificationEvent)->afterCommit();
                }
            }
        }
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
