#!/bin/bash

# TheTradeVisor Backend Instances Startup Script
# Starts all 4 backend nginx instances

echo "🚀 Starting TheTradeVisor Backend Instances"
echo "==========================================="
echo ""

# Check if running as root for sudo commands
if [ "$EUID" -ne 0 ]; then 
    USE_SUDO="sudo"
else
    USE_SUDO=""
fi

# Start each backend instance
for i in 1 2 3 4; do
    echo "Starting Backend Instance ${i}..."
    
    # Check if already running
    if [ -f /run/nginx-backend-${i}.pid ]; then
        PID=$(cat /run/nginx-backend-${i}.pid)
        if kill -0 $PID 2>/dev/null; then
            echo "⚠️  Backend Instance ${i} is already running (PID: ${PID})"
            continue
        fi
    fi
    
    # Start the instance
    $USE_SUDO nginx -c /etc/nginx/backends/nginx-backend-${i}-master.conf
    
    if [ $? -eq 0 ]; then
        echo "✓ Backend Instance ${i} started successfully"
    else
        echo "✗ Failed to start Backend Instance ${i}"
        exit 1
    fi
    
    sleep 0.5
done

echo ""
echo "✅ All backend instances started!"
echo ""
echo "📊 Status:"
for i in 1 2 3 4; do
    if [ -f /run/nginx-backend-${i}.pid ]; then
        PID=$(cat /run/nginx-backend-${i}.pid)
        if kill -0 $PID 2>/dev/null; then
            echo "  - Backend ${i}: Running (PID: ${PID}, Port: 808${i})"
        else
            echo "  - Backend ${i}: Failed (stale PID file)"
        fi
    else
        echo "  - Backend ${i}: Not running"
    fi
done
echo ""
