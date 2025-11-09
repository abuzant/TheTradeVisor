#!/bin/bash

# TheTradeVisor Backend Instances Shutdown Script
# Stops all 4 backend nginx instances gracefully

echo "🛑 Stopping TheTradeVisor Backend Instances"
echo "==========================================="
echo ""

# Check if running as root for sudo commands
if [ "$EUID" -ne 0 ]; then 
    USE_SUDO="sudo"
else
    USE_SUDO=""
fi

# Stop each backend instance
for i in 1 2 3 4; do
    echo "Stopping Backend Instance ${i}..."
    
    if [ -f /run/nginx-backend-${i}.pid ]; then
        PID=$(cat /run/nginx-backend-${i}.pid)
        
        if kill -0 $PID 2>/dev/null; then
            # Send graceful shutdown signal
            $USE_SUDO kill -QUIT $PID
            
            # Wait for process to stop (max 5 seconds)
            for j in {1..10}; do
                if ! kill -0 $PID 2>/dev/null; then
                    echo "✓ Backend Instance ${i} stopped gracefully"
                    break
                fi
                sleep 0.5
            done
            
            # Force kill if still running
            if kill -0 $PID 2>/dev/null; then
                echo "⚠️  Force stopping Backend Instance ${i}..."
                $USE_SUDO kill -TERM $PID
                sleep 1
                if kill -0 $PID 2>/dev/null; then
                    $USE_SUDO kill -KILL $PID
                fi
            fi
        else
            echo "⚠️  Backend Instance ${i} PID file exists but process not running"
            $USE_SUDO rm -f /run/nginx-backend-${i}.pid
        fi
    else
        echo "⚠️  Backend Instance ${i} is not running (no PID file)"
    fi
done

echo ""
echo "✅ All backend instances stopped!"
echo ""
