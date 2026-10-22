<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Services\Monitoring\MonitorChecker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class CheckMonitor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(public Monitor $monitor) {}

    public function handle(MonitorChecker $checker): void
    {
        $checker->check($this->monitor->fresh());
    }

    /** @return array<int, object> */
    public function middleware(): array
    {
        return [new WithoutOverlapping("monitor:{$this->monitor->id}")];
    }
}
