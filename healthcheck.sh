#!/bin/bash

echo "=== TheTradeVisor Health Check ==="
echo ""
echo "Services Status:"
systemctl is-active nginx && echo "✓ Nginx: Running" || echo "✗ Nginx: Down"
systemctl is-active php8.3-fpm && echo "✓ PHP-FPM: Running" || echo "✗ PHP-FPM: Down"
systemctl is-active postgresql && echo "✓ PostgreSQL: Running" || echo "✗ PostgreSQL: Down"
systemctl is-active redis && echo "✓ Redis: Running" || echo "✗ Redis: Down"
systemctl is-active supervisor && echo "✓ Supervisor: Running" || echo "✗ Supervisor: Down"

echo ""
echo "Queue Workers:"
sudo supervisorctl status thetradevisor-worker:*

echo ""
echo "Disk Usage:"
df -h / | tail -1

echo ""
echo "Memory Usage:"
free -h | grep Mem
