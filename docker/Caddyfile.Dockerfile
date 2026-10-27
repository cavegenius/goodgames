FROM caddy:2-builder AS builder

RUN xcaddy build --with github.com/caddy-dns/godaddy --output /usr/bin/caddy

FROM caddy:2-alpine

COPY --from=builder /usr/bin/caddy /usr/bin/caddy
COPY docker/Caddyfile.production /etc/caddy/Caddyfile
