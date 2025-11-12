# Admin Log Viewer - Alert Logs Added

## Update Summary

Added system monitoring and alert logs to the admin log viewer panel.

---

## New Logs Available

### 1. **System Alerts** 🔔
- **Path**: `/var/log/thetradevisor/alerts.log`
- **Contains**: All system alerts (INFO, WARNING, CRITICAL)
- **Updated**: Real-time as alerts are triggered
- **Size**: ~689 bytes

**What you'll see**:
- Alert level (INFO, WARNING, CRITICAL)
- Alert message
- Timestamp
- Details about the issue

**Example**:
```
[2025-11-12 14:30:07 UTC] INFO: Alert System Test - All Systems Operational
  Details: This is a test alert to verify the monitoring and notification 
  system is working correctly.

[2025-11-12 14:35:26 UTC] WARNING: Test Alert - Please Check Your Email
  Details: This is a test warning alert sent at Wed Nov 12 14:35:26 UTC 2025.
```

### 2. **Health Monitor** 💓
- **Path**: `/var/log/thetradevisor/health_monitor.log`
- **Contains**: System health checks (every 2 minutes)
- **Updated**: Every 2 minutes via cron
- **Size**: ~2.5 KB

**What you'll see**:
- CPU usage checks
- Memory usage checks
- Disk I/O monitoring
- PostgreSQL health
- PHP-FPM status
- Backend nginx health
- Auto-recovery actions

**Example**:
```
[2025-11-12 14:36:01] Starting health check...
[2025-11-12 14:36:01] CPU Usage: 45%
[2025-11-12 14:36:01] Memory Usage: 65%
[2025-11-12 14:36:01] All checks passed
[2025-11-12 14:36:01] Health check completed
```

### 3. **PHP-FPM Slow Log** 🐌
- **Path**: `/var/log/php8.3-fpm-slow.log`
- **Contains**: PHP requests taking >5 seconds
- **Updated**: When slow requests occur
- **Purpose**: Identify performance bottlenecks

**What you'll see**:
- Slow request timestamps
- Script being executed
- Execution time
- Stack traces

---

## How to Access

### Via Admin Panel

1. Log in as admin
2. Go to **Admin Panel**
3. Click **Logs** in the sidebar
4. Select log type from dropdown:
   - **System Alerts** - View all alerts
   - **Health Monitor** - View health checks
   - **PHP FPM Slow** - View slow requests
   - (Plus all existing logs)

### Log Type Dropdown

The dropdown now includes:
- Laravel
- Worker
- Horizon
- Nginx Access
- Nginx Error
- Nginx API Access
- Nginx API Error
- PostgreSQL
- Redis
- PHP FPM
- **PHP FPM Slow** ← NEW
- **System Alerts** ← NEW
- **Health Monitor** ← NEW

---

## Features

### View Logs
- Select number of lines to display (default: 100)
- Real-time log viewing
- Automatic formatting
- File size display
- Last modified timestamp

### Download Logs
- Download any log file
- Full log file download
- Preserves original filename

### Auto-Refresh
- Logs update when you refresh the page
- Shows most recent entries first

---

## Use Cases

### Monitor System Health
1. Go to **Health Monitor** log
2. See health checks every 2 minutes
3. Identify patterns or issues

### Review Alerts
1. Go to **System Alerts** log
2. See all INFO, WARNING, CRITICAL alerts
3. Check if emails were sent
4. Review alert details

### Debug Slow Requests
1. Go to **PHP FPM Slow** log
2. See which requests are slow (>5s)
3. Identify bottlenecks
4. Optimize slow code

### Troubleshoot Issues
1. Check **System Alerts** for recent problems
2. Check **Health Monitor** for system status
3. Check **PHP FPM Slow** for performance issues
4. Cross-reference with other logs

---

## Alert Log Examples

### INFO Alert
```
[2025-11-12 14:30:07 UTC] INFO: Alert System Test - All Systems Operational
  Details: Email delivery via Amazon SES, monitoring active every 2 minutes
```

### WARNING Alert
```
[2025-11-12 13:26:00 UTC] WARNING: High CPU usage: 85%
  Details: Top CPU-consuming processes logged
```

### CRITICAL Alert
```
[2025-11-12 13:30:00 UTC] CRITICAL: Multiple system issues detected! (3 issues)
  Details: Auto-recovery initiated - clearing cache, restarting services
```

---

## Health Monitor Examples

### Normal Check
```
[2025-11-12 14:36:01] Starting health check...
[2025-11-12 14:36:01] CPU Usage: 45%
[2025-11-12 14:36:01] Memory Usage: 65%
[2025-11-12 14:36:01] Disk I/O: 450 ops/s
[2025-11-12 14:36:01] PostgreSQL: OK
[2025-11-12 14:36:01] PHP-FPM: OK
[2025-11-12 14:36:01] All checks passed
```

### Issue Detected
```
[2025-11-12 13:26:00] Starting health check...
[2025-11-12 13:26:00] CPU Usage: 85%
[2025-11-12 13:26:00] ALERT: High CPU usage: 85%
[2025-11-12 13:26:00] Memory Usage: 90%
[2025-11-12 13:26:00] ALERT: High memory usage: 90%
[2025-11-12 13:26:00] Health check failed with 2 issues
```

---

## Benefits

### For Admins
- ✅ Single place to view all logs
- ✅ Easy access to monitoring data
- ✅ Quick troubleshooting
- ✅ Download logs for analysis
- ✅ No SSH required

### For System Health
- ✅ Monitor system in real-time
- ✅ Identify issues quickly
- ✅ Track alert history
- ✅ Verify monitoring is working
- ✅ Review auto-recovery actions

### For Debugging
- ✅ See slow requests
- ✅ Track performance issues
- ✅ Correlate alerts with events
- ✅ Historical data available
- ✅ Easy to share with team

---

## Log Rotation

All logs are automatically rotated to prevent disk space issues:

- **System Alerts**: Rotated weekly, kept for 4 weeks
- **Health Monitor**: Rotated weekly, kept for 4 weeks
- **PHP-FPM Slow**: Rotated daily, kept for 7 days

Configuration in `/etc/logrotate.d/thetradevisor`

---

## Security

- ✅ Admin-only access
- ✅ Logs stored securely
- ✅ No sensitive data in logs
- ✅ Proper file permissions
- ✅ Automatic rotation

---

## Summary

**3 new log types added to admin panel**:
1. System Alerts - All alerts (INFO, WARNING, CRITICAL)
2. Health Monitor - System health checks every 2 minutes
3. PHP-FPM Slow - Slow requests (>5 seconds)

**Access**: Admin Panel → Logs → Select log type

**Benefits**: Easy monitoring, quick troubleshooting, no SSH required

---

**Updated**: November 12, 2025  
**Commit**: 78aba21  
**Status**: ✅ Live in production


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

