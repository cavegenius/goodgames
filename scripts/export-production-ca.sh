#!/bin/sh
set -eu

if [ -z "${GOODGAMES_PRODUCTION_ENV_FILE:-}" ]; then
    if [ -z "${HOME:-}" ]; then
        echo "Set GOODGAMES_PRODUCTION_ENV_FILE or HOME to the production host account's home directory." >&2
        exit 1
    fi
    GOODGAMES_PRODUCTION_ENV_FILE="$HOME/.config/goodgames/.env.production"
fi

if [ -z "${1:-}" ]; then
    if [ -z "${HOME:-}" ]; then
        echo "Pass a destination path for the Caddy root certificate." >&2
        exit 1
    fi
    DESTINATION="$HOME/.config/goodgames/caddy-root.crt"
else
    DESTINATION=$1
fi

if [ ! -f "$GOODGAMES_PRODUCTION_ENV_FILE" ]; then
    echo "Production environment file not found: $GOODGAMES_PRODUCTION_ENV_FILE" >&2
    exit 1
fi

PROXY_ID=$(docker ps \
    --filter label=com.docker.compose.project=goodgames-production \
    --filter label=com.docker.compose.service=proxy \
    --quiet | head -n 1)

if [ -z "$PROXY_ID" ]; then
    echo "Production proxy is not running. Deploy production first." >&2
    exit 1
fi

mkdir -p "$(dirname -- "$DESTINATION")"
docker cp "$PROXY_ID:/data/caddy/pki/authorities/local/root.crt" "$DESTINATION"
chmod 644 "$DESTINATION"
printf "Exported Caddy's local root certificate to %s\n" "$DESTINATION"
