#!/bin/bash

# Alert Notification Script for TheTradeVisor
# Sends alerts via Slack webhook or email

ALERT_LEVEL="$1"  # INFO, WARNING, CRITICAL
ALERT_MESSAGE="$2"
ALERT_DETAILS="$3"

# Load environment variables
if [ -f /www/.env ]; then
    export $(grep -v '^#' /www/.env | xargs)
fi

# Default email if not set
ALERT_EMAIL="${ALERT_EMAIL:-hello@thetradevisor.com}"

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

# Function to send email notification
send_email() {
    if [ -z "$ALERT_EMAIL" ]; then
        return 1
    fi
    
    SUBJECT="[$ALERT_LEVEL] TheTradeVisor Alert: $ALERT_MESSAGE"
    
    BODY=$(cat <<EOF
TheTradeVisor System Alert

Level: $ALERT_LEVEL
Message: $ALERT_MESSAGE
Server: $HOSTNAME
Time: $TIMESTAMP

Details:
$ALERT_DETAILS

---
This is an automated alert from TheTradeVisor system monitoring.
To configure alerts, update SLACK_WEBHOOK_URL or ALERT_EMAIL in /www/.env
EOF
)
    
    # Try to send email using mail command
    if command -v mail &> /dev/null; then
        echo "$BODY" | mail -s "$SUBJECT" "$ALERT_EMAIL"
        return $?
    elif command -v sendmail &> /dev/null; then
        echo -e "Subject: $SUBJECT\n\n$BODY" | sendmail "$ALERT_EMAIL"
        return $?
    else
        # Fallback: log to file
        echo "[$TIMESTAMP] EMAIL ALERT (no mail command): $SUBJECT" >> /var/log/thetradevisor/email_alerts.log
        echo "$BODY" >> /var/log/thetradevisor/email_alerts.log
        echo "---" >> /var/log/thetradevisor/email_alerts.log
        return 1
    fi
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
