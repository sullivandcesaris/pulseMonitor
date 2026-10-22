# 🚀 PulseMonitor — Development Roadmap v2.0

**Project:** PulseMonitor — Service Monitoring SaaS  
**Backend:** PHP / Laravel  
**Frontend:** Vue.js + Inertia.js + TypeScript  
**Database:** PostgreSQL  
**Queue & Cache:** Redis  
**Infrastructure:** Docker  
**Observability:** Laravel Horizon, Sentry, Prometheus, Grafana

**Main Objective:** Build a scalable SaaS platform that monitors websites and APIs, detects outages, tracks response times, and notifies customers when services become unavailable.

**Engineering Objective:** Demonstrate software architecture, reliability, scalability, security, testing, observability, and technical decision-making.

## Current Project Status — 2026-10-22

The repository is public and pushed to GitHub. The Laravel & Frontend Foundation is implemented and verified:

- Laravel Vue/Inertia starter kit with authentication is running.
- TypeScript, Vue, Tailwind CSS, shadcn-vue, light/dark themes, and responsive layouts are configured.
- PulseMonitor dashboard, sidebar navigation, monitor management, and organization-scoped incident history are available.
- Frontend architecture and component conventions are documented in `docs/frontend.md`.
- Production asset build, ESLint, Prettier, Vue type checking, Laravel Pint, and the starter test suite pass.
- Laravel Sail is configured as the local runtime with PostgreSQL and Redis services.
- Sail containers are running; the application responds with HTTP 200 at `http://localhost:8000`.
- PostgreSQL migrations ran successfully and `migrate:status` reports all migrations as `Ran`.
- Redis passed both a service-level `PONG` check and a Laravel set/get round trip.
- Laravel reports PostgreSQL, Redis cache/session/queue, and stack/single logging drivers; public storage is linked.
- Week 2 domain foundations are in place: organizations, memberships, monitors, monitor checks, incidents, relationships, indexes, factories, seed data, and a relationship diagram.
- Week 3 HTTP monitoring is implemented with queued checks, scheduler dispatch, SSRF protections, outbound port restrictions, and a dedicated Redis queue worker.
- Week 4 incident detection and recovery are implemented with configurable failure thresholds, idempotent transitions, recovery duration tracking, and automated tests.
- Week 5 dashboard, analytics, and notification workflows are implemented with live monitoring data, selectable periods, response-time charts, filters, incident history, and queued email delivery tracking.
- Week 6 scalability foundations are implemented with separate monitoring/notification queues, independent workers, atomic due-monitor claims, overlap protection, historical pagination, and documented query strategy.
- Week 6 benchmarks now cover 100, 1,000, and 10,000-monitor scenarios with database timing, queue throughput/waiting, CPU, memory, and bottleneck documentation.
- Week 7 observability and operational controls are implemented with structured logs, correlation IDs, Horizon/Sentry integrations, Prometheus/Grafana definitions, health checks, security controls, and recovery procedures.
- Week 8 production artifacts are prepared with a release image, Compose services for the app, scheduler, workers, PostgreSQL, Redis, and Caddy, production environment guidance, and a backup/recovery runbook.

**Next priority:** deploy the Week 8 release to a public host, configure DNS/SMTP/backups, and publish the demonstration and case study.

---

# PHASE 1 — FOUNDATION & CORE DOMAIN

## Week 1 — Project Foundation

**Goal:** Create a professional development environment with a solid technical foundation.

### Repository & Documentation

- [x] Create a public GitHub repository named `pulsemonitor`.
- [x] Define the project's purpose, target users, and MVP scope.
- [x] Create the initial README.
- [x] Document the selected technology stack.
- [x] Create `docs/architecture.md`.
- [x] Document the decision to start with a modular monolith.
- [x] Add an initial architecture diagram.
- [x] Create `.env.example`.

### Laravel & Frontend Foundation — PulseMonitor

