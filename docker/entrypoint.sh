#!/bin/sh
set -eu

cd /var/www/html
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
fi

if [ ! -L public/storage ] && [ ! -e public/storage ]; then
    php artisan storage:link
fi

php artisan config:cache
php artisan view:cache

exec "$@"
