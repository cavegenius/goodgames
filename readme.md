# Good Games

Good Games is a Laravel application for tracking a personal video game library.

## Requirements

- PHP 8.5 or newer, with the extensions required by Laravel
- Composer 2
- Node.js 22.12 or newer (or 24 or newer) and npm
- A database supported by Laravel

## Local setup

1. Install the PHP dependencies with `(cd src && composer install)`.
2. Copy `src/.env.example` to `src/.env` and set the database connection and any external service keys you use.
3. Generate an application key with `(cd src && php artisan key:generate)`.
4. Run database migrations with `(cd src && php artisan migrate)`.
5. Install frontend dependencies at the repository root with `npm ci` and compile assets with `npm run build`.
6. Start the application with `(cd src && php artisan serve)`.

Laravel application code and runtime configuration live in `src/`. Development scripts, test suites, Docker configuration, and Node tooling stay at the repository root. For frontend development with Vite's hot reload server, run `npm run dev` at the root in a second terminal while the Docker app is running.

## Run with Docker

Install and start Docker Desktop with Docker Compose v2, then run:

```sh
./scripts/start.sh
```

The script creates a private `.env.docker` file with a persistent Laravel app key and generated MySQL passwords, builds the local development image, starts MySQL, applies database migrations, and waits for the health check. The local Compose setup bind-mounts Laravel source into the app container, so PHP and Blade changes appear without rebuilding or restarting Docker. Open [http://localhost:8080](http://localhost:8080) when it reports ready. Run `npm run dev` in another terminal for frontend hot reload. Add the `STEAM_KEY`, `IGDB_KEY`, and `PSN_KEY` values you use to `.env.docker`, then restart with `./scripts/start.sh`.

The production image copies the Laravel app from `src/`; repository-level tests, scripts, datasets, and Node tooling stay out of the runtime image. It serves HTTP on port 8080 inside the container. Compose binds that port to localhost by default so a TLS reverse proxy on the host can forward traffic to it. For a public deployment, terminate HTTPS at a trusted load balancer or reverse proxy, set `APP_URL` to the public HTTPS URL, and keep `SESSION_SECURE_COOKIE=true`. The local development override explicitly uses HTTP and disables the secure-cookie flag for localhost. The included Compose stack uses MySQL 8.4 and persistent named volumes for the database and Laravel storage. `docker compose --env-file .env.docker -f compose.yaml down` stops the stack without deleting data; avoid `down -v` unless you intend to remove the database and uploaded files.

The `/api/healthz` endpoint returns `200` only when Laravel can connect to the configured database. Docker uses it as the app container's health check.

## Private home production deployment

The development stack (`compose.yaml` plus `compose.local.yaml`) is separate from the production stack (`compose.production.yaml`). Production has its own MySQL database and persistent storage volumes. A push to `master` triggers `.github/workflows/deploy-production.yml`, which builds and deploys on a GitHub Actions self-hosted runner in your home network. The runner connects outbound to GitHub, so this setup does not require router port forwarding.

Production is served at `https://yourgamelists.com` on your home network through a Caddy reverse proxy. The app container listens only on localhost for direct host access; Caddy listens on the server's LAN IP. The Caddy image includes the GoDaddy DNS provider module so it can obtain and renew a trusted certificate with DNS-01 validation.

### Prepare the home host

This Mac can host both development and production. Keep Docker/OrbStack running and reserve this computer's LAN address in your router so the private URL remains stable. Production uses its own Compose project, image tags, MySQL volume, and app-storage volume; the local development stack keeps its existing volumes.

The public A record can remain pointed at your public IP. Configure a local DNS override in your router so `yourgamelists.com` resolves to the server's reserved LAN IP for devices at home. If the router has no local DNS overrides, use a local DNS resolver such as Pi-hole or add a hosts-file entry on each device. **Do not forward ports 80, 443, or 8081 from the router.** The public DNS record alone does not make the site reachable from outside; the router must not forward requests to the server. With local DNS, devices at home use the domain and connect directly to the private LAN address.

On the production host, make a private environment file outside the repository:

```sh
mkdir -p "$HOME/.config/goodgames"
cp .env.production.example "$HOME/.config/goodgames/.env.production"
chmod 600 "$HOME/.config/goodgames/.env.production"
```

Edit that file. Set `APP_BIND_ADDRESS` to the host's reserved LAN IP and keep `APP_LOCAL_BIND_ADDRESS=127.0.0.1`. Set `APP_URL` and `DOMAIN` to `https://yourgamelists.com` and `yourgamelists.com`, respectively. Replace `APP_KEY` with `base64:` followed by the output of `openssl rand -base64 32`; set both MySQL passwords to separate values from `openssl rand -hex 32`. Create a GoDaddy API key and secret for DNS updates, set `GODADDY_API_TOKEN` to `key:secret`, and set `ACME_EMAIL` to your email. The key and secret let Caddy create and remove DNS TXT records for certificate validation; keep them private in this file, outside the repository. Keep the file readable only by the account running the GitHub runner. `SESSION_SECURE_COOKIE=true` is required because the site uses HTTPS.

The app requires email verification for library access. Configure `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, and `MAIL_ENCRYPTION` with a working SMTP account before creating your production account. The example's `MAIL_MAILER=log` is only useful for diagnosis and does not deliver verification messages.

Add a host firewall rule allowing TCP port 443 only from your trusted home subnet (for example, `192.168.1.0/24`). Port 8081 is bound to localhost, so only the host itself can access the app directly. Other devices on that Wi-Fi/subnet can reach the login page; the app's verified-account login protects game-management pages.

To seed production from your existing local dump, manual runs use `seed-data/yourgamelists.sql` from this checkout. For GitHub-triggered deployment, copy the dump to `$HOME/.config/goodgames/yourgamelists.sql` on this computer before the first `master` deployment. Keep it outside the repository because it contains personal data. The seed step verifies the production users, games, and filters tables are empty, makes a backup, imports the dump, and writes a marker into persistent app storage. It refuses to replace nonempty production data; later pushes skip an already-completed import. If the seed file is absent, the seed step logs that it skipped.

### Configure automatic deployment

In the GitHub repository, add a self-hosted Actions runner on this computer and give it the custom label `goodgames-home`. Run the runner as a service under the same account that owns the environment file and has permission to use Docker. Limit repository write access to people you trust: code pushed to `master` will execute on this host. The workflow checks out the pushed revision, then `scripts/deploy-production.sh` fetches a clean snapshot of `origin/master`, builds the production image, and applies database migrations. The same script can be run manually from this development checkout; it still deploys a clean snapshot of `origin/master`, never the current development files.

For the first deployment, create the production environment file on this computer, then run the script from this repository:

```sh
./scripts/deploy-production.sh
```

Open `https://yourgamelists.com` from a device on your home network. Production uses a separate Compose project and named volumes, so starting or resetting the local development stack does not reset production data.

The script fetches `origin/master` into a temporary source directory before building, even when launched from `develop`. So working-tree edits and the local development image cannot enter production. Back up both the production database and app storage to a different disk or backup service. Mount that destination on this computer, then run:

```sh
GOODGAMES_BACKUP_DIR=/path/to/backup-disk/goodgames ./scripts/backup-production.sh
```

The script creates a timestamped archive. Schedule it daily with your host's scheduler and keep an additional copy off the host; the Docker volumes themselves are not backups. Try restoring a backup before relying on it.

To build the app image separately for a registry:

```sh
docker build -t goodgames:latest .
```

The container runs migrations on startup by default. Set `RUN_MIGRATIONS=false` when migrations are managed as a separate deployment step.

## Tests

Start the local app container, then run the PHP suite in that same active container:

```sh
./scripts/start.sh
./scripts/run-tests-local.sh
```

The runner executes PHPUnit in the app container and accepts normal PHPUnit options, for example:

```sh
./scripts/run-tests-local.sh --filter GameLibraryTest
```

`start.sh` builds a local-only app image with PHPUnit and SQLite enabled; the production image remains the default Docker build target. PHPUnit uses in-memory SQLite. Run the frontend template and DOM interaction tests from the host with:

```sh
npm test
```

The JavaScript suite renders the real Handlebars templates in jsdom, triggers the jQuery UI handlers, and checks the resulting DOM and AJAX route/payload contracts. AJAX responses and browser-only plugins are stubbed; use the container PHPUnit suite for backend behavior.

## Local development data

Seed a predictable demo account and a small game library after migrating:

```sh
(cd src && php artisan db:seed-local)
```

Sign in with `demo@goodgames.test` and password `password`. This command is disabled when `APP_ENV=production`; rerunning it resets only that demo account's games and saved filters.

To save SQL dumps for repeated use, run the save tool with a path or follow its prompt:

```sh
./scripts/save-local-dataset.sh /path/to/yourgamelists.sql
```

Saved dumps go into the git-ignored root `seed-data/` folder. To choose one and restore a user from it, run:

```sh
./scripts/restore-local-data.sh
```

The picker confirms before replacing the entire local Docker database with the selected SQL dump. It restores every table and record in the dump, including users, games, and filters; it does not ask for a source user. You can pass a different folder of `.sql` dumps to the restore script. Keep dumps local; they can contain account details.

## Stack

- Laravel 13 and PHP 8.5
- Vite 8, Bootstrap 5, Sass, jQuery, Handlebars, and Vue 3
