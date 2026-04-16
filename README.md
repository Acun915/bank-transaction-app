## Requirements

- Docker Desktop

## Setup

```bash
composer install
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
npm install && npm run build
```

## Development

To run the frontend in development mode with hot-reload:

```bash
npm run dev
```

Keep both `sail up -d` and `npm run dev` running simultaneously.

Application is available at http://localhost

## Tests

```bash
# All tests
./vendor/bin/sail php vendor/bin/phpunit

# Feature tests only
./vendor/bin/sail php vendor/bin/phpunit --testsuite=Feature

# Unit tests only
./vendor/bin/sail php vendor/bin/phpunit --testsuite=Unit
```
## Stopping the Application

```bash
./vendor/bin/sail down
```

Data in MySQL is preserved between restarts.