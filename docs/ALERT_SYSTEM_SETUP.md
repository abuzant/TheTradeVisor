# Alert System Setup Guide

## Overview

TheTradeVisor now has a comprehensive alert system that sends notifications via Slack or Email when system issues are detected.

---

## How Alerting Works

### Alert Flow

```
System Issue Detected
    ↓
Monitor Script Triggers Alert
    ↓
send_alert.sh Script
    ↓
Try Slack Webhook (if configured)
    ↓ (if fails or not configured)
Try Email (if configured)
    ↓ (always)
Log to /var/log/thetradevisor/alerts.log
```

### Alert Levels

- **🔴 CRITICAL**: System in danger, immediate action required
  - High CPU (>80%)
  - High Memory (>85%)
  - Multiple system issues (≥3)
  - PHP-FPM restart required

- **⚠️ WARNING**: Potential issue, monitor closely
  - Long-running PostgreSQL queries (>30s)
  - High disk I/O (>1500 ops/s)
  - PHP-FPM slow requests (>5)
  - Backend nginx down

- **✅ INFO**: Informational, no action needed
  - System recovered
  - Test alerts
  - Routine notifications

---

## Configuration

### Step 1: Add to .env file

Edit `/www/.env` and add:

```env
# System Monitoring & Alerts
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/YOUR/WEBHOOK/URL
ALERT_EMAIL=hello@thetradevisor.com
```

### Step 2: Get Slack Webhook URL (Optional)

