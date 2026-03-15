#!/bin/bash

# TheTradeVisor Cache Refurbish Script - UPDATED
# Clears all caches: Laravel, Redis, Nginx, PHP-FPM, PostgreSQL, and rebuilds optimized caches
# Updated for current system configuration (Docker-based PostgreSQL/Redis, 3 PHP-FPM pools)

echo "🧹 TheTradeVisor Cache Refurbish Script (Updated)"
echo "=================================================="
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
php artisan optimize:clear
echo "✓ Laravel caches cleared"
echo ""

# Redis Cache (flush all)
echo "🔴 Flushing Redis cache..."
redis-cli FLUSHDB
echo "✓ Redis cache flushed"
echo ""

# Restart Redis container
echo "🔄 Restarting Redis container..."
docker restart redis
echo "✓ Redis container restarted"
echo ""

# Nginx FastCGI Cache
echo "🌐 Clearing Nginx FastCGI cache..."
$USE_SUDO rm -rf /var/cache/nginx/fastcgi/*
echo "✓ Nginx cache cleared"
echo ""

# PHP-FPM (restart service with 3 pools)
echo "🐘 Restarting PHP-FPM (thetradevisor + www + sarcastic pools)..."
$USE_SUDO systemctl restart php8.3-fpm
echo "✓ PHP-FPM restarted"
echo ""

# PostgreSQL (restart container)
echo "🐘 Restarting PostgreSQL container..."
docker restart postgres
echo "✓ PostgreSQL container restarted"
echo ""

# Nginx (restart web server)
echo "🌐 Restarting Nginx web server..."
$USE_SUDO systemctl restart nginx
echo "✓ Nginx restarted"
echo ""

# Note: Backend nginx instances removed - now using direct PHP-FPM connection

# Rebuild Optimized Caches
echo "⚡ Rebuilding optimized caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo "✓ Optimized caches rebuilt"
echo ""

# Restart Horizon (if running)
if pgrep -f "artisan horizon" > /dev/null; then
    echo "🔄 Restarting Horizon..."
    cd /vhosts/thetradevisor.com && php artisan horizon:terminate >> /dev/null 2>&1
    sleep 2
    cd /vhosts/thetradevisor.com && php artisan horizon >> /dev/null 2>&1 &
    echo "✓ Horizon restarted"
    echo ""
else
    echo "⚠ Horizon not running - skipping"
    echo ""
fi

# Log Rotation (force if needed)
echo "📋 Checking log rotation..."
if [ -f /etc/logrotate.d/laravel ]; then
    $USE_SUDO logrotate -f /etc/logrotate.d/laravel 2>/dev/null || true
    echo "✓ Log rotation checked/forced"
else
    echo "⚠ Log rotation not configured"
fi
echo ""

echo "✅ Refurbish complete!"
echo ""
echo "📊 System Status:"
echo "  - Laravel: Fresh caches rebuilt"
echo "  - Redis: Flushed & restarted (Docker container)"
echo "  - Nginx: Cleared & restarted"
echo "  - PHP-FPM: Restarted (thetradevisor + www + sarcastic pools)"
echo "  - PostgreSQL: Restarted (Docker container)"
echo "  - Horizon: Restarted (if running)"
echo "  - Log Rotation: Checked/forced"
echo "  - Optimized caches: Rebuilt"
echo ""
echo "💾 Note: Memory savings may vary based on current usage"
echo ""
echo "🚀 Your application is now running with clean caches and optimizations!"
