# Alert System Configuration Guide

## Quick Setup

Your alert system is already configured and working! Here's what you need to know.

---

## ✅ Current Configuration

### Email Alerts (Amazon SES)
- **Status**: ✅ Configured and tested
- **Provider**: Amazon SES (eu-north-1)
- **From**: Configured in `.env`
- **To**: Configured in `.env` as `ALERT_EMAIL`

### Slack Alerts
- **Status**: ⚠️ Not configured (optional)
- **Setup**: Add `SLACK_WEBHOOK_URL` to `.env`

---

## Configuration in .env

Your `.env` file should have:

```env
# System Monitoring & Alerts
SLACK_WEBHOOK_URL=                    # Optional: Add Slack webhook URL
ALERT_EMAIL=hello@thetradevisor.com    # Required: Your alert email

# Amazon SES Configuration (Already configured)
MAIL_MAILER=smtp
MAIL_HOST=email-smtp.eu-north-1.amazonaws.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your-ses-username
MAIL_PASSWORD=your-ses-password
MAIL_FROM_ADDRESS=hello@thetradevisor.com
MAIL_FROM_NAME=TheTradeVisor
```

---

## How It Works

### Alert Flow
```
System Issue Detected
    ↓
Monitor Script (runs every 2 minutes)
    ↓
send_alert.sh
    ↓
1. Try Slack (if SLACK_WEBHOOK_URL configured)
    ↓ (if fails or not configured)
2. Try Email via Laravel/SES (if ALERT_EMAIL configured)
    ↓ (always)
3. Log to /var/log/thetradevisor/alerts.log
```

### Email Delivery
- Uses Laravel's mail system
- Sends through Amazon SES
- Reliable delivery with SES credentials
- No need for local mail server configuration

---

## Testing

### Test Email Alert
```bash
# Test email delivery
/www/scripts/send_email_alert.php "INFO" "Test Alert" "Testing email system"

# Check if email was sent
tail /var/log/thetradevisor/alerts.log
```

### Test Full Alert System
```bash
# Test complete alert flow
/www/scripts/send_alert.sh "INFO" "System Test" "Testing alert system"

# Check logs
tail -f /var/log/thetradevisor/alerts.log
```

---

## Adding Slack (Optional)

### Step 1: Create Slack Webhook

