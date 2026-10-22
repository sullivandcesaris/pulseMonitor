# Scalability Notes

## Queue topology

Monitoring checks and notification delivery use separate Redis queues:

- The monitoring queue handles HTTP checks and is served by the queue container.
- The notifications queue handles email delivery and is served by the notifications container.

This prevents a slow mail provider from delaying service checks. Each monitor also uses
WithoutOverlapping, so concurrent scheduler runs cannot execute the same monitor at
the same time.

## Scheduling and backpressure

The monitors:dispatch-due command claims a due monitor by atomically moving its
next_check_at forward before dispatching work. This prevents repeated scheduler
dispatches while a check is waiting in Redis. The monitor interval controls the
maximum scheduling rate; workers can be scaled independently when queue depth grows.

## Database strategy

The due-monitor query is supported by the composite is_enabled, next_check_at index.
Historical incident results are paginated at 20 records per page. Dashboard queries
eager-load the latest check and monitor relations to avoid N+1 queries. Live dashboard
data is intentionally not cached because freshness is more important than reducing
already indexed queries.

The proposed retention policy is 90 days for raw monitor checks and indefinite
retention for incidents and delivery records. An archival/export step and explicit
approval are required before any destructive cleanup command is added.

## Benchmark results

The monitoring:benchmark command creates synthetic monitor rows inside a
transaction and rolls them back. Queue measurements use temporary benchmark jobs.

| Scenario | DB query | CPU | Peak memory | Queue throughput | Queue wait p95 |
| --- | ---: | ---: | ---: | ---: | ---: |
| 100 monitors | 2.04 ms | 22.57 ms | 30 MB | 28.80 jobs/s | 410.61 ms |
| 1,000 monitors | 0.94 ms | 28.69 ms | 34 MB | 8.01 jobs/s | 121,502.19 ms |
| 10,000 monitors | 1.80 ms | 257.96 ms | 44 MB | database-only run | database-only run |

The 1,000-monitor queue result shows that a single worker becomes the bottleneck as
queue depth grows. The monitoring service was verified with two independent Docker
workers; further throughput tuning should compare worker counts and queue wait time.
