#!/bin/bash

# Setup System Monitoring for TheTradeVisor
# This script configures cron jobs and system monitoring

echo "Setting up TheTradeVisor system monitoring..."

# Make health monitor executable
chmod +x /vhosts/thetradevisor.com/scripts/monitor_system_health.sh

# Create log directory
sudo mkdir -p /var/log/thetradevisor
sudo chown ubuntu:www-data /var/log/thetradevisor
sudo chmod 775 /var/log/thetradevisor

# Install required tools if not present
echo "Checking required tools..."

if ! command -v iostat &> /dev/null; then
    echo "Installing sysstat (for iostat)..."
    sudo apt-get update && sudo apt-get install -y sysstat
fi

if ! command -v nc &> /dev/null; then
    echo "Installing netcat..."
    sudo apt-get install -y netcat
fi

# Setup cron job for health monitoring (every 2 minutes)
CRON_JOB="*/2 * * * * /vhosts/thetradevisor.com/scripts/monitor_system_health.sh"

# Check if cron job already exists
if ! crontab -l 2>/dev/null | grep -q "monitor_system_health.sh"; then
    echo "Adding cron job for health monitoring..."
    (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
    echo "✓ Cron job added"
else
    echo "✓ Cron job already exists"
fi

# Configure PostgreSQL statement timeout
echo "Configuring PostgreSQL query timeout..."
docker exec postgres psql -U ${POSTGRES_USER:-postgres} -d thetradevisor -c "ALTER DATABASE thetradevisor SET statement_timeout = '30s';" 2>/dev/null || echo "Note: PostgreSQL timeout already configured or requires manual setup"

# Configure PHP-FPM slow log threshold
echo "Configuring PHP-FPM slow request logging..."
PHP_FPM_CONF="/etc/php/8.3/fpm/pool.d/thetradevisor.conf"

if [ -f "$PHP_FPM_CONF" ]; then
    # Check if slow log is already configured
    if ! grep -q "^request_slowlog_timeout" "$PHP_FPM_CONF"; then
        echo "request_slowlog_timeout = 5s" | sudo tee -a "$PHP_FPM_CONF"
        echo "slowlog = /var/log/php8.3-fpm-slow.log" | sudo tee -a "$PHP_FPM_CONF"
        echo "✓ PHP-FPM slow log configured"
        
        # Restart PHP-FPM
        sudo systemctl reload php8.3-fpm
    else
        echo "✓ PHP-FPM slow log already configured"
    fi
fi

# Create logrotate configuration
echo "Setting up log rotation..."
sudo tee /etc/logrotate.d/thetradevisor > /dev/null <<EOF
/var/log/thetradevisor/*.log {
    daily
    rotate 7
    compress
    delaycompress
    missingok
    notifempty
    create 0644 ubuntu www-data
}
EOF

echo "✓ Log rotation configured"

# Test the health monitor
echo ""
echo "Running initial health check..."
/vhosts/thetradevisor.com/scripts/monitor_system_health.sh

echo ""
echo "=========================================="
echo "✓ Monitoring setup complete!"
echo "=========================================="
echo ""
echo "Logs location: /var/log/thetradevisor/"
echo "Health monitor runs every 2 minutes"
echo ""
echo "To view logs:"
echo "  tail -f /var/log/thetradevisor/health_monitor.log"
echo "  tail -f /var/log/thetradevisor/alerts.log"
echo ""
