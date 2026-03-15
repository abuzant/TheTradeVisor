#!/bin/bash

# Alert Notification Script for TheTradeVisor
# Sends alerts via Slack webhook or email

ALERT_LEVEL="$1"  # INFO, WARNING, CRITICAL
ALERT_MESSAGE="$2"
ALERT_DETAILS="$3"

# Load environment variables
if [ -f /vhosts/thetradevisor.com/.env ]; then
    export $(grep -v '^#' /vhosts/thetradevisor.com/.env | xargs)
fi

# Default email if not set
ALERT_EMAIL="${ALERT_EMAIL:-your-email@example.com}"

# Prepare alert payload
TIMESTAMP=$(date '+%Y-%m-%d %H:%M:%S UTC')
HOSTNAME=$(hostname)

# Color codes for Slack
case "$ALERT_LEVEL" in
    "CRITICAL")
        COLOR="#FF0000"
        EMOJI="🔴"
        ;;
    "WARNING")
        COLOR="#FFA500"
        EMOJI="⚠️"
        ;;
    "INFO")
        COLOR="#00FF00"
        EMOJI="✅"
        ;;
    *)
        COLOR="#808080"
        EMOJI="ℹ️"
        ;;
esac

# Function to send Slack notification
send_slack() {
    if [ -z "$SLACK_WEBHOOK_URL" ]; then
        return 1
    fi
    
    PAYLOAD=$(cat <<EOF
{
    "username": "TheTradeVisor Monitor",
    "icon_emoji": ":chart_with_upwards_trend:",
    "attachments": [
        {
            "color": "$COLOR",
            "title": "$EMOJI $ALERT_LEVEL: $ALERT_MESSAGE",
            "text": "$ALERT_DETAILS",
            "fields": [
                {
                    "title": "Server",
                    "value": "$HOSTNAME",
                    "short": true
                },
                {
                    "title": "Time",
                    "value": "$TIMESTAMP",
                    "short": true
                }
            ],
            "footer": "TheTradeVisor System Monitor",
            "ts": $(date +%s)
        }
    ]
}
EOF
)
    
    curl -X POST -H 'Content-type: application/json' \
        --data "$PAYLOAD" \
        "$SLACK_WEBHOOK_URL" \
        --silent --output /dev/null
    
    return $?
}

# Function to send email notification via Laravel (Amazon SES)
send_email() {
    if [ -z "$ALERT_EMAIL" ]; then
        return 1
    fi
    
    # Use Laravel's mail system with SES configuration
    /vhosts/thetradevisor.com/scripts/send_email_alert.php "$ALERT_LEVEL" "$ALERT_MESSAGE" "$ALERT_DETAILS"
    return $?
}

# Function to log alert locally
log_alert() {
    echo "[$TIMESTAMP] $ALERT_LEVEL: $ALERT_MESSAGE" >> /var/log/thetradevisor/alerts.log
    if [ -n "$ALERT_DETAILS" ]; then
        echo "  Details: $ALERT_DETAILS" >> /var/log/thetradevisor/alerts.log
    fi
}

# Main alert logic
main() {
    # Always log locally
    log_alert
    
    # Try Slack first
    if [ -n "$SLACK_WEBHOOK_URL" ]; then
        if send_slack; then
            echo "Alert sent to Slack"
            exit 0
        else
            echo "Failed to send Slack alert, trying email..."
        fi
    fi
    
    # Try email if Slack failed or not configured
    if [ -n "$ALERT_EMAIL" ]; then
        if send_email; then
            echo "Alert sent to email: $ALERT_EMAIL"
            exit 0
        else
            echo "Failed to send email alert"
        fi
    fi
    
    # If both failed, at least we logged it
    echo "Alert logged to /var/log/thetradevisor/alerts.log"
}

# Run main function
main
