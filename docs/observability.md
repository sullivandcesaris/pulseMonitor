# Observability

PulseMonitor uses structured JSON logs, request correlation IDs, Laravel Horizon,
Sentry, Prometheus-compatible metrics, and Grafana dashboard definitions.

## Local configuration

- LOG_STACK=structured writes JSON logs to stderr.
- Every web response receives an X-Correlation-ID header. Incoming IDs are
  preserved so logs can be connected across services.
- SENTRY_LARAVEL_DSN enables Sentry when configured; an empty DSN disables it.
- PROMETHEUS_ENABLED=true enables the /metrics endpoint.
- /health checks PostgreSQL and Redis and returns HTTP 503 when either dependency
  is unavailable.

The Horizon dashboard is available at /horizon when Horizon is running. Its
configuration separates the monitoring and notification supervisors and records
queue wait thresholds, completed jobs, and failed jobs.

Prometheus should scrape /metrics. The dashboard definition in
infra/grafana/pulsemonitor-dashboard.json tracks pending queue jobs, open incidents,
and completed checks. Operational alerts should fire when health checks fail, queue
depth grows continuously, or failed jobs exceed the agreed threshold.
