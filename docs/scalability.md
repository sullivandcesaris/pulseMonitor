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
eager-load the latest check and monitor relations to avoid N+1 queries.

Long-term monitor-check retention remains a pending operational decision because
pruning history is destructive. The retention period, archival strategy, and
deletion safeguards should be approved before an automated cleanup command is added.
Detailed load benchmarks are planned after the representative 100, 1,000, and
10,000-monitor scenarios are added.
