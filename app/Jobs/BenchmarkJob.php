<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class BenchmarkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $runId, public float $enqueuedAt)
    {
        $this->onQueue('benchmark');
    }

    public function handle(): void
    {
        $key = "pulsemonitor:benchmark:{$this->runId}";
        Redis::pipeline(function ($pipe) use ($key): void {
            $pipe->incr("{$key}:processed");
            $pipe->rpush("{$key}:waits", (string) round((microtime(true) - $this->enqueuedAt) * 1000, 2));
        });
    }
}
