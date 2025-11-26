#!/bin/bash

echo "=== PHP-FPM ROLLBACK SCRIPT ==="
echo "Use this if you experience performance issues after optimization"
echo ""

# Check if running as sudo
if [ "$EUID" -ne 0 ]; then
    echo "Please run with sudo: sudo ./rollback_php_fpm.sh"
    exit 1
fi

echo "Restoring original PHP-FPM configurations..."

# Restore backup files
cp /etc/php/8.3/fpm/pool.d/www.conf.backup /etc/php/8.3/fpm/pool.d/www.conf
cp /etc/php/8.3/fpm/pool.d/pool1.conf.backup /etc/php/8.3/fpm/pool.d/pool1.conf
cp /etc/php/8.3/fpm/pool.d/pool2.conf.backup /etc/php/8.3/fpm/pool.d/pool2.conf
cp /etc/php/8.3/fpm/pool.d/pool3.conf.backup /etc/php/8.3/fpm/pool.d/pool3.conf
cp /etc/php/8.3/fpm/pool.d/pool4.conf.backup /etc/php/8.3/fpm/pool.d/pool4.conf

echo "Restarting PHP-FPM service..."
systemctl restart php8.3-fpm

echo "Verifying rollback..."
echo "Process count after rollback:"
ps aux | grep php-fpm | grep "pool" | wc -l

echo "Memory usage after rollback:"
ps aux | grep php-fpm | awk '{sum+=$6} END {print "Total: " sum/1024 " MB"}'

echo ""
echo "✅ ROLLBACK COMPLETED"
echo "Your PHP-FPM settings are back to original values"
