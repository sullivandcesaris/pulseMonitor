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
- PulseMonitor dashboard, sidebar navigation, mock monitor view, and incident view are available.
- Frontend architecture and component conventions are documented in `docs/frontend.md`.
- Production asset build, ESLint, Prettier, Vue type checking, Laravel Pint, and the starter test suite pass.
- Laravel Sail is configured as the local runtime with PostgreSQL and Redis services.
- Sail containers are running; the application responds with HTTP 200 at `http://localhost:8000`.
- PostgreSQL migrations ran successfully and `migrate:status` reports all migrations as `Ran`.
- Redis passed both a service-level `PONG` check and a Laravel set/get round trip.
- Laravel reports PostgreSQL, Redis cache/session/queue, and stack/single logging drivers; public storage is linked.

**Next priority:** begin domain modeling in Week 2.

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
- [ ] Evaluate charting libraries (Apache ECharts or Chart.js).
- [ ] Document the selected charting solution for Week 5.

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

- [ ] Define the `User` entity.
- [ ] Define the `Organization` entity.
- [ ] Define the `Monitor` entity.
- [ ] Define the `MonitorCheck` entity.
- [ ] Define the `Incident` entity.
- [ ] Define relationships between entities.
- [ ] Create a database relationship diagram.

### Monitor Configuration

Each monitor should support:

- [ ] Monitor name.
- [ ] Target URL.
- [ ] HTTP method: GET or HEAD.
- [ ] Monitoring interval (for example, 60 or 300 seconds).
- [ ] Expected HTTP status code.
- [ ] Request timeout.
- [ ] Enabled/disabled state.
- [ ] Organization ownership.
- [ ] Monitoring timestamps and next scheduled check.

### Database & Authorization

- [ ] Create database migrations.
- [ ] Create Eloquent models and relationships.
- [ ] Implement organization membership.
- [ ] Implement organization-based authorization.
- [ ] Prevent cross-organization data access.
- [ ] Create appropriate database indexes.
- [ ] Add database factories and seeders.

### Monitor Management

- [ ] Implement monitor creation.
- [ ] Implement monitor editing.
- [ ] Implement monitor deletion.
- [ ] Implement monitor activation/deactivation.
- [ ] Validate user input.
- [ ] Add authorization tests.
- [ ] Add CRUD feature tests.

**Deliverable:** Users can register and manage GET/HEAD monitoring targets within their organizations.

---

# PHASE 2 — MONITORING ENGINE & RELIABILITY

## Week 3 — HTTP Monitoring Engine

**Goal:** Build the service responsible for checking websites and APIs.

### HTTP Monitoring

- [ ] Create the monitoring service.
- [ ] Implement GET checks.
- [ ] Implement HEAD checks.
- [ ] Configure expected HTTP status codes.
- [ ] Measure HTTP response time.
- [ ] Handle connection errors.
- [ ] Handle request timeouts.
- [ ] Record HTTP status codes.
- [ ] Define how unexpected HTTP responses are classified.
- [ ] Save results in the `monitor_checks` table.

### Security

- [ ] Require HTTP or HTTPS URLs.
- [ ] Validate monitoring destinations.
- [ ] Block private, loopback, link-local, and other restricted network destinations.
- [ ] Prevent access to cloud metadata services.
- [ ] Validate DNS resolutions and redirects.
- [ ] Apply outbound network restrictions where possible.
- [ ] Limit monitoring frequency.
- [ ] Prevent unsafe or unauthorized targets.

### Background Processing

- [ ] Create a Laravel monitoring Job.
- [ ] Configure Redis queues.
- [ ] Configure Laravel Queue Workers.
- [ ] Use Laravel Scheduler to identify due monitors.
- [ ] Dispatch monitoring Jobs asynchronously.
- [ ] Prevent duplicate concurrent checks.
- [ ] Handle worker failures.
- [ ] Record execution errors.

### Testing

- [ ] Test successful HTTP responses.
- [ ] Test unexpected HTTP status codes.
- [ ] Test connection failures.
- [ ] Test timeouts.
- [ ] Test invalid URLs.
- [ ] Test SSRF protection.
- [ ] Test job dispatching and result persistence.

**Deliverable:** PulseMonitor automatically executes scheduled GET/HEAD checks and stores their results.

---

## Week 4 — Incident Detection & Recovery

**Goal:** Reliably detect when monitored services become unavailable.

### Incident Detection

- [ ] Define healthy and unhealthy states.
- [ ] Implement configurable consecutive-failure thresholds.
- [ ] Create incidents after repeated failures.
- [ ] Prevent duplicate open incidents.
- [ ] Record incident start time.
- [ ] Record incident failure information.
- [ ] Associate incidents with monitors.

### Recovery

- [ ] Detect successful responses after an outage.
- [ ] Define recovery confirmation rules.
- [ ] Automatically resolve incidents.
- [ ] Record incident resolution time.
- [ ] Calculate incident duration.
- [ ] Keep historical incidents available.

### Reliability

- [ ] Implement job retries and backoff.
- [ ] Define when infrastructure failures should be retried.
- [ ] Prevent duplicate incident transitions.
- [ ] Handle concurrent workers.
- [ ] Handle delayed and out-of-order check results.
- [ ] Implement idempotent processing.

### Tests

- [ ] Test consecutive failures.
- [ ] Test temporary failures.
- [ ] Test incident creation.
- [ ] Test incident recovery.
- [ ] Test concurrent processing.
- [ ] Test duplicate job execution.

