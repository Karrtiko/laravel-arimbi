#!/bin/sh
set -e

# Copy .env if missing
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

# Install dependencies if missing (because volume mount hides image vendor)
if [ ! -d vendor ]; then
    echo "Installing Composer dependencies..."
    composer install --optimize-autoloader --no-dev
    php artisan key:generate
fi

# Create database file if not exists
if [ ! -f database/database.sqlite ]; then
    echo "Creating database.sqlite..."
    touch database/database.sqlite
fi

# Ensure permissions
# Relaxed permissions for Linux/Docker compatibility (SQLite needs directory write access)
chmod -R 777 database
chmod -R 777 storage
chmod -R 777 bootstrap/cache

if [ -f database/database.sqlite ]; then
    chmod 666 database/database.sqlite
fi
chown -R www-data:www-data database
chown -R www-data:www-data storage

# Cache configuration
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

# Run Migrations
echo "Running migrations..."
php artisan migrate --force

# Start Nginx
echo "Starting Nginx..."
service nginx start

# Start PHP-FPM
echo "Starting PHP-FPM..."
php-fpm
