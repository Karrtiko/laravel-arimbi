#!/bin/sh
set -e

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create database file if not exists
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

# Ensure permissions (SQLite and Storage)
# We set permissions to 666/777 to avoid issues in simple Docker setups
# Typically www-data should own it, but bind mounts can be tricky
chown -R www-data:www-data database
chown -R www-data:www-data storage
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod 664 database/database.sqlite

# Run Migrations (Safe in prod, adds tables only)
php artisan migrate --force

# Start Nginx
service nginx start

# Start PHP-FPM
php-fpm
