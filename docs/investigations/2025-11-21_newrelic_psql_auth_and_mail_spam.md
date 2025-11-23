# Investigation: New Relic PostgreSQL Authentication Failures & Internal Mail Spam

**Date:** November 21, 2025  
**Investigator:** System Administrator  
**Status:** ✅ RESOLVED

## Issues Identified

### 1. PostgreSQL Authentication Failures for New Relic User

**Symptoms:**
- New Relic logs showing repeated authentication failures
- Error: `password authentication failed for user "newrelic"`
- Occurring every 15 seconds (New Relic integration interval)

**Root Cause:**
- Password mismatch between PostgreSQL database and New Relic configuration
- Config file had: `ZDhkNGFkMmJiNWRioO0$`
- Database had different SCRAM-SHA-256 hash

**Resolution:**
1. Generated new secure password: `RwEhbbzi6vGWmiVN+ljqDw==`
2. Updated PostgreSQL user:
   ```bash
   sudo -u postgres psql -c "ALTER USER newrelic WITH PASSWORD 'RwEhbbzi6vGWmiVN+ljqDw==';"
   ```
3. Updated New Relic config: `/etc/newrelic-infra/integrations.d/postgresql-config.yml`
4. Restarted New Relic service: `sudo systemctl restart newrelic-infra`

**Verification:**
```bash
# Test connection
PGPASSWORD='RwEhbbzi6vGWmiVN+ljqDw==' psql -h localhost -U newrelic -d postgres -c "SELECT version();"

# Check New Relic logs
sudo journalctl -u newrelic-infra --since "5 minutes ago" | grep postgresql
# Result: "Integration health check finished with success"
```

---

### 2. Internal Mail Spam to root@thetradevisor.com

**Symptoms:**
- Security warning emails sent to root every 2 minutes
- Subject: `*** SECURITY information for ip-172-31-11-38 ***`
- Message: `tradeadmin : a password is required`
- Command: PostgreSQL query from monitoring script

**Root Cause:**
- Health monitoring script (`/www/scripts/monitor_system_health.sh`) runs via cron every 2 minutes
- Script executes: `sudo -u postgres psql -t -c "SELECT COUNT(*) FROM pg_stat_activity..."`
- No sudoers rule existed for tradeadmin to run psql as postgres without password
- System sent security notification to root for each failed sudo attempt

**Resolution:**
1. Created sudoers rule:
   ```bash
   echo "tradeadmin ALL=(postgres) NOPASSWD: /usr/bin/psql" | sudo tee /etc/sudoers.d/tradeadmin
   sudo chmod 0440 /etc/sudoers.d/tradeadmin
   ```

2. Fixed script bug (line 102 integer expression error):
   ```bash
   # Added line to clean SLOW_REQUESTS variable
   SLOW_REQUESTS=$(echo "$SLOW_REQUESTS" | tr -d '\n' | tr -d ' ')
   ```

3. Removed deprecated backend nginx check (ports 8081-8084 no longer used)

**Verification:**
- Last security email to root: `19:10:03 UTC`
- After fix: No more security emails
- Monitoring script runs cleanly without errors

---

### 3. Cron Email Output to tradeadmin@thetradevisor.com

**Symptoms:**
- Cron sends output of monitoring script to tradeadmin mailbox
- Contains journalctl hint messages
- Script error about integer expression

**Root Cause:**
- Cron sends all output (stdout/stderr) to user's mailbox by default
- Monitoring script had bash error at line 102

**Resolution:**
1. Fixed bash error (see issue #2 above)
2. Script now runs cleanly with only success messages
3. Emails still sent but contain only clean output

**Optional Future Enhancement:**
- Redirect cron output to log file instead of email:
  ```cron
  */2 * * * * /www/scripts/monitor_system_health.sh >/dev/null 2>&1
  ```
- OR keep emails for monitoring purposes (current approach)

---

## Files Modified

1. `/etc/newrelic-infra/integrations.d/postgresql-config.yml`
   - Updated PASSWORD field

2. `/etc/sudoers.d/tradeadmin` (created)
   - Added NOPASSWD rule for psql

3. `/etc/sudoers.d/thetradevisor`
   - Fixed permissions (0440)

4. `/www/scripts/monitor_system_health.sh`
   - Fixed SLOW_REQUESTS variable handling
   - Removed deprecated backend nginx check

---

## Current Status

### ✅ New Relic PostgreSQL Integration
- Status: **WORKING**
- Authentication: **SUCCESS**
- Data collection: **ACTIVE**
- Errors: **NONE**

### ✅ System Monitoring
- Health checks: **RUNNING**
- Cron schedule: **Every 2 minutes**
- Errors: **NONE**
- Security emails: **STOPPED**

### ✅ Mail System
- Postfix: **RUNNING**
- Security notifications: **STOPPED** (no more sudo password failures)
- Cron emails: **CLEAN** (no errors)

---

## Lessons Learned

1. **Password Management**: Always verify password sync between config files and database
2. **Sudoers Rules**: Service accounts need proper sudo permissions for automated tasks
3. **Monitoring Scripts**: Clean up deprecated checks to avoid confusion
4. **Variable Handling**: Always sanitize shell variables before integer comparisons
5. **Mail Notifications**: Useful for debugging but can become spam if not managed

---

## Recommendations

1. ✅ **Completed**: Fix New Relic authentication
2. ✅ **Completed**: Stop security email spam
3. ✅ **Completed**: Fix monitoring script errors
4. 🔄 **Optional**: Redirect cron output to log files instead of email
5. 🔄 **Optional**: Set up proper email forwarding for root@thetradevisor.com
6. 🔄 **Optional**: Implement log rotation for `/var/mail/` to prevent mailbox growth

---

## Related Files

- New Relic Config: `/etc/newrelic-infra/integrations.d/postgresql-config.yml`
- Monitoring Script: `/www/scripts/monitor_system_health.sh`
- Sudoers: `/etc/sudoers.d/tradeadmin`
- Crontab: `crontab -l` (tradeadmin user)
- Logs: `/var/log/thetradevisor/health_monitor.log`
- Mail: `/var/mail/root`, `/var/mail/tradeadmin`

---

## Timeline

- **19:06 - 19:10 UTC**: Investigation started, identified issues
- **19:10 UTC**: Fixed New Relic password, restarted service
- **19:11 UTC**: Created sudoers rule for tradeadmin
- **19:11 UTC**: Fixed monitoring script errors
- **19:12 UTC**: First clean cron run (no errors)
- **19:14 UTC**: Verified all issues resolved

---

## Verification Commands

```bash
# Check New Relic status
sudo systemctl status newrelic-infra
sudo journalctl -u newrelic-infra --since "10 minutes ago" | grep postgresql

# Check PostgreSQL connection
PGPASSWORD='RwEhbbzi6vGWmiVN+ljqDw==' psql -h localhost -U newrelic -d postgres -c "SELECT version();"

# Check sudoers
sudo visudo -c

# Check mail
sudo tail -20 /var/mail/root
sudo tail -20 /var/mail/tradeadmin

# Check monitoring script
/www/scripts/monitor_system_health.sh

# Check cron
crontab -l
```

---

**Investigation completed successfully. All issues resolved.**
