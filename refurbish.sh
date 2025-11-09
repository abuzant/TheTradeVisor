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

# Redis Cache (only DB 1, preserve sessions in DB 2)
echo "🔴 Flushing Redis cache (DB 1 only)..."
redis-cli -n 1 FLUSHDB
echo "✓ Redis cache flushed (sessions preserved)"
echo ""

# Nginx FastCGI Cache
echo "🌐 Clearing Nginx FastCGI cache..."
$USE_SUDO rm -rf /var/cache/nginx/fastcgi/*
echo "✓ Nginx cache cleared"
echo ""

# PHP-FPM (restart main service to reload all pools)
echo "🐘 Restarting PHP-FPM (all 4 pools)..."
$USE_SUDO systemctl restart php8.3-fpm
echo "✓ PHP-FPM restarted (pools 1-4)"
echo ""

# Backend Nginx instances
echo "🌐 Restarting backend Nginx instances..."
for i in 1 2 3 4; do
    # Stop existing instance
    if [ -f /run/nginx-backend-${i}.pid ]; then
        $USE_SUDO kill -QUIT $(cat /run/nginx-backend-${i}.pid) 2>/dev/null || true
        sleep 0.5
    fi
    # Start new instance
    $USE_SUDO nginx -c /etc/nginx/backends/nginx-backend-${i}-master.conf
done
echo "✓ Backend instances restarted"
echo ""

# Load Balancer Nginx
echo "🌐 Reloading Load Balancer Nginx..."
$USE_SUDO systemctl reload nginx
echo "✓ Load balancer reloaded"
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
echo "  - PHP-FPM: Restarted (4 pools)"
echo "  - Backend Instances: Restarted (4 instances)"
echo "  - Load Balancer: Reloaded"
echo "  - Optimized caches: Rebuilt"
echo ""
echo "🚀 Your application is now running with clean caches across all instances!"
