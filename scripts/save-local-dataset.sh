#!/bin/sh
set -eu

ROOT_DIR=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
DATA_DIR="$ROOT_DIR/seed-data"

if [ "$#" -gt 1 ]; then
    echo "Usage: $0 [path/to/dump.sql]" >&2
    exit 2
fi

if [ "$#" -eq 1 ]; then
    source_file=$1
else
    printf 'Path to the SQL dump to save: '
    read source_file
fi

if [ ! -f "$source_file" ] || [ ! -r "$source_file" ]; then
    echo "Cannot read SQL dump: $source_file" >&2
    exit 1
fi

case "$source_file" in
    *.sql|*.SQL) ;;
    *) echo "The selected file must have a .sql extension." >&2; exit 1 ;;
esac

mkdir -p "$DATA_DIR"
filename=$(basename "$source_file")
destination="$DATA_DIR/$filename"

if [ -e "$destination" ]; then
    printf '%s already exists. Replace it? [y/N] ' "$filename"
    read confirm
    case "$confirm" in
        y|Y|yes|YES|Yes) ;;
        *) echo "Save cancelled."; exit 0 ;;
    esac
fi

cp "$source_file" "$destination"
chmod 600 "$destination"
printf 'Saved dataset: %s\n' "$destination"
printf 'Restore it with: ./scripts/restore-local-data.sh\n'
