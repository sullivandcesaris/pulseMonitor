# PulseMonitor

PulseMonitor is a service-monitoring SaaS for teams that need a clear, reliable view of the availability and response time of their websites and HTTP APIs.

It lets an organization register monitoring targets, execute scheduled HTTP checks, persist operational history, detect incidents after consecutive failures, notify users, and inspect service health through a responsive dashboard.

## About

PulseMonitor was designed as a reliability-focused portfolio project and as a foundation for a small production monitoring product. The project emphasizes practical engineering concerns instead of only presenting a dashboard:

- Organization-scoped access control and monitor ownership.
- Asynchronous monitoring through queues and dedicated workers.
- SSRF-aware outbound request validation.
- Idempotent incident creation and recovery.
- Operational visibility through logs, metrics, Horizon, Sentry, and Grafana.
- Automated tests, benchmarks, production Compose artifacts, and recovery documentation.

The application currently targets small engineering teams, SaaS companies, agencies, and platform teams that need lightweight HTTP monitoring without the complexity of a full synthetic-monitoring platform.

## Current capabilities

### Identity and organizations

- Registration, authentication, email verification, password reset, and profile settings.
- Organization membership with organization-scoped monitor and incident access.
- Authorization policies that prevent cross-organization data exposure.

### Monitoring

- GET and HEAD checks over HTTP and HTTPS.
- Configurable interval, timeout, expected status code, and enabled state.
- Response-time and HTTP-status recording.
- Connection, timeout, invalid-target, and unexpected-response handling.
- Scheduled asynchronous execution through Redis queues.
- Separate monitoring and notification queues for independent scaling.

### Incidents and notifications

- Configurable consecutive-failure threshold before opening an incident.
- Idempotent incident transitions and protection against duplicate open incidents.
- Recovery confirmation threshold and incident duration tracking.
- Queued email notifications for incident start and recovery.
- Notification delivery records for traceability and retry handling.

### Dashboard and operations

- Current monitor health, availability, open incidents, and average response time.
- Period filters for the last 6 hours, 24 hours, or 7 days.
- Recent checks with a compact scrollable view and a “View all” dialog filtered by monitor.
- Response-time history chart powered by Apache ECharts.
- Incident history with pagination.
- Health and metrics endpoints, structured logs, correlation IDs, Horizon, Sentry, Prometheus, and Grafana definitions.

## Technology stack

| Area | Technology |
| --- | --- |
| Backend | PHP 8.5, Laravel 12 |
| Web delivery | Inertia.js 2, Laravel routing and server-side authorization |
| Frontend | Vue 3, TypeScript, Vite |
| UI | Tailwind CSS 3, shadcn-vue-style components, Radix Vue |
| Charts | Apache ECharts through `vue-echarts` |
| Database | PostgreSQL |
| Queue and cache | Redis, Laravel Queue, Laravel Horizon |
| Email | Laravel Mail with queued notification jobs |
| Runtime | Docker, Docker Compose, Laravel Sail |
| Observability | Laravel logs, Sentry, Prometheus, Grafana |
| Testing | PHPUnit 11, Laravel feature tests, factories, RefreshDatabase |
| Code quality | Laravel Pint, ESLint, Prettier, Vue type checking |

## Architecture

PulseMonitor is a modular monolith. Laravel owns routing, authentication, authorization, persistence, scheduling, and application services. Vue pages receive server-provided props through Inertia, avoiding a separate API layer for the main web experience.

```text
User
  │
  ▼
Laravel + Inertia + Vue
  │              │
  ▼              ▼
PostgreSQL     Redis
  │              │
  │       ┌──────┴────────┐
  │       ▼               ▼
  │  Monitoring       Notifications
  │    workers          workers
  │       │
  └───────┴──> Customer HTTP/HTTPS endpoints
```

The main domain flow is:

1. A user creates a monitor inside an organization.
2. The scheduler finds enabled monitors whose next check is due.
3. A queued worker validates the destination and performs the HTTP request.
4. The result is stored as a `MonitorCheck`.
5. `IncidentTracker` applies failure and recovery thresholds.
6. Incident transitions dispatch queued notification jobs.
7. The dashboard reads organization-scoped monitors, checks, incidents, and metrics.

Important source areas:

```text
app/
├── Http/Controllers/       web endpoints and Inertia responses
├── Jobs/                   monitoring and notification queue jobs
├── Models/                 users, organizations, monitors, checks, incidents
├── Services/Monitoring/    HTTP checking and incident state transitions
└── Policies/               organization-scoped authorization

resources/js/
├── components/             reusable UI and shadcn-vue primitives
├── layouts/                authenticated and settings shells
└── pages/                  route-level Inertia Vue pages
```

More detailed decisions are documented in:

- [Architecture](docs/architecture.md)
- [Domain model](docs/domain-model.md)
- [Frontend architecture](docs/frontend.md)
- [Charting decision](docs/charting-decision.md)
- [Deployment runbook](docs/deployment.md)
- [Observability](docs/observability.md)
- [Security](docs/security.md)
- [Scalability](docs/scalability.md)
- [Recovery procedures](docs/operations/recovery.md)

