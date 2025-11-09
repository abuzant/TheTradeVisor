#!/bin/bash

# TheTradeVisor Backend Instances Status Script
# Shows status of all backend instances and PHP-FPM pools

echo "📊 TheTradeVisor Multi-Instance Status"
echo "======================================"
echo ""

# Backend Nginx Instances
echo "🌐 Backend Nginx Instances:"
for i in 1 2 3 4; do
    if [ -f /run/nginx-backend-${i}.pid ]; then
        PID=$(cat /run/nginx-backend-${i}.pid)
        if kill -0 $PID 2>/dev/null; then
            # Get worker count
            WORKERS=$(pgrep -P $PID | wc -l)
            echo "  ✓ Backend ${i}: Running (PID: ${PID}, Port: 808${i}, Workers: ${WORKERS})"
        else
            echo "  ✗ Backend ${i}: Stopped (stale PID file)"
        fi
    else
        echo "  ✗ Backend ${i}: Stopped"
    fi
done
echo ""

# PHP-FPM Pools
echo "🐘 PHP-FPM Pools:"
for i in 1 2 3 4; do
    # Check if pool is listening
    if netstat -tln 2>/dev/null | grep -q "127.0.0.1:900${i}"; then
        # Try to get pool status if available
        POOL_STATUS=$(php-fpm8.3 -t 2>&1 | grep -i "pool${i}" || echo "active")
        echo "  ✓ Pool ${i}: Listening on 127.0.0.1:900${i}"
    else
        echo "  ✗ Pool ${i}: Not listening on 127.0.0.1:900${i}"
    fi
done
echo ""

# Load Balancer
echo "🔀 Load Balancer:"
if systemctl is-active --quiet nginx; then
    echo "  ✓ Nginx Load Balancer: Active"
else
    echo "  ✗ Nginx Load Balancer: Inactive"
fi
echo ""

# Test backend connectivity
echo "🔍 Backend Connectivity Test:"
for i in 1 2 3 4; do
    if curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:808${i} 2>/dev/null | grep -q "200\|301\|302"; then
        echo "  ✓ Backend ${i} (127.0.0.1:808${i}): Responding"
    else
        echo "  ✗ Backend ${i} (127.0.0.1:808${i}): Not responding"
    fi
done
echo ""

# Summary
RUNNING=$(ps aux | grep "nginx.*backend-" | grep -v grep | wc -l)
echo "📈 Summary: ${RUNNING} backend processes running"
echo ""
