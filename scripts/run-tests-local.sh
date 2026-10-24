#!/bin/sh
set -eu

ROOT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
ENV_FILE="$ROOT_DIR/.env.docker"

if [ ! -f "$ENV_FILE" ]; then
    echo "No local Docker environment found. Start the app first with ./scripts/start.sh." >&2
    exit 1
fi

if ! command -v docker >/dev/null 2>&1 || ! docker info >/dev/null 2>&1; then
    echo "Docker Desktop must be running. Start it and try again." >&2
    exit 1
fi

if ! docker compose --env-file "$ENV_FILE" -f "$ROOT_DIR/compose.yaml" -f "$ROOT_DIR/compose.local.yaml" ps --status running --services | grep -qx app; then
    echo "The local app container is not running. Start it with ./scripts/start.sh." >&2
    exit 1
fi

cd "$ROOT_DIR"
exec docker compose --env-file "$ENV_FILE" -f compose.yaml -f compose.local.yaml exec -T app \
    ./vendor/bin/phpunit --configuration=phpunit.local.xml "$@"
