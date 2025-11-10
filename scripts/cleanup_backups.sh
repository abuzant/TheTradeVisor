#!/bin/bash

# Backup cleanup script - Keep only last 5 backups
BACKUP_DIR="/var/www/thetradevisor.com/backups"
MAX_BACKUPS=5

echo "Cleaning up backups - keeping last $MAX_BACKUPS..."

# Change to backup directory
cd "$BACKUP_DIR" || exit 1

# Count current backup folders
BACKUP_COUNT=$(find . -maxdepth 1 -type d -name "rate-limiting-*" | wc -l)

if [ "$BACKUP_COUNT" -le "$MAX_BACKUPS" ]; then
    echo "Current backup count ($BACKUP_COUNT) is within limit ($MAX_BACKUPS). No cleanup needed."
    exit 0
fi

# List backups sorted by date (newest first) and remove oldest ones beyond the limit
find . -maxdepth 1 -type d -name "rate-limiting-*" | sort -r | tail -n +$((MAX_BACKUPS + 1)) | while read -r backup; do
    echo "Removing old backup: $backup"
    rm -rf "$backup"
done

echo "Backup cleanup completed. Current backups:"
find . -maxdepth 1 -type d -name "rate-limiting-*" | sort -r
