#!/bin/bash

# Extract slow queries from PostgreSQL log (Docker container)
# Creates a dedicated slow query log file

CONTAINER="postgres"
SLOW_QUERY_LOG="/var/log/thetradevisor/postgresql_slow_queries.log"

# Create log directory if it doesn't exist
mkdir -p /var/log/thetradevisor

# Extract slow queries (duration >= 1000ms) from Docker logs
docker logs "$CONTAINER" 2>&1 | grep -E "duration: [0-9]{4,}\." | tail -1000 > "$SLOW_QUERY_LOG.tmp"

# Add timestamp header
echo "# PostgreSQL Slow Queries (>1 second)" > "$SLOW_QUERY_LOG"
echo "# Generated: $(date)" >> "$SLOW_QUERY_LOG"
echo "# Source: docker logs $CONTAINER" >> "$SLOW_QUERY_LOG"
echo "" >> "$SLOW_QUERY_LOG"

# Append slow queries
cat "$SLOW_QUERY_LOG.tmp" >> "$SLOW_QUERY_LOG" 2>/dev/null

# Cleanup
rm -f "$SLOW_QUERY_LOG.tmp"

# Set permissions
chmod 644 "$SLOW_QUERY_LOG"

echo "Slow queries extracted to: $SLOW_QUERY_LOG"
