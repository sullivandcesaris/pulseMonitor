#!/usr/bin/env sh

set -eu

: "${DB_HOST:=pgsql}"
: "${DB_PORT:=5432}"
: "${DB_DATABASE:?DB_DATABASE is required}"
: "${DB_USERNAME:?DB_USERNAME is required}"
: "${DB_PASSWORD:?DB_PASSWORD is required}"
: "${BACKUP_DIR:=./backups}"

timestamp="$(date -u +%Y%m%dT%H%M%SZ)"
mkdir -p "$BACKUP_DIR"

PGPASSWORD="$DB_PASSWORD" pg_dump \
    --host="$DB_HOST" \
    --port="$DB_PORT" \
    --username="$DB_USERNAME" \
    --format=custom \
    --file="$BACKUP_DIR/pulsemonitor-$timestamp.dump" \
    "$DB_DATABASE"

echo "Created PostgreSQL backup: $BACKUP_DIR/pulsemonitor-$timestamp.dump"
echo "Upload this file to encrypted off-host storage and verify restoration before deleting older backups."
