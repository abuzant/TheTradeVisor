#!/bin/bash

set -euo pipefail

DRY_RUN=false
if [[ "${1:-}" == "--dry-run" ]]; then
    DRY_RUN=true
fi

LOG_DIR="/var/log/thetradevisor"
LOG_FILE="${LOG_DIR}/windsurf_restart.log"

mkdir -p "$LOG_DIR"
chmod 755 "$LOG_DIR"

timestamp() {
    date "+%Y-%m-%d %H:%M:%S"
}

log() {
    echo "[$(timestamp)] $1" | tee -a "$LOG_FILE" >/dev/null
}

log "Starting scheduled Windsurf restart"

# Gracefully terminate Windsurf language servers and related processes
if pgrep -f "windsurf/bin/language_server" >/dev/null; then
    if [ "$DRY_RUN" = true ]; then
        log "[dry-run] Would send SIGTERM to Windsurf language servers"
    else
        pkill -f "windsurf/bin/language_server" || true
        log "Sent SIGTERM to Windsurf language servers"
    fi
fi

if pgrep -f "\\.windsurf-server/bin/.*/node" >/dev/null; then
    if [ "$DRY_RUN" = true ]; then
        log "[dry-run] Would send SIGTERM to Windsurf node worker processes"
    else
        pkill -f "\\.windsurf-server/bin/.*/node" || true
        log "Sent SIGTERM to Windsurf node worker processes"
    fi
fi

if [ "$DRY_RUN" = true ]; then
    log "[dry-run] Skipping wait/force terminate"
else
    sleep 5

    if pgrep -f "windsurf" >/dev/null; then
        log "Processes still running after grace period; forcing termination"
        pkill -9 -f "windsurf" || true
        sleep 2
    fi

    if pgrep -f "windsurf" >/dev/null; then
        REMAINING=$(pgrep -fl "windsurf" | tr '\n' '; ')
        log "WARNING: Some Windsurf processes still running: ${REMAINING}"
    else
        log "Windsurf processes successfully terminated"
    fi
fi

log "Scheduled Windsurf restart complete"