1. Go to https://api.slack.com/apps
2. Click "Create New App" → "From scratch"
3. Name it "TheTradeVisor Monitor"
4. Select your workspace
5. Click "Incoming Webhooks" in sidebar
6. Toggle "Activate Incoming Webhooks" to ON
7. Click "Add New Webhook to Workspace"
8. Select channel (e.g., #alerts or #monitoring)
9. Click "Allow"
10. Copy the webhook URL

### Step 2: Add to .env

```env
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/YOUR/WEBHOOK/URL
```

### Step 3: Test Slack

```bash
# Test Slack notification
/www/scripts/send_alert.sh "INFO" "Slack Test" "Testing Slack integration"
```

You should see a message in your Slack channel!

---

## Alert Levels

### 🔴 CRITICAL
**Triggers**:
- CPU usage > 80%
- Memory usage > 85%
- Multiple system issues (≥3)
- PHP-FPM restart required

**Actions**:
- Immediate notification
- Auto-recovery attempted
- Detailed logs captured

### ⚠️ WARNING
**Triggers**:
- Long-running PostgreSQL queries (>30s)
- High disk I/O (>1500 ops/s)
- PHP-FPM slow requests (>5)
- Backend nginx down

**Actions**:
- Notification sent
- Issue logged
- Monitoring continues

### ✅ INFO
**Triggers**:
- System recovered
- Test alerts
- Routine notifications

**Actions**:
- Notification sent
- Logged for reference

---

## Monitoring Schedule

The health monitor runs **every 2 minutes** via cron:

```bash
# View cron job
crontab -l | grep monitor_system_health

# Output:
# */2 * * * * /www/scripts/monitor_system_health.sh
```

---

## Viewing Alerts

### Real-time Monitoring
```bash
# Watch alerts as they happen
tail -f /var/log/thetradevisor/alerts.log

# Watch health monitor
tail -f /var/log/thetradevisor/health_monitor.log
```

### Historical Alerts
```bash
# Last 50 alerts
tail -50 /var/log/thetradevisor/alerts.log

# Alerts from today
grep "$(date '+%Y-%m-%d')" /var/log/thetradevisor/alerts.log

# Critical alerts only
grep "CRITICAL" /var/log/thetradevisor/alerts.log

# Alerts from last hour
grep "$(date '+%Y-%m-%d %H')" /var/log/thetradevisor/alerts.log
```

---

## Troubleshooting

### Email Not Sending

1. **Check SES credentials in .env**:
   ```bash
   grep MAIL_ /www/.env
   ```

2. **Verify SES is working**:
   ```bash
   cd /www && php artisan tinker
   >>> Mail::raw('Test', function($m) { $m->to('hello@thetradevisor.com')->subject('Test'); });
   ```

3. **Check Laravel logs**:
   ```bash
   tail -50 /www/storage/logs/laravel.log
   ```

4. **Test email script directly**:
   ```bash
   /www/scripts/send_email_alert.php "INFO" "Test" "Testing"
   ```

### Slack Not Working

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

3. **Check alert logs**:
   ```bash
   tail /var/log/thetradevisor/alerts.log
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

3. **Check script permissions**:
   ```bash
   ls -la /www/scripts/send_alert.sh
   ls -la /www/scripts/send_email_alert.php
   # Both should be executable (-rwxr-xr-x)
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
```

### Change Monitoring Frequency

Edit crontab:
```bash
crontab -e

# Change from every 2 minutes to every 5 minutes:
*/5 * * * * /www/scripts/monitor_system_health.sh
```

### Change Alert Email

Edit `/www/.env`:
```env
ALERT_EMAIL=new-email@example.com
```

No restart required - changes take effect immediately.

---

## Best Practices

1. ✅ **Keep ALERT_EMAIL updated** - Ensure you receive alerts
2. ✅ **Test alerts monthly** - Verify system is working
3. ✅ **Monitor alert logs** - Check for patterns
4. ✅ **Don't ignore WARNING alerts** - They often precede CRITICAL issues
5. ✅ **Review thresholds quarterly** - Adjust based on normal usage
6. ✅ **Use Slack for immediate notifications** - Email for backup

---

## Security Notes

- ⚠️ Never commit `.env` file to Git (already in `.gitignore`)
- ⚠️ Keep SES credentials secure
- ⚠️ Slack webhook URLs are sensitive - don't share publicly
- ✅ Alert logs are stored locally and rotated automatically
- ✅ Email addresses in documentation are sanitized

---

## Summary

✅ **Email alerts configured** - Using Amazon SES  
✅ **Alert system active** - Monitoring every 2 minutes  
✅ **Logs captured** - `/var/log/thetradevisor/alerts.log`  
⚠️ **Slack optional** - Add webhook URL to enable  

**Your system is protected and will alert you of any issues!**

---

## Quick Reference

```bash
# Test email
/www/scripts/send_email_alert.php "INFO" "Test" "Testing"

# Test full alert system
/www/scripts/send_alert.sh "INFO" "Test" "Testing"

# View alerts
tail -f /var/log/thetradevisor/alerts.log

# Check monitoring
crontab -l | grep monitor

# Manual health check
/www/scripts/monitor_system_health.sh
```

---

**For support**: Check logs in `/var/log/thetradevisor/`  
**For customization**: Edit `/www/scripts/monitor_system_health.sh`  
**For testing**: Run `/www/scripts/send_alert.sh "INFO" "Test" "Testing"`


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

