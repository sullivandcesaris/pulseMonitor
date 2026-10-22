<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CorrelationId
{
    public function handle(Request $request, Closure $next): Response
    {
        $correlationId = $request->header('X-Correlation-ID') ?: (string) Str::uuid();
        $startedAt = microtime(true);
        Log::withContext(['correlation_id' => $correlationId]);
        $response = $next($request);
        $response->headers->set('X-Correlation-ID', $correlationId);
        Log::info('http.request.completed', [
            'method' => $request->method(),
            'path' => $request->path(),
            'status' => $response->getStatusCode(),
            'duration_ms' => round((microtime(true) - $startedAt) * 1000, 2),
        ]);
        try {
            Redis::incr('pulsemonitor:metrics:http_requests_total');
            Redis::set('pulsemonitor:metrics:http_request_duration_ms_last', round((microtime(true) - $startedAt) * 1000, 2));
        } catch (\Throwable) {
            // Metrics must not make an otherwise healthy request fail.
        }

        return $response;
    }
}
