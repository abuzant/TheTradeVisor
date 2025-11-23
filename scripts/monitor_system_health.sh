#!/bin/bash

# TheTradeVisor System Health Monitor
# Prevents resource exhaustion and system hangs

LOG_FILE="/var/log/thetradevisor/health_monitor.log"
ALERT_FILE="/var/log/thetradevisor/alerts.log"
MAX_CPU=80
MAX_MEMORY=85
MAX_DISK_IO=1500
MAX_PHP_SLOW_REQUESTS=5

# Create log directory if it doesn't exist
mkdir -p /var/log/thetradevisor

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

alert() {
    local level="${2:-WARNING}"  # Default to WARNING if not specified
    local message="$1"
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $level: $message" | tee -a "$ALERT_FILE"
    
    # Send notification via Slack/Email
    /www/scripts/send_alert.sh "$level" "$message" "" &
}

# Check CPU usage
check_cpu() {
    CPU_USAGE=$(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | cut -d'%' -f1 | cut -d'.' -f1)
    if [ "$CPU_USAGE" -gt "$MAX_CPU" ]; then
        local details=$(ps aux --sort=-%cpu | head -10)
        alert "High CPU usage: ${CPU_USAGE}%" "CRITICAL"
        
        # Log top processes
        echo "$details" >> "$ALERT_FILE"
        
        # Check for runaway PHP processes
        SLOW_PHP=$(pgrep -c -f "php-fpm.*executing too slow" 2>/dev/null || echo 0)
        if [ "$SLOW_PHP" -gt "$MAX_PHP_SLOW_REQUESTS" ]; then
            alert "Too many slow PHP requests: $SLOW_PHP" "CRITICAL"
        fi
        
        return 1
    fi
    return 0
}

# Check memory usage
check_memory() {
    MEMORY_USAGE=$(free | grep Mem | awk '{print int($3/$2 * 100)}')
    if [ "$MEMORY_USAGE" -gt "$MAX_MEMORY" ]; then
        local details=$(ps aux --sort=-%mem | head -10)
        alert "High memory usage: ${MEMORY_USAGE}%" "CRITICAL"
        
        # Log top memory consumers
        echo "$details" >> "$ALERT_FILE"
        
        return 1
    fi
    return 0
}

# Check disk I/O
check_disk_io() {
    # Get read operations per second
    READ_OPS=$(iostat -x 1 2 | grep -A 1 "Device" | tail -1 | awk '{print int($4)}')
    
    if [ "$READ_OPS" -gt "$MAX_DISK_IO" ]; then
        alert "High disk I/O: ${READ_OPS} ops/s"
        
        # Log processes doing I/O
        iotop -b -n 1 -o >> "$ALERT_FILE" 2>/dev/null || true
        
        return 1
    fi
    return 0
}

# Check PostgreSQL health
check_postgres() {
    # Check for long-running queries (>30 seconds)
    LONG_QUERIES=$(sudo -u postgres psql -t -c "SELECT COUNT(*) FROM pg_stat_activity WHERE state = 'active' AND now() - query_start > interval '30 seconds';" 2>/dev/null || echo 0)
    
    if [ "$LONG_QUERIES" -gt 0 ]; then
        alert "Long-running PostgreSQL queries detected: $LONG_QUERIES" "WARNING"
        
        # Log the queries
        sudo -u postgres psql -c "SELECT pid, now() - query_start as duration, query FROM pg_stat_activity WHERE state = 'active' AND now() - query_start > interval '30 seconds';" >> "$ALERT_FILE" 2>/dev/null
        
        return 1
    fi
    return 0
}

# Check PHP-FPM pool status
check_php_fpm() {
    # Count slow requests in last minute
    SLOW_REQUESTS=$(journalctl -u php8.3-fpm --since "1 minute ago" | grep -c "executing too slow" || echo 0)
    SLOW_REQUESTS=$(echo "$SLOW_REQUESTS" | tr -d '\n' | tr -d ' ')
    
    if [ "$SLOW_REQUESTS" -gt "$MAX_PHP_SLOW_REQUESTS" ]; then
        alert "Too many slow PHP-FPM requests in last minute: $SLOW_REQUESTS"
        
        # Log PHP-FPM status
        systemctl status php8.3-fpm --no-pager >> "$ALERT_FILE"
        
        return 1
    fi
    return 0
}

# Check backend nginx instances (DEPRECATED - no longer using backend nginx)
check_backends() {
    # Backend nginx instances removed - now using direct PHP-FPM connection
    return 0
}

# Main monitoring loop
main() {
    log "Starting health check..."
    
    ISSUES=0
    
    check_cpu || ((ISSUES++))
    check_memory || ((ISSUES++))
    check_disk_io || ((ISSUES++))
    check_postgres || ((ISSUES++))
    check_php_fpm || ((ISSUES++))
    check_backends || ((ISSUES++))
    
    if [ "$ISSUES" -eq 0 ]; then
        log "All checks passed"
    else
        alert "Health check failed with $ISSUES issues"
        
        # If critical issues, consider emergency actions
        if [ "$ISSUES" -ge 3 ]; then
            alert "CRITICAL: Multiple system issues detected! ($ISSUES issues)" "CRITICAL"
            
            # Clear Laravel cache to reduce load
            cd /www && php artisan cache:clear >> "$ALERT_FILE" 2>&1
            
            # Restart PHP-FPM if needed
            if [ "${SLOW_REQUESTS:-0}" -gt 10 ]; then
                alert "Emergency: Restarting PHP-FPM due to excessive slow requests" "CRITICAL"
                systemctl restart php8.3-fpm
            fi
        fi
    fi
    
    log "Health check completed"
}

# Run the check
main
