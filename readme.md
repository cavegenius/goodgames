# Good Games

Good Games is a Laravel application for tracking a personal video game library.

## Requirements

- PHP 8.5 or newer, with the extensions required by Laravel
- Composer 2
- Node.js 22.12 or newer (or 24 or newer) and npm
- A database supported by Laravel

## Local setup

1. Install the PHP dependencies with `composer install`.
2. Copy `.env.example` to `.env` and set the database connection and any external service keys you use.
3. Generate an application key with `php artisan key:generate`.
4. Run database migrations with `php artisan migrate`.
5. Install frontend dependencies with `npm ci` and compile assets with `npm run build`.
6. Start the application with `php artisan serve`.

For frontend development with Vite's hot reload server, run `npm run dev` in a second terminal.

## Tests

The test suite can run against an in-memory SQLite database:

```sh
DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan test
```

## Stack

- Laravel 13 and PHP 8.5
- Vite 8, Bootstrap 5, Sass, jQuery, Handlebars, and Vue 3