**Goal:** Set up Laravel, Vue.js, Inertia.js, TypeScript, and a reusable UI component system.

#### 1. Project Setup
- [x] Create a Laravel project using the official Vue.js + Inertia.js starter kit.
- [x] Verify TypeScript configuration.
- [x] Verify authentication (registration, login, logout).
- [x] Define the initial application structure.

#### 2. UI Framework & Design System
- [x] Confirm shadcn-vue is configured.
- [x] Confirm Tailwind CSS is configured.
- [x] Define design tokens (colors, typography, spacing, borders).
- [x] Configure light and dark themes.
- [x] Establish reusable UI component conventions.

#### 3. Dashboard Foundation
- [x] Create the main application layout with a sidebar.
- [x] Define navigation (Dashboard, Monitors, Incidents, Settings).
- [x] Create reusable cards, buttons, forms, dialogs, and status badges.
- [x] Create an initial dashboard using mock data.
- [x] Ensure responsive layouts for desktop and mobile.

#### 4. Development Quality
- [x] Configure Laravel Pint for PHP formatting.
- [x] Configure ESLint for TypeScript and Vue.
- [x] Configure Prettier for frontend formatting.
- [x] Document frontend architecture and component conventions.

#### 5. Future Preparation
- [x] Evaluate charting libraries (Apache ECharts or Chart.js).
- [x] Document the selected charting solution for Week 5 in `docs/charting-decision.md`.

**Deliverable:** A working Laravel + Vue.js application with authentication, a responsive dashboard layout, light/dark themes, and reusable UI components.

### Infrastructure

- [x] Configure PostgreSQL.
- [x] Configure Redis.
- [x] Set up Docker Compose through Laravel Sail.
- [x] Configure environment variables for Sail services.
- [x] Verify database migrations.
- [x] Verify the Redis connection.
- [x] Configure basic application logging.

### CI/CD

- [x] Create a GitHub Actions workflow.
- [x] Configure automated tests.
- [x] Configure linting and code formatting checks.
- [x] Verify the pipeline on every pull request.

**Deliverable:** A working Laravel + Vue application with authentication, PostgreSQL, Redis, Docker, and automated CI checks.

---

## Week 2 — Domain Modeling & Database Design

**Goal:** Design the core entities and define how customers register monitoring targets.

### Domain Entities

- [x] Define the `User` entity.
- [x] Define the `Organization` entity.
- [x] Define the `Monitor` entity.
- [x] Define the `MonitorCheck` entity.
- [x] Define the `Incident` entity.
- [x] Define relationships between entities.
- [x] Create a database relationship diagram in `docs/domain-model.md`.

### Monitor Configuration

Each monitor should support:

- [x] Monitor name.
- [x] Target URL.
- [x] HTTP method: GET or HEAD.
- [x] Monitoring interval (for example, 60 or 300 seconds).
- [x] Expected HTTP status code.
- [x] Request timeout.
- [x] Enabled/disabled state.
- [x] Organization ownership.
- [x] Monitoring timestamps and next scheduled check.

### Database & Authorization

- [x] Create database migrations.
- [x] Create Eloquent models and relationships.
- [x] Implement organization membership.
- [x] Implement organization-based authorization.
- [x] Prevent cross-organization data access.
- [x] Create appropriate database indexes.
- [x] Add database factories and seeders.

### Monitor Management

- [x] Implement monitor creation.
- [x] Implement monitor editing.
- [x] Implement monitor deletion.
- [x] Implement monitor activation/deactivation.
- [x] Validate user input.
- [x] Add authorization tests.
- [x] Add CRUD feature tests.

**Deliverable:** Users can register and manage GET/HEAD monitoring targets within their organizations.

---

# PHASE 2 — MONITORING ENGINE & RELIABILITY

## Week 3 — HTTP Monitoring Engine

**Goal:** Build the service responsible for checking websites and APIs.

### HTTP Monitoring

