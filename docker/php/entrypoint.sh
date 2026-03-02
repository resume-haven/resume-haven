#!/bin/sh

echo "🔧 Setting permissions for Laravel..."

# Storage + Cache
chown -R appuser:appgroup /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# SQLite database
if [ -f /var/www/html/database/database.sqlite ]; then
    echo "🔧 Fixing SQLite permissions..."
    chown appuser:appgroup /var/www/html/database/database.sqlite 2>/dev/null || true
    chmod 666 /var/www/html/database/database.sqlite 2>/dev/null || true
fi

# vendor-Verzeichnis
if [ -d /var/www/html/vendor ]; then
    chown -R appuser:appgroup /var/www/html/vendor 2>/dev/null || true
    chmod -R 755 /var/www/html/vendor 2>/dev/null || true
fi

echo "📦 Running composer install (if needed)..."
if [ ! -d /var/www/html/vendor ] || [ ! -f /var/www/html/vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader 2>&1 | head -20 || {
        echo "⚠️  Composer install had issues, but continuing..."
    }
else
    echo "✅ Vendor directory already exists"
fi

echo "🧹 Clearing Laravel caches..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

echo "🚀 Starting PHP-FPM..."
exec php-fpm
