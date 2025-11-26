#!/bin/bash

echo "=== DISABLE LOG ROTATION SCRIPT ==="
echo "Use this if you experience issues with log rotation"
echo ""

# Check if running as sudo
if [ "$EUID" -ne 0 ]; then
    echo "Please run with sudo: sudo ./disable_log_rotation.sh"
    exit 1
fi

echo "Disabling Laravel log rotation..."

# Remove the logrotate configuration
if [ -f /etc/logrotate.d/laravel ]; then
    mv /etc/logrotate.d/laravel /etc/logrotate.d/laravel.disabled
    echo "Log rotation configuration disabled"
else
    echo "No log rotation configuration found"
fi

echo ""
echo "✅ LOG ROTATION DISABLED"
echo "Logs will now grow without rotation"
echo "To re-enable: sudo mv /etc/logrotate.d/laravel.disabled /etc/logrotate.d/laravel"
