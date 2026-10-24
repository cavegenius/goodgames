#!/bin/sh
set -eu

ROOT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
DATA_DIR=${1:-"$ROOT_DIR/seed-data"}
ENV_FILE="$ROOT_DIR/.env.docker"
case "$DATA_DIR" in
    /*) ;;
    *) DATA_DIR="$(pwd)/$DATA_DIR" ;;
esac

if [ ! -d "$DATA_DIR" ]; then
    echo "Dataset folder not found: $DATA_DIR" >&2
    echo "Create it or pass a folder containing .sql files." >&2
    exit 1
fi

set --
for file in "$DATA_DIR"/*.sql "$DATA_DIR"/*.SQL; do
    [ -f "$file" ] && set -- "$@" "$file"
done

if [ "$#" -eq 0 ]; then
    echo "No SQL datasets found in: $DATA_DIR" >&2
    echo "Copy a .sql dump into that folder, then run this script again." >&2
    exit 1
fi

echo "Available SQL datasets:"
index=1
for file do
    printf '  %s) %s\n' "$index" "$(basename "$file")"
    index=$((index + 1))
done

printf 'Choose a dataset [1-%s]: ' "$#"
read choice
case "$choice" in
    ''|*[!0-9]*) echo "Enter a number from 1 to $#." >&2; exit 1 ;;
esac
if [ "$choice" -lt 1 ] || [ "$choice" -gt "$#" ]; then
    echo "Enter a number from 1 to $#." >&2
    exit 1
fi

index=1
for file do
    if [ "$index" -eq "$choice" ]; then
        dataset=$file
        break
    fi
    index=$((index + 1))
done

printf 'This erases and replaces the entire local Docker database with %s. Continue? [y/N] ' "$(basename "$dataset")"
read confirm
case "$confirm" in
    y|Y|yes|YES|Yes) ;;
    *) echo "Restore cancelled."; exit 0 ;;
esac

if [ ! -f "$ENV_FILE" ]; then
    echo "No local Docker environment found. Start the app first with ./scripts/start.sh." >&2
    exit 1
fi
if ! command -v docker >/dev/null 2>&1 || ! docker info >/dev/null 2>&1; then
    echo "Docker Desktop must be running. Start it and try again." >&2
    exit 1
fi

cd "$ROOT_DIR"
if ! docker compose --env-file "$ENV_FILE" -f compose.yaml -f compose.local.yaml ps --status running --services | grep -qx database; then
    echo "The local database container is not running. Start it with ./scripts/start.sh." >&2
    exit 1
fi

# The configured database name is controlled by this local Compose setup.
docker compose --env-file "$ENV_FILE" -f compose.yaml -f compose.local.yaml exec -T database sh -c '
    case "$MYSQL_DATABASE" in
        ""|*[!A-Za-z0-9_]*) echo "Unsafe local database name: $MYSQL_DATABASE" >&2; exit 1 ;;
    esac
    mysql -uroot -p"$MYSQL_ROOT_PASSWORD" -e "DROP DATABASE IF EXISTS \`$MYSQL_DATABASE\`; CREATE DATABASE \`$MYSQL_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
'
docker compose --env-file "$ENV_FILE" -f compose.yaml -f compose.local.yaml exec -T database sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' < "$dataset"
printf 'Restored full dataset from %s into the local Docker database.\n' "$(basename "$dataset")"