1. Go to https://api.slack.com/apps
2. Create a new app or select existing
3. Enable "Incoming Webhooks"
4. Click "Add New Webhook to Workspace"
5. Select channel (e.g., #alerts or #monitoring)
6. Copy the webhook URL
7. Add to `.env` as `SLACK_WEBHOOK_URL`

### Step 3: Configure Email (Optional)

If you don't use Slack, configure email:

```env
ALERT_EMAIL=hello@thetradevisor.com
```

**Note**: Email requires `mail` or `sendmail` command to be installed on the server.

To install mail command:
```bash
sudo apt-get update
sudo apt-get install mailutils
```

### Step 4: Test the Alert System

```bash
# Test INFO alert
/www/scripts/send_alert.sh "INFO" "Test Alert" "This is a test"

# Test WARNING alert
/www/scripts/send_alert.sh "WARNING" "High CPU Test" "CPU usage at 85%"

# Test CRITICAL alert
/www/scripts/send_alert.sh "CRITICAL" "System Test" "Critical system issue"
```

Check:
- Slack channel (if configured)
- Email inbox (if configured)
- `/var/log/thetradevisor/alerts.log` (always)

---

## Alert Triggers

### CPU Usage > 80%
```
🔴 CRITICAL: High CPU usage: 85%

Details:
- Top CPU-consuming processes logged
- Checks for slow PHP-FPM requests
- Auto-recovery if multiple issues
```

### Memory Usage > 85%
```
🔴 CRITICAL: High memory usage: 90%

Details:
- Top memory-consuming processes logged
- System may clear Laravel cache
- Swap space will be used if available
```

### Long-Running PostgreSQL Queries
```
⚠️ WARNING: Long-running PostgreSQL queries detected: 3

Details:
- Queries running > 30 seconds
- Query details logged
- Queries will be killed by PostgreSQL timeout
```

### PHP-FPM Slow Requests
```
⚠️ WARNING: Too many slow PHP requests: 8

Details:
- Requests taking > 5 seconds
- Logged to /var/log/php8.3-fpm-slow.log
- May trigger PHP-FPM restart if > 10
```

### Multiple System Issues
```
🔴 CRITICAL: Multiple system issues detected! (3 issues)

Auto-Recovery Actions:
- Laravel cache cleared
- PHP-FPM restarted (if needed)
- System monitoring intensified
```

### Backend Nginx Down
```
⚠️ WARNING: Backend nginx on port 8081 is DOWN

Details:
- One of 4 backend workers not responding
- Load balancer will route to other backends
- Manual restart may be required
```

---

## Monitoring Schedule

The health monitor runs **every 2 minutes** via cron:

```bash
*/2 * * * * /www/scripts/monitor_system_health.sh
```

To verify it's running:
```bash
crontab -l | grep monitor_system_health
```

---

## Alert Logs

### View Recent Alerts
```bash
# All alerts
tail -f /var/log/thetradevisor/alerts.log

# Last 50 alerts
tail -50 /var/log/thetradevisor/alerts.log

# Alerts from today
grep "$(date '+%Y-%m-%d')" /var/log/thetradevisor/alerts.log

# Critical alerts only
grep "CRITICAL" /var/log/thetradevisor/alerts.log
```

### View Health Monitor Log
```bash
# Real-time monitoring
tail -f /var/log/thetradevisor/health_monitor.log

# Last health check
tail -20 /var/log/thetradevisor/health_monitor.log
```

---

## Slack Message Format

Alerts sent to Slack include:

```
🔴 CRITICAL: High CPU usage: 85%

Server: ip-172-31-11-38
Time: 2025-11-12 14:30:00 UTC

Details: [Alert details here]
```

Color coding:
- 🔴 Red = CRITICAL
- ⚠️ Orange = WARNING
- ✅ Green = INFO

---

## Email Format

Alerts sent via email include:

```
Subject: [CRITICAL] TheTradeVisor Alert: High CPU usage: 85%

TheTradeVisor System Alert

Level: CRITICAL
Message: High CPU usage: 85%
Server: ip-172-31-11-38
Time: 2025-11-12 14:30:00 UTC

Details:
[Alert details here]

---
This is an automated alert from TheTradeVisor system monitoring.
To configure alerts, update SLACK_WEBHOOK_URL or ALERT_EMAIL in /www/.env
```

---

## Troubleshooting

### Alerts Not Sending to Slack

1. **Check webhook URL**:
   ```bash
   grep SLACK_WEBHOOK_URL /www/.env
   ```

2. **Test webhook manually**:
   ```bash
   curl -X POST -H 'Content-type: application/json' \
     --data '{"text":"Test from TheTradeVisor"}' \
     YOUR_WEBHOOK_URL
   ```

3. **Check alert log**:
   ```bash
   tail /var/log/thetradevisor/alerts.log
   ```

### Alerts Not Sending via Email

1. **Check if mail command exists**:
   ```bash
   which mail
   which sendmail
   ```

2. **Install mailutils if missing**:
   ```bash
   sudo apt-get install mailutils
   ```

3. **Test email manually**:
   ```bash
   echo "Test email" | mail -s "Test" hello@thetradevisor.com
   ```

4. **Check email alert log**:
   ```bash
   tail /var/log/thetradevisor/email_alerts.log
   ```

### No Alerts at All

1. **Check if monitoring is running**:
   ```bash
   crontab -l | grep monitor_system_health
   ```

2. **Run monitor manually**:
   ```bash
   /www/scripts/monitor_system_health.sh
   ```

3. **Check permissions**:
   ```bash
   ls -la /www/scripts/send_alert.sh
   # Should be executable: -rwxr-xr-x
   ```

4. **Make executable if needed**:
   ```bash
   chmod +x /www/scripts/send_alert.sh
   chmod +x /www/scripts/monitor_system_health.sh
   ```

---

## Customization

### Change Alert Thresholds

Edit `/www/scripts/monitor_system_health.sh`:

```bash
# Current thresholds
MAX_CPU=80                    # CPU percentage
MAX_MEMORY=85                 # Memory percentage
MAX_DISK_IO=1500             # Disk operations per second
MAX_PHP_SLOW_REQUESTS=5      # Slow PHP requests

# Modify as needed
MAX_CPU=90                    # More lenient
MAX_MEMORY=90
```

### Change Monitoring Frequency

Edit crontab:
```bash
crontab -e

# Change from every 2 minutes to every 5 minutes:
*/5 * * * * /www/scripts/monitor_system_health.sh
```

### Add Custom Alerts

Edit `/www/scripts/monitor_system_health.sh` and add your check:

```bash
check_custom() {
    # Your custom check logic here
    if [ some_condition ]; then
        alert "Your custom alert message" "WARNING"
        return 1
    fi
    return 0
}

# Add to main function
main() {
    # ... existing checks ...
    check_custom || ((ISSUES++))
}
```

---

## Best Practices

1. **Configure at least one notification method** (Slack or Email)
2. **Test alerts after configuration** to ensure they work
3. **Monitor alert logs regularly** to catch issues early
4. **Don't ignore WARNING alerts** - they often precede CRITICAL issues
5. **Review alert thresholds monthly** and adjust based on normal usage
6. **Document any custom checks** you add to the monitoring script

---

## Integration with Other Tools

### PagerDuty

To integrate with PagerDuty, modify `/www/scripts/send_alert.sh`:

```bash
send_pagerduty() {
    curl -X POST https://events.pagerduty.com/v2/enqueue \
      -H 'Content-Type: application/json' \
      -d "{
        \"routing_key\": \"$PAGERDUTY_KEY\",
        \"event_action\": \"trigger\",
        \"payload\": {
          \"summary\": \"$ALERT_MESSAGE\",
          \"severity\": \"critical\",
          \"source\": \"$HOSTNAME\"
        }
      }"
}
```

### Discord

Similar to Slack, Discord supports webhooks:

```bash
send_discord() {
    curl -X POST "$DISCORD_WEBHOOK_URL" \
      -H 'Content-Type: application/json' \
      -d "{\"content\": \"$EMOJI $ALERT_LEVEL: $ALERT_MESSAGE\"}"
}
```

---

## Summary

✅ **Alert system is active and monitoring every 2 minutes**  
✅ **Alerts logged locally always**  
✅ **Slack integration ready** (configure webhook)  
✅ **Email integration ready** (configure email)  
✅ **Auto-recovery for critical issues**  
✅ **Comprehensive system health monitoring**

**Next Steps**:
1. Add your Slack webhook URL to `.env`
2. Test the alert system
3. Monitor `/var/log/thetradevisor/alerts.log`
4. Adjust thresholds if needed

---

**For support**: Check logs in `/var/log/thetradevisor/`  
**For customization**: Edit `/www/scripts/monitor_system_health.sh`  
**For testing**: Run `/www/scripts/send_alert.sh "INFO" "Test" "Testing"`


---

---

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)  
❤️ From Palestine to the world with Love

For project support and inquiries:  
📧 [hello@thetradevisor.com](mailto:hello@thetradevisor.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)
