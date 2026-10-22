# Operational Recovery Procedures

## Queue worker failure

1. Check worker status with docker compose ps queue notifications.
2. Inspect failed jobs with php artisan queue:failed.
3. Restart the affected worker with docker compose restart queue or
   docker compose restart notifications.
4. Retry only reviewed jobs with php artisan queue:retry ID.
5. Confirm queue depth and /health before closing the incident.

## Redis outage

1. Confirm the Redis health check and inspect docker compose logs redis.
2. Restore Redis before restarting workers; queued monitoring and notification jobs
   depend on Redis and should not be discarded.
3. Restart workers after Redis is healthy.
4. Run the scheduler dispatch command and verify new checks are being processed.

## PostgreSQL outage

1. Confirm /health reports the database failure.
2. Inspect PostgreSQL logs and storage/connection capacity.
3. Restore the database and verify migrations/status before restarting workers.
4. Check failed jobs and incident history for gaps caused by the outage.

## Error-handling strategy

Infrastructure connection failures are retried by monitor jobs with bounded
backoff. The final attempt records a failed check and incident state. Notification
failures are persisted in notification deliveries and retried by the notification
queue. Application exceptions are sent to Sentry when a DSN is configured and are
correlated with request or job IDs in structured logs.
