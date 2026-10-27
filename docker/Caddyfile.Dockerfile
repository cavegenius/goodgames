FROM caddy:2-alpine

COPY docker/Caddyfile.production /etc/caddy/Caddyfile
