#!/bin/bash
# ==========================================
# Your App Name - Docker Entrypoint
# ==========================================

set -e

echo "=========================================="
echo " Starting Your App Name..."
echo "=========================================="

# Wait for database
echo "Waiting for MySQL..."
sleep 5

# Run migrations
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Clear and cache
echo "Optimizing..."
php artisan config:cache --no-interaction || true
php artisan route:cache --no-interaction || true
php artisan view:cache --no-interaction || true
php artisan event:cache --no-interaction || true

# Set permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "=========================================="
echo " Setup complete!"
echo "=========================================="

# Start supervisor (runs nginx + php-fpm)
exec /usr/bin/supervisord -n
