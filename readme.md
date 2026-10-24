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

Laravel application code and runtime configuration live in `src/`. Development scripts, test suites, Docker configuration, and Node tooling stay at the repository root. For frontend development with Vite's hot reload server, run `npm run dev` at the root in a second terminal.

## Run with Docker

Install and start Docker Desktop with Docker Compose v2, then run:

```sh
./scripts/start.sh
```

The script creates a private `.env.docker` file with a persistent Laravel app key and generated MySQL passwords, builds the production image, starts MySQL, applies database migrations, and waits for the health check. Open [http://localhost:8080](http://localhost:8080) when it reports ready. Add the `STEAM_KEY`, `IGDB_KEY`, and `PSN_KEY` values you use to `.env.docker`, then restart with `docker compose --env-file .env.docker up -d`.

The production image copies the Laravel app from `src/`; repository-level tests, scripts, datasets, and Node tooling stay out of the runtime image. It serves HTTP on port 8080 with Nginx and PHP-FPM. For a public deployment, set `APP_URL` and `APP_PORT` in `.env.docker` and terminate HTTPS at your load balancer or reverse proxy. The included Compose stack uses MySQL 8.4 and persistent named volumes for the database and Laravel storage. `docker compose --env-file .env.docker down` stops the stack without deleting data; avoid `down -v` unless you intend to remove the database and uploaded files.

The `/api/healthz` endpoint returns `200` only when Laravel can connect to the configured database. Docker uses it as the app container's health check.

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

The picker asks for the source user ID (defaults to `4`, used by the supplied `yourgamelists.sql`) and confirms before replacing the demo account's data. You can pass a different folder of `.sql` dumps to the restore script. The importer reads only `INSERT` rows from `games` and `filters`, remaps them to the demo account, and ignores all other dump content. Keep dumps local; they can contain real account details. The direct Artisan form is also available from `src/`: `php artisan db:seed-local --dump=/path/to/file.sql --source-user=4`.

## Stack

- Laravel 13 and PHP 8.5
- Vite 8, Bootstrap 5, Sass, jQuery, Handlebars, and Vue 3
