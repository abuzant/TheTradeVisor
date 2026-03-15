#!/bin/bash

# TheTradeVisor JSON File Cleanup Script
# Deletes JSON files older than 7 days from storage/app/raw_data/
# Files are organized by user ID and contain date in filename: YYYY-MM-DD_HHMMSS_*.json

RETENTION_DAYS=7
BASE_DIR="/vhosts/thetradevisor.com/storage/app/raw_data"
LOG_FILE="/var/log/thetradevisor/json_cleanup.log"
DRY_RUN=${1:-false}

# Create log directory if needed
mkdir -p "$(dirname "$LOG_FILE")"

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# Calculate cutoff date (7 days ago)
CUTOFF_DATE=$(date -d "$RETENTION_DAYS days ago" '+%Y-%m-%d')
CUTOFF_TIMESTAMP=$(date -d "$RETENTION_DAYS days ago" '+%s')

log "Starting JSON cleanup (retention: $RETENTION_DAYS days, cutoff: $CUTOFF_DATE)"
if [ "$DRY_RUN" = "true" ]; then
    log "DRY RUN MODE - No files will be deleted"
fi

# Counters
TOTAL_FILES=0
DELETED_FILES=0
DELETED_SIZE=0
ERRORS=0

# Find all JSON files
while IFS= read -r filepath; do
    ((TOTAL_FILES++))
    
    # Extract filename
    filename=$(basename "$filepath")
    
    # Extract date from filename (format: YYYY-MM-DD_HHMMSS_*.json)
    if [[ $filename =~ ^([0-9]{4}-[0-9]{2}-[0-9]{2})_ ]]; then
        file_date="${BASH_REMATCH[1]}"
        file_timestamp=$(date -d "$file_date" '+%s' 2>/dev/null)
        
        if [ $? -ne 0 ]; then
            log "WARNING: Could not parse date from filename: $filename"
            ((ERRORS++))
            continue
        fi
        
        # Check if file is older than cutoff
        if [ "$file_timestamp" -lt "$CUTOFF_TIMESTAMP" ]; then
            # Get file size before deletion (Linux stat format)
            file_size=$(stat -c "%s" "$filepath" 2>/dev/null || echo 0)
            
            if [ "$DRY_RUN" = "true" ]; then
                log "Would delete: $filepath (date: $file_date, size: $file_size bytes)"
                ((DELETED_FILES++))
                DELETED_SIZE=$((DELETED_SIZE + file_size))
            else
                if rm -f "$filepath" 2>/dev/null; then
                    ((DELETED_FILES++))
                    DELETED_SIZE=$((DELETED_SIZE + file_size))
                    
                    # Log every 1000 deletions
                    if [ $((DELETED_FILES % 1000)) -eq 0 ]; then
                        log "Progress: Deleted $DELETED_FILES files so far..."
                    fi
                else
                    log "ERROR: Failed to delete $filepath"
                    ((ERRORS++))
                fi
            fi
        fi
    else
        log "WARNING: Filename doesn't match expected pattern: $filename"
        ((ERRORS++))
    fi
    
    # Progress indicator every 10000 files
    if [ $((TOTAL_FILES % 10000)) -eq 0 ]; then
        log "Progress: Scanned $TOTAL_FILES files..."
    fi
    
done < <(find "$BASE_DIR" -name "*.json" -type f 2>/dev/null)

# Convert size to human readable
DELETED_SIZE_MB=$((DELETED_SIZE / 1024 / 1024))

# Summary
log "=========================================="
log "Cleanup Summary:"
log "  Total files scanned: $TOTAL_FILES"
log "  Files deleted: $DELETED_FILES"
log "  Space freed: ${DELETED_SIZE_MB}MB"
log "  Errors: $ERRORS"
log "  Cutoff date: $CUTOFF_DATE"
if [ "$DRY_RUN" = "true" ]; then
    log "  Mode: DRY RUN (no files actually deleted)"
fi
log "=========================================="

# Clean up empty directories
if [ "$DRY_RUN" != "true" ]; then
    log "Cleaning up empty directories..."
    find "$BASE_DIR" -type d -empty -delete 2>/dev/null
    log "Empty directories removed"
fi

log "Cleanup completed"

exit 0
