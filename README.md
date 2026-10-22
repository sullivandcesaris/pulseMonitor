# PulseMonitor

PulseMonitor is a service-monitoring SaaS for teams that need to know when their websites and HTTP APIs become unavailable or slow.

## Purpose

PulseMonitor periodically sends safe, configurable HTTP requests to customer-owned endpoints, records the results, detects sustained failures, and notifies the organization when an incident starts or is resolved.

## Target users

- Small engineering teams that need lightweight uptime monitoring.
- SaaS teams that want an operational view of their public APIs.
- Agencies and platform teams managing monitors for multiple organizations.

## MVP scope

The first release focuses on a reliable monitoring loop:

- User authentication and organization membership.
- Organization-scoped monitor CRUD.
- GET and HEAD monitors over HTTP and HTTPS.
- Configurable interval, timeout, and expected status code.
- Asynchronous checks using Redis-backed Laravel queues.
- Persisted check results and response times.
- Consecutive-failure incident detection and automatic recovery.
- Dashboard views for monitors and incidents.
- Automated tests, CI checks, logs, and operational documentation.

Notifications, advanced reporting, billing, multi-region checks, and synthetic browser monitoring are deliberately outside the initial MVP and can be added after the core reliability model is proven.

## Technology stack

| Area | Choice |
| --- | --- |
| Backend | PHP, Laravel |
| Frontend | Vue.js, Inertia.js, TypeScript |
| UI | Tailwind CSS, shadcn-vue |
| Database | PostgreSQL |
| Queue and cache | Redis, Laravel Queue, Laravel Horizon |
| Runtime | Docker and Docker Compose |
| Observability | Laravel logs, Sentry, Prometheus, Grafana |
| CI | GitHub Actions |

## Architecture

PulseMonitor starts as a modular monolith. The application, domain services, queue workers, and scheduler share one codebase, while PostgreSQL, Redis, and observability infrastructure remain separate runtime dependencies. This keeps deployment and local development simple while preserving boundaries that can be extracted later if scale requires it.

See [docs/architecture.md](docs/architecture.md) for the system context, module boundaries, data flow, and key decisions.

## Local development

The application is currently being bootstrapped. Once the Laravel project is created, the expected setup will be:

```bash
cp .env.example .env
docker compose up -d
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Run the frontend and workers in separate terminals when those scripts are available:

```bash
npm install
npm run dev
php artisan horizon
php artisan schedule:work
```

## Security principles

Monitoring creates outbound requests based on user input, so target validation is a core security boundary. The implementation must validate schemes, resolve DNS safely, block private and link-local destinations, protect cloud metadata endpoints, re-check redirects, enforce timeouts, and apply rate/frequency limits.

## Project status

The repository is in the foundation phase. The product decisions and architecture are documented; Laravel application scaffolding is the next implementation step.

## License

License to be decided before the first public release.
