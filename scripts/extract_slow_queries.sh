#!/bin/bash

# Extract slow queries from PostgreSQL log
# Creates a dedicated slow query log file

POSTGRES_LOG="/var/log/postgresql/postgresql-16-main.log"
SLOW_QUERY_LOG="/var/log/thetradevisor/postgresql_slow_queries.log"

# Create log directory if it doesn't exist
mkdir -p /var/log/thetradevisor

# Extract slow queries (duration >= 1000ms)
grep -E "duration: [0-9]{4,}\." "$POSTGRES_LOG" 2>/dev/null | tail -1000 > "$SLOW_QUERY_LOG.tmp"

# Add timestamp header
echo "# PostgreSQL Slow Queries (>1 second)" > "$SLOW_QUERY_LOG"
echo "# Generated: $(date)" >> "$SLOW_QUERY_LOG"
echo "# Source: $POSTGRES_LOG" >> "$SLOW_QUERY_LOG"
echo "" >> "$SLOW_QUERY_LOG"

# Append slow queries
cat "$SLOW_QUERY_LOG.tmp" >> "$SLOW_QUERY_LOG" 2>/dev/null

# Cleanup
rm -f "$SLOW_QUERY_LOG.tmp"

# Set permissions
chmod 644 "$SLOW_QUERY_LOG"

echo "Slow queries extracted to: $SLOW_QUERY_LOG"
