#!/bin/sh
set -eu

ROOT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
SOURCE_DIR=$(mktemp -d "${TMPDIR:-/tmp}/goodgames-master.XXXXXX")
trap 'rm -rf "$SOURCE_DIR"' EXIT HUP INT TERM

if ! command -v docker >/dev/null 2>&1 || ! docker compose version >/dev/null 2>&1; then
    echo "Docker Engine and Docker Compose v2 are required on the production host." >&2
    exit 1
fi

if [ -z "${GOODGAMES_PRODUCTION_ENV_FILE:-}" ]; then
    if [ -z "${HOME:-}" ]; then
        echo "Set GOODGAMES_PRODUCTION_ENV_FILE or HOME to the production host account's home directory." >&2
        exit 1
    fi
    GOODGAMES_PRODUCTION_ENV_FILE="$HOME/.config/goodgames/.env.production"
fi

if [ ! -f "$GOODGAMES_PRODUCTION_ENV_FILE" ]; then
    echo "Production environment file not found: $GOODGAMES_PRODUCTION_ENV_FILE" >&2
    echo "Create it using .env.production.example and keep it outside the repository." >&2
    exit 1
fi

if ! git -C "$ROOT_DIR" remote get-url origin >/dev/null 2>&1; then
    echo "The source checkout must have an origin remote so production can fetch master." >&2
    exit 1
fi

git -C "$ROOT_DIR" fetch --no-tags origin master
MASTER_REVISION=$(git -C "$ROOT_DIR" rev-parse FETCH_HEAD)
git -C "$ROOT_DIR" archive "$MASTER_REVISION" | tar -x -C "$SOURCE_DIR"

docker compose \
    --project-name goodgames-production \
    --project-directory "$SOURCE_DIR" \
    --env-file "$GOODGAMES_PRODUCTION_ENV_FILE" \
    --file "$SOURCE_DIR/compose.production.yaml" \
    up --build --detach --remove-orphans --wait --wait-timeout 180

if [ -z "${GOODGAMES_PRODUCTION_SEED_FILE:-}" ] && [ -f "$ROOT_DIR/seed-data/yourgamelists.sql" ]; then
    GOODGAMES_PRODUCTION_SEED_FILE="$ROOT_DIR/seed-data/yourgamelists.sql"
fi

GOODGAMES_PRODUCTION_ENV_FILE="$GOODGAMES_PRODUCTION_ENV_FILE" \
GOODGAMES_PRODUCTION_SOURCE_DIR="$SOURCE_DIR" \
GOODGAMES_PRODUCTION_SEED_FILE="${GOODGAMES_PRODUCTION_SEED_FILE:-}" \
    "$SOURCE_DIR/scripts/seed-production.sh"

echo "Good Games production deployment is healthy on master ($MASTER_REVISION)."
