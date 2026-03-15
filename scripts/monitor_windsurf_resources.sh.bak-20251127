#!/bin/bash
#
# Windsurf Resource Monitor
# Monitors Windsurf processes and kills them if they exceed resource limits
#
# Usage: Run via cron every 5 minutes
# */5 * * * * /www/scripts/monitor_windsurf_resources.sh >> /var/log/thetradevisor/windsurf_monitor.log 2>&1

# Configuration
MAX_MEMORY_MB=3000  # 3GB max per process
MAX_MEMORY_PERCENT=40  # 40% of total system memory
LOG_FILE="/var/log/thetradevisor/windsurf_monitor.log"
ALERT_EMAIL="ruslan@abuzant.com"

# Ensure log directory exists
mkdir -p "$(dirname "$LOG_FILE")"

# Get total system memory in MB
TOTAL_MEM_KB=$(grep MemTotal /proc/meminfo | awk '{print $2}')
TOTAL_MEM_MB=$((TOTAL_MEM_KB / 1024))
MAX_ALLOWED_MB=$((TOTAL_MEM_MB * MAX_MEMORY_PERCENT / 100))

# Use the smaller of the two limits
if [ $MAX_ALLOWED_MB -lt $MAX_MEMORY_MB ]; then
    LIMIT_MB=$MAX_ALLOWED_MB
else
    LIMIT_MB=$MAX_MEMORY_MB
fi

timestamp() {
    date "+%Y-%m-%d %H:%M:%S"
}

log() {
    echo "[$(timestamp)] $1"
}

# Check Windsurf processes
ps aux | grep -E "windsurf|language_server" | grep -v grep | while read -r line; do
    PID=$(echo "$line" | awk '{print $2}')
    USER=$(echo "$line" | awk '{print $1}')
    MEM_PERCENT=$(echo "$line" | awk '{print $4}')
    MEM_MB=$(echo "$line" | awk '{print $6}')
    MEM_MB=$((MEM_MB / 1024))
    CMD=$(echo "$line" | awk '{for(i=11;i<=NF;i++) printf $i" "; print ""}')
    
    # Check if memory exceeds limit
    if [ $MEM_MB -gt $LIMIT_MB ]; then
        log "WARNING: Process $PID exceeds memory limit ($MEM_MB MB > $LIMIT_MB MB)"
        log "  User: $USER"
        log "  Command: $CMD"
        log "  Action: Killing process"
        
        # Kill the process
        kill -9 $PID
        
        # Send alert email
        echo "Windsurf process $PID was killed for excessive memory usage ($MEM_MB MB)" | \
            mail -s "ALERT: Windsurf Process Killed on thetradevisor.com" "$ALERT_EMAIL" 2>/dev/null || true
        
        log "  Result: Process killed"
    fi
done

# Check total Windsurf memory usage
TOTAL_WINDSURF_MEM=0
WINDSURF_PROCESSES=0

while read -r mem_kb; do
    TOTAL_WINDSURF_MEM=$((TOTAL_WINDSURF_MEM + mem_kb))
    WINDSURF_PROCESSES=$((WINDSURF_PROCESSES + 1))
done < <(ps aux | grep -E "windsurf|language_server" | grep -v grep | awk '{print $6}')

if [ $WINDSURF_PROCESSES -gt 0 ]; then
    TOTAL_WINDSURF_MB=$((TOTAL_WINDSURF_MEM / 1024))
    TOTAL_WINDSURF_PERCENT=$((TOTAL_WINDSURF_MB * 100 / TOTAL_MEM_MB))
    
    log "INFO: Windsurf processes: $WINDSURF_PROCESSES, Total memory: ${TOTAL_WINDSURF_MB}MB (${TOTAL_WINDSURF_PERCENT}%)"
    
    # Alert if total exceeds 50%
    if [ $TOTAL_WINDSURF_PERCENT -gt 50 ]; then
        log "WARNING: Total Windsurf memory usage exceeds 50% of system memory"
        echo "Total Windsurf memory usage: ${TOTAL_WINDSURF_MB}MB (${TOTAL_WINDSURF_PERCENT}%)" | \
            mail -s "WARNING: High Windsurf Memory Usage on thetradevisor.com" "$ALERT_EMAIL" 2>/dev/null || true
    fi
fi
