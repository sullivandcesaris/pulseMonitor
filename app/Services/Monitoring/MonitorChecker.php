<?php

namespace App\Services\Monitoring;

use App\Models\Monitor;
use App\Models\MonitorCheck;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Throwable;

class MonitorChecker
{
    public function check(Monitor $monitor): MonitorCheck
    {
        $startedAt = hrtime(true);
        $status = 'down';
        $httpStatus = null;
        $errorMessage = null;

        try {
            $url = $this->validateDestination($monitor->target_url);
            $response = Http::connectTimeout(config('monitoring.connect_timeout'))
                ->timeout($monitor->timeout_seconds)
                ->withOptions(['allow_redirects' => config('monitoring.allow_redirects')])
                ->send($monitor->method, $url);
            $httpStatus = $response->status();
            $status = $httpStatus === $monitor->expected_status ? 'up' : 'unexpected_status';
            $errorMessage = $status === 'up' ? null : "Expected HTTP {$monitor->expected_status}, received HTTP {$httpStatus}.";
        } catch (ConnectionException $exception) {
            $errorMessage = 'Connection failed or timed out.';
        } catch (Throwable $exception) {
            $errorMessage = $exception->getMessage();
        }

        $checkedAt = now();
        $check = $monitor->checks()->create([
            'status' => $status,
            'http_status' => $httpStatus,
            'response_time_ms' => (int) round((hrtime(true) - $startedAt) / 1_000_000),
            'error_message' => $errorMessage,
            'checked_at' => $checkedAt,
        ]);
        $monitor->update(['last_checked_at' => $checkedAt, 'next_check_at' => $checkedAt->copy()->addSeconds($monitor->interval_seconds)]);

        return $check;
    }

    private function validateDestination(string $targetUrl): string
    {
        $parts = parse_url($targetUrl);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower($parts['host'] ?? '');

        if (! in_array($scheme, ['http', 'https'], true) || $host === '' || isset($parts['user'], $parts['pass'])) {
            throw new InvalidArgumentException('Monitor targets must be HTTP or HTTPS URLs without credentials.');
        }
        if ($host === 'localhost' || str_ends_with($host, '.localhost') || $host === 'metadata.google.internal') {
            throw new InvalidArgumentException('Private and metadata destinations are not allowed.');
        }

        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);
        if (! in_array($port, config('monitoring.allowed_ports'), true)) {
            throw new InvalidArgumentException('Monitor targets may only use approved HTTP or HTTPS ports.');
        }

        $addresses = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : $this->resolveHost($host);
        foreach ($addresses as $address) {
            if (! filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                throw new InvalidArgumentException('Private, loopback, link-local, and reserved destinations are not allowed.');
            }
        }

        return $targetUrl;
    }

    /** @return list<string> */
    private function resolveHost(string $host): array
    {
        $records = dns_get_record($host, DNS_A | DNS_AAAA);
        $addresses = array_values(array_filter(array_map(fn (array $record): ?string => $record['ip'] ?? $record['ipv6'] ?? null, $records ?: [])));
        if ($addresses === []) {
            throw new InvalidArgumentException('Monitor target hostname could not be resolved.');
        }

        return $addresses;
    }
}
