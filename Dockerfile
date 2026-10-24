# Build the frontend assets in a dedicated stage so Node is not part of the
# production runtime image.
FROM node:22-alpine AS frontend

WORKDIR /build
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY src/resources ./src/resources
COPY vite.config.js ./
COPY src/public/fonts ./src/public/fonts
RUN npm run build

FROM php:8.5-fpm-alpine AS runtime

# The PHP 8.5 image already includes DOM, Lexbor, cURL, mbstring, XML, and OPcache.
# Rebuilding DOM against Alpine's separate Lexbor package can introduce a version mismatch.
RUN apk add --no-cache nginx supervisor icu-libs libzip \
    && apk add --no-cache --virtual .php-build-deps $PHPIZE_DEPS icu-dev libzip-dev \
    && docker-php-ext-install -j"$(nproc)" bcmath exif intl pcntl pdo_mysql zip \
    && apk del .php-build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY src/composer.json src/composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts --no-autoloader

COPY src/ ./
COPY --from=frontend /build/src/public/build ./public/build
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/container-entrypoint

RUN composer dump-autoload --no-dev --optimize --no-interaction \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R a+rX /var/www/html \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x /usr/local/bin/container-entrypoint

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD wget -q -O /dev/null http://127.0.0.1:8080/api/healthz || exit 1

ENTRYPOINT ["/usr/local/bin/container-entrypoint"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
