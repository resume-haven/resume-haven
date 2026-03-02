#!/bin/sh

set -e

echo "🔧 Setting permissions for Laravel..."

# Storage + Cache
chown -R appuser:appgroup /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# SQLite database
if [ -f /var/www/html/database/database.sqlite ]; then
    echo "🔧 Fixing SQLite permissions..."
    chown appuser:appgroup /var/www/html/database/database.sqlite
    chmod 666 /var/www/html/database/database.sqlite
fi

# vendor-Verzeichnis
if [ -d /var/www/html/vendor ]; then
    chown -R appuser:appgroup /var/www/html/vendor
    chmod -R 755 /var/www/html/vendor
fi

echo "📦 Running composer install (if needed)..."
composer install --no-interaction --prefer-dist --optimize-autoloader || true

echo "🧹 Clearing Laravel caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

echo "🚀 Starting PHP-FPM as appuser..."
exec gosu appuser php-fpm
echo "🚀 Starting PHP-FPM..."
exec php-fpm
