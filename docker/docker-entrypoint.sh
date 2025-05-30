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

echo "Waiting for database connection..."
max_tries=30
count=0
while ! php artisan db:monitor >/dev/null 2>&1; do
    count=$((count + 1))
    if [ $count -gt $max_tries ]; then
        echo "Error: Could not connect to database after $max_tries attempts"
        exit 1
    fi
    echo "Waiting for database... ($count/$max_tries)"
    sleep 2
done

echo "Running database migrations and seeding..."
php artisan migrate --force --seed

echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ $# -eq 0 ]; then
    exec php artisan serve --host=0.0.0.0 --port=8000
else
    exec "$@"
fi
