#!/bin/bash

# Check if .env file exists, if not copy from .env.example
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
fi

# Generate application key if not set
if [ -z "$(grep '^APP_KEY=' .env | cut -d '=' -f2)" ]; then
    echo "Generating application key..."
    php artisan key:generate
fi

# Execute the main command or default to artisan serve
if [ $# -eq 0 ]; then
    exec php artisan serve --host=0.0.0.0 --port=8000
else
    exec "$@"
fi
