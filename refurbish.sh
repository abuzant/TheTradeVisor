#!/bin/bash

# TheTradeVisor Cache Refurbish Script
# Clears all caches: Laravel, Redis, Nginx, PHP-FPM, and rebuilds optimized caches

echo "🧹 TheTradeVisor Cache Refurbish Script"
echo "========================================"
echo ""

# Check if running as root for sudo commands
if [ "$EUID" -ne 0 ]; then 
    USE_SUDO="sudo"
else
    USE_SUDO=""
fi

# Laravel Application Caches
echo "📦 Clearing Laravel caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
echo "✓ Laravel caches cleared"
echo ""

# Redis Cache
echo "🔴 Flushing Redis cache..."
redis-cli FLUSHDB
echo "✓ Redis cache flushed"
echo ""

# Nginx FastCGI Cache
echo "🌐 Clearing Nginx FastCGI cache..."
$USE_SUDO rm -rf /var/cache/nginx/fastcgi/*
echo "✓ Nginx cache cleared"
echo ""

# PHP-FPM
echo "🐘 Restarting PHP-FPM..."
$USE_SUDO systemctl restart php8.3-fpm
echo "✓ PHP-FPM restarted"
echo ""

# Nginx
echo "🌐 Reloading Nginx..."
$USE_SUDO systemctl reload nginx
echo "✓ Nginx reloaded"
echo ""

# Rebuild Optimized Caches
echo "⚡ Rebuilding optimized caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo "✓ Optimized caches rebuilt"
echo ""

# Optional: Restart Horizon (if running)
if pgrep -f "artisan horizon" > /dev/null; then
    echo "🔄 Restarting Horizon..."
    $USE_SUDO supervisorctl restart horizon
    echo "✓ Horizon restarted"
    echo ""
fi

echo "✅ Refurbish complete!"
echo ""
echo "📊 Cache Status:"
echo "  - Laravel: Fresh"
echo "  - Redis: Empty"
echo "  - Nginx: Empty"
echo "  - PHP-FPM: Restarted"
echo "  - Optimized caches: Rebuilt"
echo ""
echo "🚀 Your application is now running with clean caches!"
