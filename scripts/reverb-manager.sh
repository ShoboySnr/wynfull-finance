#!/bin/bash

# Reverb Manager Script for cPanel
# This script ensures Laravel Reverb stays running

# Configuration
PROJECT_PATH="/home/alexizyd/public_html/app/wynfull-finance"
REVERB_PID_FILE="$PROJECT_PATH/storage/reverb.pid"
LOG_FILE="$PROJECT_PATH/storage/logs/reverb.log"

# Function to check if Reverb is running
is_reverb_running() {
    if [ -f "$REVERB_PID_FILE" ]; then
        PID=$(cat "$REVERB_PID_FILE")
        if ps -p $PID > /dev/null 2>&1; then
            return 0
        else
            rm -f "$REVERB_PID_FILE"
            return 1
        fi
    else
        return 1
    fi
}

# Function to start Reverb
start_reverb() {
    cd "$PROJECT_PATH"
    
    # Start Reverb in background and save PID
    nohup php artisan reverb:start >> "$LOG_FILE" 2>&1 &
    echo $! > "$REVERB_PID_FILE"
    
    echo "$(date): Reverb started with PID $(cat $REVERB_PID_FILE)" >> "$LOG_FILE"
}

# Function to stop Reverb
stop_reverb() {
    if [ -f "$REVERB_PID_FILE" ]; then
        PID=$(cat "$REVERB_PID_FILE")
        kill $PID 2>/dev/null
        rm -f "$REVERB_PID_FILE"
        echo "$(date): Reverb stopped" >> "$LOG_FILE"
    fi
}

# Main logic
case "$1" in
    start)
        if is_reverb_running; then
            echo "Reverb is already running"
        else
            start_reverb
            echo "Reverb started"
        fi
        ;;
    stop)
        stop_reverb
        echo "Reverb stopped"
        ;;
    restart)
        stop_reverb
        sleep 2
        start_reverb
        echo "Reverb restarted"
        ;;
    status)
        if is_reverb_running; then
            echo "Reverb is running (PID: $(cat $REVERB_PID_FILE))"
        else
            echo "Reverb is not running"
        fi
        ;;
    monitor)
        if ! is_reverb_running; then
            echo "$(date): Reverb not running, starting..." >> "$LOG_FILE"
            start_reverb
        fi
        ;;
    *)
        echo "Usage: $0 {start|stop|restart|status|monitor}"
        exit 1
        ;;
esac
