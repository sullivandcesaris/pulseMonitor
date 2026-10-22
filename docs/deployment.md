# Production Deployment

Week 8 provides a provider-neutral production deployment shape. It runs the
web application, scheduler, monitoring workers, notification workers,
PostgreSQL, Redis, and Caddy from the same release image. The application is
still a modular monolith, but each runtime responsibility can scale
independently.

## Prerequisites

- A Linux host with Docker Engine and Compose v2.
- A DNS record for `APP_DOMAIN` pointing to the host.
- SMTP credentials for incident notifications.
- Encrypted off-host storage for PostgreSQL backups.
- A Sentry DSN and Prometheus/Grafana environment if those integrations are
  enabled.

## First deployment

```bash
cp .env.production.example .env.production
# Edit .env.production; generate APP_KEY with `php artisan key:generate --show`.
docker compose -f infra/production/compose.yaml build
docker compose -f infra/production/compose.yaml up -d pgsql redis
docker compose -f infra/production/compose.yaml run --rm app php artisan migrate --force
docker compose -f infra/production/compose.yaml up -d
```

Caddy obtains and renews the HTTPS certificate automatically after DNS is
resolvable and ports 80/443 are reachable. Only Caddy exposes public ports;
PostgreSQL and Redis stay on the private Compose network.

## Release procedure

```bash
git pull --ff-only origin main
docker compose -f infra/production/compose.yaml build --pull
docker compose -f infra/production/compose.yaml run --rm app php artisan migrate --force
docker compose -f infra/production/compose.yaml up -d
docker compose -f infra/production/compose.yaml exec app php artisan optimize
```

The scheduler runs `schedule:work`, which dispatches due monitor checks every
minute. Monitoring and notification queues have independent workers so email
delivery cannot block checks.

## Backups and recovery

Run `infra/production/backup.sh` from a host with PostgreSQL client tools and
upload the generated custom-format dump to encrypted off-host storage. Test a
restore at least monthly in an isolated database. Keep the retention period
and deletion process under the operator's backup policy; the script never
deletes backups automatically.

## Verification checklist

- `curl -fsS https://$APP_DOMAIN/health` reports `status: ok`.
- The application container logs show successful migrations and no boot
  errors.
- `docker compose ... ps` shows app, scheduler, both workers, PostgreSQL,
  Redis, and Caddy running.
- Create a short-interval monitor and confirm a check is stored.
- Confirm a forced failure creates an incident and a recovery closes it.
- Confirm a notification delivery is recorded by the notification worker.
- Verify `/metrics` is reachable only through the intended monitoring network.

Production deployment itself, DNS, SMTP, cloud backups, and public demo
verification remain environment-specific operations and are not claimed by
the repository tests.