- [x] Create the monitoring service.
- [x] Implement GET checks.
- [x] Implement HEAD checks.
- [x] Configure expected HTTP status codes.
- [x] Measure HTTP response time.
- [x] Handle connection errors.
- [x] Handle request timeouts.
- [x] Record HTTP status codes.
- [x] Define how unexpected HTTP responses are classified.
- [x] Save results in the `monitor_checks` table.

### Security

- [x] Require HTTP or HTTPS URLs.
- [x] Validate monitoring destinations.
- [x] Block private, loopback, link-local, and other restricted network destinations.
- [x] Prevent access to cloud metadata services.
- [x] Validate DNS resolutions and redirects.
- [x] Apply outbound network restrictions where possible (application port allowlist, DNS/IP filtering, redirect blocking, and documented infrastructure egress boundary).
- [x] Limit monitoring frequency.
- [x] Prevent unsafe or unauthorized targets.

### Background Processing

- [x] Create a Laravel monitoring Job.
- [x] Configure Redis queues.
- [x] Configure Laravel Queue Workers through the dedicated Sail `queue` service.
- [x] Use Laravel Scheduler to identify due monitors.
- [x] Dispatch monitoring Jobs asynchronously.
- [x] Prevent duplicate concurrent checks.
- [x] Handle worker failures.
- [x] Record execution errors.

### Testing

- [x] Test successful HTTP responses.
- [x] Test unexpected HTTP status codes.
- [x] Test connection failures.
- [x] Test timeouts.
- [x] Test invalid URLs.
- [x] Test SSRF protection.
- [x] Test job dispatching and result persistence.

**Deliverable:** PulseMonitor automatically executes scheduled GET/HEAD checks and stores their results.

---

## Week 4 — Incident Detection & Recovery

**Goal:** Reliably detect when monitored services become unavailable.

### Incident Detection

- [x] Define healthy and unhealthy states.
- [x] Implement configurable consecutive-failure thresholds.
- [x] Create incidents after repeated failures.
- [x] Prevent duplicate open incidents.
- [x] Record incident start time.
- [x] Record incident failure information.
- [x] Associate incidents with monitors.

### Recovery

- [x] Detect successful responses after an outage.
- [x] Define recovery confirmation rules.
- [x] Automatically resolve incidents.
- [x] Record incident resolution time.
- [x] Calculate incident duration.
- [x] Keep historical incidents available.

### Reliability

- [x] Implement job retries and backoff.
- [x] Define when infrastructure failures should be retried.
- [x] Prevent duplicate incident transitions.
- [x] Handle concurrent workers.
- [x] Handle delayed and out-of-order check results.
- [x] Implement idempotent processing.

### Tests

- [x] Test consecutive failures.
- [x] Test temporary failures.
- [x] Test incident creation.
- [x] Test incident recovery.
- [x] Test concurrent processing.
- [x] Test duplicate job execution.

**Deliverable:** PulseMonitor reliably detects outages, creates incidents, and records service recovery.

---

# PHASE 3 — PRODUCT EXPERIENCE

## Week 5 — Dashboard, Analytics & Notifications

**Goal:** Transform the monitoring engine into a usable SaaS application.

### Dashboard

- [x] Create the main monitoring dashboard.
- [x] Display registered services.
- [x] Display current service status.
- [x] Display last check time.
- [x] Display HTTP response time.
- [x] Display recent monitoring results.
- [x] Create response-time charts.
- [x] Implement monitor filters.
- [x] Create incident history screens.

### Availability Metrics

- [x] Define how uptime is calculated.
- [x] Calculate uptime percentage for selected periods.
- [x] Calculate average response time.
- [x] Identify periods without monitoring data.
- [x] Distinguish unknown status from confirmed downtime.
- [x] Display monitoring history.

### Notifications

- [x] Implement email notifications.
- [x] Send notifications when incidents are opened.
- [x] Send notifications when services recover.
- [x] Prevent duplicate notifications.
- [x] Record notification delivery attempts.
- [x] Handle notification failures.
- [x] Allow users to enable or disable alerts.