**Deliverable:** PulseMonitor reliably detects outages, creates incidents, and records service recovery.

---

# PHASE 3 — PRODUCT EXPERIENCE

## Week 5 — Dashboard, Analytics & Notifications

**Goal:** Transform the monitoring engine into a usable SaaS application.

### Dashboard

- [ ] Create the main monitoring dashboard.
- [ ] Display registered services.
- [ ] Display current service status.
- [ ] Display last check time.
- [ ] Display HTTP response time.
- [ ] Display recent monitoring results.
- [ ] Create response-time charts.
- [ ] Implement monitor filters.
- [ ] Create incident history screens.

### Availability Metrics

- [ ] Define how uptime is calculated.
- [ ] Calculate uptime percentage for selected periods.
- [ ] Calculate average response time.
- [ ] Identify periods without monitoring data.
- [ ] Distinguish unknown status from confirmed downtime.
- [ ] Display monitoring history.

### Notifications

- [ ] Implement email notifications.
- [ ] Send notifications when incidents are opened.
- [ ] Send notifications when services recover.
- [ ] Prevent duplicate notifications.
- [ ] Record notification delivery attempts.
- [ ] Handle notification failures.
- [ ] Allow users to enable or disable alerts.

### User Experience

- [ ] Create responsive dashboard layouts.
- [ ] Add loading and error states.
- [ ] Provide clear incident information.
- [ ] Add confirmation messages for user actions.

**Deliverable:** Customers can register services, monitor availability, view incidents, and receive outage notifications.

---

# PHASE 4 — ADVANCED SOFTWARE ENGINEERING

## Week 6 — Scalability & Performance

**Goal:** Demonstrate the ability to design systems that support increasing workloads.

### Queue Scalability

- [ ] Separate monitoring jobs from notification jobs.
- [ ] Configure independent queue workers.
- [ ] Test multiple workers.
- [ ] Implement concurrency limits.
- [ ] Prevent overlapping checks.
- [ ] Evaluate queue processing throughput.
- [ ] Implement backpressure strategies.
- [ ] Avoid scheduling more work than the system can process.

### Database Performance

- [ ] Analyze frequently executed queries.
- [ ] Add indexes based on actual query patterns.
- [ ] Investigate slow queries.
- [ ] Implement pagination for historical data.
- [ ] Evaluate caching strategies.
- [ ] Prevent N+1 query problems.
- [ ] Define long-term monitoring data retention.

### Load Testing

- [ ] Create a load-testing scenario.
- [ ] Simulate 100 registered monitors.
- [ ] Simulate 1,000 registered monitors.
- [ ] Simulate 10,000 registered monitors.
- [ ] Measure queue throughput.
- [ ] Measure queue waiting time.
- [ ] Measure database performance.
- [ ] Measure memory and CPU consumption.
- [ ] Identify system bottlenecks.
- [ ] Document performance improvements.

**Deliverable:** Performance benchmarks and documented scaling strategies showing how PulseMonitor behaves under increasing workloads.

---

## Week 7 — Observability, Security & Operational Reliability

**Goal:** Make the system observable, secure, and easier to maintain.

### Application Observability

- [ ] Implement structured application logs.
- [ ] Implement correlation IDs.
- [ ] Configure Laravel Horizon.
- [ ] Configure Sentry.
- [ ] Export application metrics.
- [ ] Configure Prometheus.
- [ ] Create Grafana dashboards.
- [ ] Monitor queue latency.
- [ ] Monitor failed jobs.
- [ ] Monitor application response times.
- [ ] Add health-check endpoints.
- [ ] Configure operational alerts.

### Security

- [ ] Review authentication and authorization.
- [ ] Validate organization data isolation.
- [ ] Review SSRF protection.
- [ ] Test URL and redirect validation.
- [ ] Apply appropriate rate limits.
- [ ] Review sensitive data handling.
- [ ] Protect monitoring credentials and configuration.
- [ ] Review logging for sensitive information.

### Reliability & Maintenance

- [ ] Test recovery after queue worker failures.
- [ ] Test behavior during Redis outages.
- [ ] Test behavior during database outages.
- [ ] Define error-handling strategies.
- [ ] Define data-retention policies.
- [ ] Document operational recovery procedures.

**Deliverable:** A monitored application with operational metrics, error tracking, alerts, and documented security controls.

---

# PHASE 5 — DEPLOYMENT & PORTFOLIO

## Week 8 — Production Deployment & Engineering Case Study

**Goal:** Publish a working application and demonstrate your engineering decisions.

### Deployment

- [ ] Deploy the Laravel application.
- [ ] Deploy PostgreSQL.
- [ ] Configure Redis.
- [ ] Configure queue workers.
- [ ] Configure Laravel Scheduler.
- [ ] Configure HTTPS.
- [ ] Configure production environment variables.
- [ ] Configure database backups.
- [ ] Verify background jobs in production.
- [ ] Verify monitoring execution.
- [ ] Verify incident detection.
- [ ] Verify email notifications.

### Technical Documentation

- [ ] Complete the README.
- [ ] Document local installation.
- [ ] Document the application architecture.
- [ ] Add an architecture diagram.
- [ ] Document major technical decisions.
- [ ] Explain why a modular monolith was selected.
- [ ] Document queue and worker design.
- [ ] Document the database structure.
- [ ] Document SSRF security decisions.
- [ ] Document monitoring limitations.
- [ ] Include load-testing results.

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
