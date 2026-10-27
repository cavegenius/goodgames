#!/bin/sh
set -eu

ROOT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
cd "$ROOT_DIR"

if [ -z "${GOODGAMES_PRODUCTION_ENV_FILE:-}" ]; then
    if [ -z "${HOME:-}" ]; then
        echo "Set GOODGAMES_PRODUCTION_ENV_FILE or HOME to the production host account's home directory." >&2
        exit 1
    fi
    GOODGAMES_PRODUCTION_ENV_FILE="$HOME/.config/goodgames/.env.production"
fi

if [ ! -f "$GOODGAMES_PRODUCTION_ENV_FILE" ]; then
    echo "Production environment file not found: $GOODGAMES_PRODUCTION_ENV_FILE" >&2
    exit 1
fi

if [ -z "${GOODGAMES_BACKUP_DIR:-}" ]; then
    if [ -z "${HOME:-}" ]; then
        echo "Set GOODGAMES_BACKUP_DIR or HOME to a backup destination." >&2
        exit 1
    fi
    GOODGAMES_BACKUP_DIR="$HOME/.local/backups/goodgames"
fi

mkdir -p "$GOODGAMES_BACKUP_DIR"
umask 077
TEMP_DIR=$(mktemp -d "$GOODGAMES_BACKUP_DIR/.goodgames-backup.XXXXXX")
trap 'rm -rf "$TEMP_DIR"' EXIT HUP INT TERM

if ! docker compose version >/dev/null 2>&1; then
    echo "Docker Compose v2 is required on the production host." >&2
    exit 1
fi

docker compose \
    --project-name goodgames-production \
    --env-file "$GOODGAMES_PRODUCTION_ENV_FILE" \
    --file compose.production.yaml \
    exec -T database sh -c 'exec mysqldump --single-transaction --routines --triggers -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' \
    > "$TEMP_DIR/database.sql"

docker compose \
    --project-name goodgames-production \
    --env-file "$GOODGAMES_PRODUCTION_ENV_FILE" \
    --file compose.production.yaml \
    exec -T app tar -czf - -C /var/www/html/storage . \
    > "$TEMP_DIR/storage.tar.gz"

TIMESTAMP=$(date -u '+%Y%m%dT%H%M%SZ')
ARCHIVE="$GOODGAMES_BACKUP_DIR/goodgames-$TIMESTAMP.tar.gz"
tar -czf "$ARCHIVE" -C "$TEMP_DIR" database.sql storage.tar.gz

echo "Created production backup: $ARCHIVE"
