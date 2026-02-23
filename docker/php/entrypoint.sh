#!/bin/sh

set -e

echo "🔧 Setting permissions for Laravel..."

# Storage + Cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# SQLite database
if [ -f /var/www/html/database/database.sqlite ]; then
    echo "🔧 Fixing SQLite permissions..."
    chown www-data:www-data /var/www/html/database/database.sqlite
    chmod 666 /var/www/html/database/database.sqlite
fi

echo "📦 Running composer install (if needed)..."
composer install --no-interaction --prefer-dist --optimize-autoloader || true

echo "🧹 Clearing Laravel caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

echo "🚀 Starting PHP-FPM..."
exec php-fpm