## Local development

### Prerequisites

- PHP 8.5 or a compatible PHP 8.2+ runtime.
- Composer.
- Node.js and npm.
- Docker Desktop with Compose support.

### Laravel Sail

Laravel Sail is the supported local environment. It runs the application, PostgreSQL, and Redis through Docker Compose.

```bash
cp .env.example .env
composer install
php artisan key:generate
./vendor/bin/sail build
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

The application is available at `http://localhost:8000`. Run the queue worker and scheduler in separate terminals when using the basic Sail workflow:

```bash
./vendor/bin/sail artisan queue:work
./vendor/bin/sail artisan schedule:work
```

Stop the environment with:

```bash
./vendor/bin/sail down
```

For the production-like Compose setup, see [docs/deployment.md](docs/deployment.md) and the files under `infra/production/`.

## Useful commands

```bash
# Backend
php artisan migrate --seed
php artisan route:list
php artisan queue:work
php artisan schedule:work
php artisan horizon

# Frontend
npm run dev
npm run build
npm run format:check
npm run lint
npm exec vue-tsc -- --noEmit

# Tests and PHP formatting
php artisan test --compact
vendor/bin/pint --dirty --format agent
```

The Composer `dev` script starts the HTTP server, queue listener, log viewer, and Vite together:

```bash
composer run dev
```

## Testing and quality

The test suite uses PHPUnit and Laravel's feature-testing tools. Coverage currently includes authentication, monitor management, organization isolation, monitoring execution, incident detection and recovery, notifications, operational reliability, and dashboard behavior.

Before opening a pull request, run the checks relevant to the change:

```bash
php artisan test --compact
vendor/bin/pint --dirty --format agent
npm run format:check
npm run build
npm exec vue-tsc -- --noEmit
```

The repository also contains benchmark support for 100, 1,000, and 10,000-monitor scenarios:

```bash
php artisan monitoring:benchmark --sizes=100,1000,10000 --workers=4
```

## Security model

Monitoring creates outbound requests from user-controlled URLs, so target validation is treated as a security boundary. The monitoring service must validate the destination immediately before execution and:

- Allow only HTTP and HTTPS schemes.
- Reject loopback, private, link-local, multicast, reserved, and cloud metadata addresses.
- Validate DNS resolutions and every redirect destination.
- Restrict outbound ports and enforce connection and total request timeouts.
- Limit request frequency and response size.
- Avoid forwarding customer credentials or internal headers.
- Record safe diagnostic information without storing secrets or response bodies unnecessarily.

Frontend visibility is not considered a security control; organization authorization remains on the Laravel side.

## Project status

The foundation and monitoring MVP are implemented. Weeks 1–7 of the roadmap are complete, and Week 8 production artifacts are prepared, including the release image, production Compose services, Caddy configuration, worker separation, environment guidance, and backup/recovery procedures.

The application is suitable for local demonstration and continued product development. A public production launch still requires environment-specific work such as deployment credentials, DNS, SMTP configuration, backups, alert routing, and a final security review.

## Suggested improvements

The following improvements are intentionally ordered by product and operational impact:

### High priority

1. **Deploy a public staging environment** with HTTPS, SMTP, Redis, PostgreSQL backups, and a monitored health endpoint. This validates the production artifacts against a real runtime.
2. **Add end-to-end browser tests** for registration, monitor creation, dashboard filtering, incident history, and the “View all checks” flow.
3. **Improve dashboard scalability** by paginating or lazily loading long check histories instead of sending every check in the selected period to the initial page.
4. **Add notification preferences** per organization and user, including email verification, per-monitor subscriptions, and notification quiet hours.
5. **Complete the security review** with dependency auditing, rate-limit verification, secret scanning, redirect edge cases, and an external SSRF assessment.

### Medium priority

6. **Add incident acknowledgment and maintenance windows** so teams can suppress expected alerts while preserving the operational history.
7. **Add monitor labels, tags, and search** to make larger monitor collections easier to operate.
8. **Add retention policies and rollups** for high-volume check history, preserving detailed recent data while aggregating older measurements.
9. **Add webhook and chat integrations** for Slack, Teams, or generic webhooks with delivery status and replay support.
10. **Improve observability dashboards** with queue latency, check latency percentiles, failure-rate alerts, worker saturation, and notification delivery health.

### Longer term

11. **Add multi-region monitoring** with region-aware results and quorum-based incident decisions.
12. **Add browser and synthetic transaction checks** for login flows, checkout paths, and JavaScript-heavy applications.
13. **Introduce usage limits and billing** based on monitor count, check frequency, retention, and notification channels.
14. **Extract high-volume monitoring execution** into a dedicated service only after queue and database scaling limits are demonstrated in production.

## License

License to be decided before the first public release.
