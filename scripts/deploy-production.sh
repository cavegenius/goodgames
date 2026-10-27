#!/bin/sh
set -eu

ROOT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
cd "$ROOT_DIR"

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

docker compose \
    --project-name goodgames-production \
    --env-file "$GOODGAMES_PRODUCTION_ENV_FILE" \
    --file compose.production.yaml \
    up --build --detach --remove-orphans --wait --wait-timeout 180

echo "Good Games production deployment is healthy."
