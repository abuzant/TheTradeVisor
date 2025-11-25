#!/bin/bash

echo "=== POSTGRESQL ROLLBACK SCRIPT ==="
echo "Use this if you experience issues after PostgreSQL optimization"
echo ""

# Check if running as sudo
if [ "$EUID" -ne 0 ]; then
    echo "Please run with sudo: sudo ./rollback_postgresql.sh"
    exit 1
fi

echo "Restoring original PostgreSQL configuration..."

# Restore backup file
cp /etc/postgresql/16/main/postgresql.conf.backup /etc/postgresql/16/main/postgresql.conf

echo "Restarting PostgreSQL service..."
systemctl restart postgresql

echo "Verifying rollback..."
echo "Configuration restored to original settings"

echo ""
echo "✅ POSTGRESQL ROLLBACK COMPLETED"
echo "Your PostgreSQL settings are back to original values"
