<?php

namespace App\Jobs;

use App\Exceptions\RetryableMonitorCheckException;
use App\Models\Monitor;
use App\Services\Monitoring\MonitorChecker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Throwable;

class CheckMonitor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public Monitor $monitor)
    {
        $this->onQueue('monitoring');
    }

    public function handle(MonitorChecker $checker): void
    {
        $checker->check($this->monitor->fresh(), config('monitoring.retry_infrastructure_failures'));
    }

    /** @return array<int, int> */
    public function backoff(): array
    {
        return [10, 60];
    }

    /** @return array<int, object> */
    public function middleware(): array
    {
        return [new WithoutOverlapping("monitor:{$this->monitor->id}")];
    }

    public function failed(?Throwable $exception): void
    {
        if (! $exception instanceof RetryableMonitorCheckException) {
            return;
        }

        app(MonitorChecker::class)->recordFailure($this->monitor->fresh(), $exception->getMessage());
    }
}
