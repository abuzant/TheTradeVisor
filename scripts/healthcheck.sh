#!/bin/bash

echo "=== TheTradeVisor Health Check ==="
echo ""
echo "Services Status:"
systemctl is-active nginx && echo "✓ Nginx: Running" || echo "✗ Nginx: Down"
systemctl is-active php8.3-fpm && echo "✓ PHP-FPM: Running" || echo "✗ PHP-FPM: Down"

# Check PostgreSQL container
if docker ps --format "table {{.Names}}\t{{.Status}}" | grep -q "postgres.*Up"; then
    echo "✓ PostgreSQL: Running (Docker)"
else
    echo "✗ PostgreSQL: Down (Docker)"
fi

# Check Redis container
if docker ps --format "table {{.Names}}\t{{.Status}}" | grep -q "redis.*Up"; then
    echo "✓ Redis: Running (Docker)"
else
    echo "✗ Redis: Down (Docker)"
fi

# Check Telegram Bot container
if docker ps --format "table {{.Names}}\t{{.Status}}" | grep -q "telegram-bot.*Up"; then
    echo "✓ Telegram Bot: Running (Docker)"
else
    echo "⚠ Telegram Bot: Down (Docker)"
fi

echo ""
echo "Queue Workers:"
# Check if Horizon is running
if pgrep -f "artisan horizon" > /dev/null; then
    echo "✓ Horizon: Running"
else
    echo "⚠ Horizon: Not running"
fi

echo ""
echo "Disk Usage:"
df -h / | tail -1

echo ""
echo "Memory Usage:"
free -h | grep Mem

echo ""
echo "Docker Containers:"
docker ps --format "table {{.Names}}\t{{.Status}}" | grep -E "postgres|redis|telegram-bot"
