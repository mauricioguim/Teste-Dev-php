#!/bin/bash
set -e

if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
fi

if [ -z "$(grep '^APP_KEY=' .env | cut -d '=' -f2)" ]; then
    echo "Generating application key..."
    php artisan key:generate
fi

echo "Running migrations and seeders…"
php artisan migrate --force --seed

echo "Caching config, routes and views…"
php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ ! -d "vendor" ]; then
    echo "Installing dependencies via Composer…"
    composer install --no-interaction --optimize-autoloader
fi

if [ $# -eq 0 ]; then
    exec php artisan serve --host=0.0.0.0 --port="${APP_PORT:-8000}"
else
    exec "$@"
fi