### User Experience

- [x] Create responsive dashboard layouts.
- [x] Add loading and error states.
- [x] Provide clear incident information.
- [x] Add confirmation messages for user actions.

**Deliverable:** Customers can register services, monitor availability, view incidents, and receive outage notifications.

---

# PHASE 4 — ADVANCED SOFTWARE ENGINEERING

## Week 6 — Scalability & Performance

**Goal:** Demonstrate the ability to design systems that support increasing workloads.

### Queue Scalability

- [x] Separate monitoring jobs from notification jobs.
- [x] Configure independent queue workers.
- [x] Test multiple workers.
- [x] Implement concurrency limits.
- [x] Prevent overlapping checks.
- [x] Evaluate queue processing throughput.
- [x] Implement backpressure strategies.
- [x] Avoid scheduling more work than the system can process.

### Database Performance

- [x] Analyze frequently executed queries.
- [x] Add indexes based on actual query patterns.
- [x] Investigate slow queries.
- [x] Implement pagination for historical data.
- [x] Evaluate caching strategies.
- [x] Prevent N+1 query problems.
- [x] Define long-term monitoring data retention.

### Load Testing

- [x] Create a load-testing scenario.
- [x] Simulate 100 registered monitors.
- [x] Simulate 1,000 registered monitors.
- [x] Simulate 10,000 registered monitors.
- [x] Measure queue throughput.
- [x] Measure queue waiting time.
- [x] Measure database performance.
- [x] Measure memory and CPU consumption.
- [x] Identify system bottlenecks.
- [x] Document performance improvements.

**Deliverable:** Performance benchmarks and documented scaling strategies showing how PulseMonitor behaves under increasing workloads.

---

## Week 7 — Observability, Security & Operational Reliability

**Goal:** Make the system observable, secure, and easier to maintain.

### Application Observability

- [x] Implement structured application logs.
- [x] Implement correlation IDs.
- [x] Configure Laravel Horizon.
- [x] Configure Sentry.
- [x] Export application metrics.
- [x] Configure Prometheus.
- [x] Create Grafana dashboards.
- [x] Monitor queue latency.
- [x] Monitor failed jobs.
- [x] Monitor application response times.
- [x] Add health-check endpoints.
- [x] Configure operational alerts.

### Security

- [x] Review authentication and authorization.
- [x] Validate organization data isolation.
- [x] Review SSRF protection.
- [x] Test URL and redirect validation.
- [x] Apply appropriate rate limits.
- [x] Review sensitive data handling.
- [x] Protect monitoring credentials and configuration.
- [x] Review logging for sensitive information.

### Reliability & Maintenance

- [x] Test recovery after queue worker failures.
- [x] Test behavior during Redis outages.
- [x] Test behavior during database outages.
- [x] Define error-handling strategies.
- [x] Define data-retention policies.
- [x] Document operational recovery procedures.

**Deliverable:** A monitored application with operational metrics, error tracking, alerts, and documented security controls.

---

# PHASE 5 — DEPLOYMENT & PORTFOLIO

## Week 8 — Production Deployment & Engineering Case Study

**Goal:** Publish a working application and demonstrate your engineering decisions.

### Deployment

- [ ] Deploy the Laravel application.
- [ ] Deploy PostgreSQL.
- [x] Configure Redis.
- [x] Configure queue workers.
- [x] Configure Laravel Scheduler.
- [x] Configure HTTPS.
- [x] Configure production environment variables.
- [x] Configure database backups.
- [ ] Verify background jobs in production.
- [ ] Verify monitoring execution.
- [ ] Verify incident detection.
- [ ] Verify email notifications.

### Technical Documentation

- [x] Complete the README.
- [x] Document local installation.
- [x] Document the application architecture.
- [x] Add an architecture diagram.
- [x] Document major technical decisions.
- [x] Explain why a modular monolith was selected.
- [x] Document queue and worker design.
- [x] Document the database structure.
- [x] Document SSRF security decisions.
- [x] Document monitoring limitations.
- [x] Include load-testing results.

