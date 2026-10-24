#!/bin/sh
set -eu

ROOT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
cd "$ROOT_DIR"

if ! command -v docker >/dev/null 2>&1; then
    echo "Docker is required. Install and start Docker Desktop, then retry." >&2
    exit 1
fi

if ! docker compose version >/dev/null 2>&1; then
    echo "Docker Compose v2 is required (docker compose)." >&2
    exit 1
fi

ENV_FILE=.env.docker
if [ ! -f "$ENV_FILE" ]; then
    if ! command -v openssl >/dev/null 2>&1; then
        echo "OpenSSL is required to generate the app and database secrets." >&2
        exit 1
    fi

    umask 077
    APP_KEY="base64:$(openssl rand -base64 32 | tr -d '\n')"
    MYSQL_PASSWORD=$(openssl rand -hex 24)
    MYSQL_ROOT_PASSWORD=$(openssl rand -hex 24)
    cat > "$ENV_FILE" <<EOF
APP_NAME="Your Game Lists"
APP_KEY=$APP_KEY
APP_URL=http://localhost:8080
APP_PORT=8080
MYSQL_DATABASE=goodgames
MYSQL_USER=goodgames
MYSQL_PASSWORD=$MYSQL_PASSWORD
MYSQL_ROOT_PASSWORD=$MYSQL_ROOT_PASSWORD
MAIL_MAILER=log
STEAM_KEY=
IGDB_KEY=
PSN_KEY=
XBOX_KEY=
EOF
    echo "Created $ENV_FILE with generated app and database secrets. Keep it private."
fi

# The local container reads the working tree through bind mounts. Keep these
# application files readable to PHP-FPM and Nginx inside Docker Desktop.
chmod -R a+rX src/app src/config src/database src/public src/resources src/routes
chmod a+rX src/bootstrap/app.php src/artisan

docker compose --env-file "$ENV_FILE" -f compose.yaml -f compose.local.yaml up --build -d --wait --wait-timeout 180
echo "Good Games is ready at http://localhost:$(sed -n 's/^APP_PORT=//p' "$ENV_FILE" | tail -n 1)"
