#!/bin/bash

echo "=== POSTGRESQL ROLLBACK SCRIPT ==="
echo "Use this if you experience issues after PostgreSQL optimization"
echo ""

DOCKER_COMPOSE_DIR="/home/ubuntu/docker-apps"
PG_CONF="$DOCKER_COMPOSE_DIR/postgres/postgresql.conf"
PG_BACKUP="$PG_CONF.backup"

if [ ! -f "$PG_BACKUP" ]; then
    echo "❌ No backup found at $PG_BACKUP"
    exit 1
fi

echo "Restoring original PostgreSQL configuration..."
cp "$PG_BACKUP" "$PG_CONF"

echo "Restarting PostgreSQL container..."
docker restart postgres

echo "Waiting for PostgreSQL to be ready..."
sleep 3
docker exec postgres pg_isready -U postgres && echo "✅ PostgreSQL is ready" || echo "⚠ PostgreSQL may still be starting"

echo ""
echo "✅ POSTGRESQL ROLLBACK COMPLETED"
echo "Your PostgreSQL settings are back to original values"
