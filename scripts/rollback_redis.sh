#!/bin/bash

echo "=== REDIS ROLLBACK SCRIPT ==="
echo "Use this if you experience issues after Redis optimization"
echo ""

echo "Restarting Redis container..."
docker restart redis

echo "Waiting for Redis to be ready..."
sleep 2
docker exec redis redis-cli ping && echo "✅ Redis is ready" || echo "⚠ Redis may still be starting"

echo ""
echo "Verifying rollback..."
echo "Memory config after rollback:"
redis-cli config get maxmemory

echo "Current usage after rollback:"
redis-cli info memory | grep used_memory_human

echo ""
echo "✅ REDIS ROLLBACK COMPLETED"
