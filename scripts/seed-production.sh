#!/bin/sh
set -eu

ROOT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
COMPOSE_DIR=${GOODGAMES_PRODUCTION_SOURCE_DIR:-$ROOT_DIR}
cd "$COMPOSE_DIR"

if [ -z "${GOODGAMES_PRODUCTION_ENV_FILE:-}" ]; then
    if [ -z "${HOME:-}" ]; then
        echo "Set GOODGAMES_PRODUCTION_ENV_FILE or HOME to the production host account's home directory." >&2
        exit 1
    fi
    GOODGAMES_PRODUCTION_ENV_FILE="$HOME/.config/goodgames/.env.production"
fi

if [ -z "${GOODGAMES_PRODUCTION_SEED_FILE:-}" ]; then
    if [ -f "$ROOT_DIR/seed-data/yourgamelists.sql" ]; then
        GOODGAMES_PRODUCTION_SEED_FILE="$ROOT_DIR/seed-data/yourgamelists.sql"
    elif [ -n "${HOME:-}" ]; then
        GOODGAMES_PRODUCTION_SEED_FILE="$HOME/.config/goodgames/yourgamelists.sql"
    else
        echo "Set GOODGAMES_PRODUCTION_SEED_FILE to the production seed file location." >&2
        exit 1
    fi
fi

if [ ! -f "$GOODGAMES_PRODUCTION_ENV_FILE" ]; then
    echo "Production environment file not found: $GOODGAMES_PRODUCTION_ENV_FILE" >&2
    exit 1
fi

# The dataset stays outside the checkout because it contains personal library data.
if [ ! -f "$GOODGAMES_PRODUCTION_SEED_FILE" ]; then
    echo "Production seed file not installed; skipping first-deploy seed. Expected: $GOODGAMES_PRODUCTION_SEED_FILE"
    exit 0
fi

set -- docker compose \
    --project-name goodgames-production \
    --project-directory "$COMPOSE_DIR" \
    --env-file "$GOODGAMES_PRODUCTION_ENV_FILE" \
    --file "$COMPOSE_DIR/compose.production.yaml"

if "$@" exec -T app test -f /var/www/html/storage/app/.production-seeded; then
    echo "Production seed has already been imported; skipping."
    exit 0
fi
if "$@" exec -T app test -f /var/www/html/storage/app/.production-seed-in-progress; then
    echo "A previous seed attempt was interrupted. Refusing to replace production data again." >&2
    echo "Restore the backup and remove the in-progress marker only after reviewing the database." >&2
    exit 1
fi

if ! "$@" ps --status running --services | grep -qx database; then
    echo "The production database container is not running. Deploy production first." >&2
    exit 1
fi

ROW_COUNTS=$("$@" exec -T database sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" -N -e "SELECT (SELECT COUNT(*) FROM users) + (SELECT COUNT(*) FROM games) + (SELECT COUNT(*) FROM filters);"')
case "$ROW_COUNTS" in
    ''|*[!0-9]*) echo "Could not verify whether the production database is empty." >&2; exit 1 ;;
esac
if [ "$ROW_COUNTS" -ne 0 ]; then
    echo "Production already contains user, game, or filter rows. Refusing to overwrite them with the seed dump." >&2
    exit 1
fi

GOODGAMES_PRODUCTION_ENV_FILE="$GOODGAMES_PRODUCTION_ENV_FILE" \
GOODGAMES_PRODUCTION_SOURCE_DIR="$COMPOSE_DIR" \
    "$COMPOSE_DIR/scripts/backup-production.sh"

# The dump contains CREATE TABLE statements. Replace the migration-created
# empty schema first, then import the dump and leave the app's migrations table intact.
"$@" exec -T app touch /var/www/html/storage/app/.production-seed-in-progress
"$@" exec -T database sh -c '
    case "$MYSQL_DATABASE" in
        ""|*[!A-Za-z0-9_]*) echo "Unsafe production database name: $MYSQL_DATABASE" >&2; exit 1 ;;
    esac
    mysql -uroot -p"$MYSQL_ROOT_PASSWORD" -e "DROP DATABASE IF EXISTS \`$MYSQL_DATABASE\`; CREATE DATABASE \`$MYSQL_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
'
"$@" exec -T database sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' \
    < "$GOODGAMES_PRODUCTION_SEED_FILE"
"$@" exec -T app sh -c 'mv /var/www/html/storage/app/.production-seed-in-progress /var/www/html/storage/app/.production-seeded'

echo "Imported the production seed from $(basename "$GOODGAMES_PRODUCTION_SEED_FILE")."
