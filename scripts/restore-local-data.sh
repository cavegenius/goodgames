#!/bin/sh
set -eu

ROOT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
DATA_DIR=${1:-"$ROOT_DIR/seed-data"}

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

printf 'Source user ID in this dump [4]: '
read source_user
source_user=${source_user:-4}
case "$source_user" in
    ''|*[!0-9]*) echo "Source user ID must be a number." >&2; exit 1 ;;
esac

printf 'This replaces the demo account data with %s (source user %s). Continue? [y/N] ' "$(basename "$dataset")" "$source_user"
read confirm
case "$confirm" in
    y|Y|yes|YES|Yes) ;;
    *) echo "Restore cancelled."; exit 0 ;;
esac

cd "$ROOT_DIR/src"
exec php artisan db:seed-local --dump="$dataset" --source-user="$source_user"