### Portfolio

- [ ] Create a public demonstration environment.
- [ ] Add screenshots to the GitHub repository.
- [ ] Record a short demonstration video.
- [ ] Write a technical case study.
- [ ] Describe engineering challenges encountered.
- [ ] Explain performance improvements.
- [ ] Explain architectural trade-offs.
- [ ] Publish the project in my portfolio.
- [ ] Add the project to LinkedIn.

**Deliverable:** A publicly accessible PulseMonitor demonstration and a professional GitHub repository demonstrating backend engineering skills.

---

# PHASE 6 — FUTURE ROADMAP

*These features are outside the initial MVP scope.*

## Synthetic API Monitoring

**Goal:** Support more advanced API monitoring, including operations that may change data.

- [ ] Support POST, PUT, PATCH, and DELETE requests.
- [ ] Support configurable request bodies.
- [ ] Support custom HTTP headers.
- [ ] Support authentication configurations.
- [ ] Implement expected response-body assertions.
- [ ] Support dedicated testing environments.
- [ ] Support safe dry-run mechanisms when provided by target applications.
- [ ] Support unique test execution identifiers.
- [ ] Implement cleanup strategies for test records.
- [ ] Prevent unintended production data changes.
- [ ] Add safeguards and explicit authorization for state-changing operations.

## Advanced Monitoring

- [ ] Monitor SSL certificate expiration.
- [ ] Monitor DNS resolution.
- [ ] Add multi-region monitoring.
- [ ] Implement public status pages.
- [ ] Support configurable maintenance windows.
- [ ] Support monitoring of application dependencies.
- [ ] Add more advanced SLA/SLO reports.
- [ ] Support webhook and messaging notifications.

## AI-Assisted Incident Analysis

- [ ] Generate AI-powered incident summaries.
- [ ] Analyze historical incidents.
- [ ] Identify recurring failure patterns.
- [ ] Suggest possible incident causes.
- [ ] Generate incident reports.
- [ ] Implement LLM evaluation and hallucination safeguards.
- [ ] Monitor AI usage and cost.

## Business & SaaS Features

- [ ] Implement subscription plans.
- [ ] Define monitoring limits per plan.
- [ ] Add team roles and permissions.
- [ ] Implement usage tracking.
- [ ] Create billing integrations.
- [ ] Support configurable notification channels.

---

# FINAL ENGINEERING CHECKLIST

## Architecture

- [ ] Clear separation of responsibilities.
- [ ] Modular and maintainable architecture.
- [ ] Documented technical decisions.
- [ ] Reliable background processing.

## Scalability

- [ ] Horizontally scalable monitoring workers.
- [ ] Optimized database queries.
- [ ] Controlled concurrent execution.
- [ ] Measurable performance benchmarks.

## Reliability

- [ ] Retries and timeouts.
- [ ] Idempotent processing.
- [ ] Incident consistency.
- [ ] Recovery handling.

## Security

- [ ] Authentication and authorization.
- [ ] Tenant isolation.
- [ ] SSRF protection.
- [ ] Secure configuration management.

## Observability

- [ ] Structured logs.
- [ ] Operational metrics.
- [ ] Error monitoring.
- [ ] Dashboards and alerts.

## Portfolio Quality

- [ ] Public GitHub repository.
- [ ] Professional README.
- [ ] Architecture documentation.
- [ ] Automated tests.
- [ ] CI/CD pipeline.
- [ ] Live demonstration.
- [ ] Technical case study.

---

**Definition of Success**

PulseMonitor is complete when a customer can register a website or API, configure GET/HEAD checks, monitor availability and response times, receive incident alerts, and review historical data through a reliable SaaS dashboard.

From an engineering perspective, the project must also demonstrate automated testing, secure HTTP monitoring, scalable background processing, observability, and documented architectural decisions.
