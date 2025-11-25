#!/bin/bash

echo "=== REDIS ROLLBACK SCRIPT ==="
echo "Use this if you experience issues after Redis optimization"
echo ""

# Check if running as sudo
if [ "$EUID" -ne 0 ]; then
    echo "Please run with sudo: sudo ./rollback_redis.sh"
    exit 1
fi

echo "Restoring original Redis configuration..."

# Restore backup file
cp /etc/redis/redis.conf.backup /etc/redis/redis.conf

echo "Restarting Redis service..."
systemctl restart redis

echo "Verifying rollback..."
echo "Memory config after rollback:"
redis-cli config get maxmemory

echo "Current usage after rollback:"
redis-cli info memory | grep used_memory_human

echo ""
echo "✅ REDIS ROLLBACK COMPLETED"
echo "Your Redis settings are back to original values"
