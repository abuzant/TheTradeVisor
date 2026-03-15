#!/bin/bash

# TheTradeVisor Lock Monitor and Prevention Script
# Monitors for database lock contention and takes preventive action

LOG_FILE="/var/log/thetradevisor/lock_monitor.log"
ALERT_FILE="/var/log/thetradevisor/alerts.log"
MAX_LOCK_WAIT_TIME=30  # seconds
MAX_CONCURRENT_LOCKS=5

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# Check for problematic locks
check_locks() {
    # Count waiting locks
    WAITING_LOCKS=$(docker exec postgres psql -U pgsql_user -d thetradevisor_app -t -c "SELECT COUNT(*) FROM pg_locks WHERE NOT granted;" 2>/dev/null | tr -d ' ')
    
    # Ensure we have a valid number
    WAITING_LOCKS=${WAITING_LOCKS:-0}
    if ! [[ "$WAITING_LOCKS" =~ ^[0-9]+$ ]]; then
        WAITING_LOCKS=0
    fi
    
    if [ "$WAITING_LOCKS" -gt "$MAX_CONCURRENT_LOCKS" ]; then
        log "WARNING: High number of waiting locks detected: $WAITING_LOCKS"
        
        # Get details of long-running queries
        docker exec postgres psql -U pgsql_user -d thetradevisor_app -c "SELECT pid, age(clock_timestamp(), query_start) as duration, query FROM pg_stat_activity WHERE pid IN (SELECT pid FROM pg_locks WHERE NOT granted) AND age(clock_timestamp(), query_start) > interval '${MAX_LOCK_WAIT_TIME} seconds';" >> "$ALERT_FILE" 2>/dev/null
        
        # If locks are waiting too long, terminate the oldest ones
        if [ "$WAITING_LOCKS" -gt 10 ]; then
            log "CRITICAL: Too many waiting locks, terminating oldest processes"
            
            # Terminate oldest processes waiting on locks
            docker exec postgres psql -U pgsql_user -d thetradevisor_app -c "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE pid IN (SELECT pid FROM pg_locks WHERE NOT granted) AND age(clock_timestamp(), query_start) > interval '${MAX_LOCK_WAIT_TIME} seconds' ORDER BY query_start LIMIT 5;" >> "$LOG_FILE" 2>&1
            
            # Pause Horizon temporarily to prevent new jobs
            cd /vhosts/thetradevisor.com && php artisan horizon:pause >> "$LOG_FILE" 2>&1
            
            # Wait a bit then resume
            sleep 10
            cd /vhosts/thetradevisor.com && php artisan horizon:continue >> "$LOG_FILE" 2>&1
            
            echo "[$(date '+%Y-%m-%d %H:%M:%S')] CRITICAL: Terminated stuck processes and restarted queue workers" | tee -a "$ALERT_FILE"
        fi
    fi
    
    # Check for specific trading_accounts lock contention
    TRADING_LOCKS=$(docker exec postgres psql -U pgsql_user -d thetradevisor_app -t -c "SELECT COUNT(*) FROM pg_locks l JOIN pg_class c ON l.relation = c.oid WHERE c.relname = 'trading_accounts' AND NOT l.granted;" 2>/dev/null | tr -d ' ')
    
    # Ensure we have a valid number
    TRADING_LOCKS=${TRADING_LOCKS:-0}
    if ! [[ "$TRADING_LOCKS" =~ ^[0-9]+$ ]]; then
        TRADING_LOCKS=0
    fi
    
    if [ "$TRADING_LOCKS" -gt 3 ]; then
        log "WARNING: High lock contention on trading_accounts table: $TRADING_LOCKS"
        
        # Get the PIDs of processes holding locks too long
        docker exec postgres psql -U pgsql_user -d thetradevisor_app -c "SELECT pid, query FROM pg_stat_activity WHERE pid IN (SELECT pid FROM pg_locks WHERE relation = (SELECT oid FROM pg_class WHERE relname = 'trading_accounts') AND granted = true) AND age(clock_timestamp(), query_start) > interval '60 seconds';" >> "$LOG_FILE" 2>&1
    fi
}

# Main monitoring loop
main() {
    log "Starting lock monitor check..."
    check_locks
    log "Lock monitor check completed"
}

# Run the check
main
