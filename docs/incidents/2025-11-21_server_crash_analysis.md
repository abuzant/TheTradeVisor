# Server Crash Incident Analysis - November 21, 2025

**Date**: November 21, 2025  
**Time**: ~10:30-10:40 UTC  
**Severity**: CRITICAL - Complete server unresponsiveness  
**Resolution**: Automatic reboot by AWS

---

## Executive Summary

The server became completely unresponsive around 10:30 UTC and automatically rebooted at 10:40 UTC. The root cause was **CPU credit exhaustion on a T-series AWS EC2 instance**, combined with memory pressure, causing the system to become unresponsive.

---

## Timeline

- **10:30:05 UTC**: System activity accounting shows CPU usage spike to **17.76% user + 17.19% system + 22.69% iowait = 57.64% total**
- **10:30:07 UTC**: systemd-journald reports "Under memory pressure, flushing caches"
- **10:30:10 UTC**: Memory pressure continues
- **10:30:15 UTC**: Memory pressure continues
- **10:30:20 UTC**: Memory pressure continues
- **~10:30-10:40**: Server completely unresponsive (no logs)
- **10:40:09 UTC**: System rebooted (LINUX RESTART)
- **10:40:10+ UTC**: All services started successfully

---

## Root Cause Analysis

### Primary Cause: CPU Credit Exhaustion

**Evidence from AWS CloudWatch (screenshot):**
- CPU credit balance dropped to **0** around 10:35 UTC
- CPU credit usage spiked to **4.22** (maximum burst)
- This is a T-series instance behavior

**What happened:**
1. T-series instances (t3.medium, t3a.medium, etc.) use a CPU credit system
2. Normal operation earns credits, high CPU usage consumes them
3. When credits hit 0, CPU is throttled to baseline (20-40% depending on instance type)
4. With CPU throttled, the system cannot respond to requests
5. SSH becomes unresponsive, web server stops responding
6. AWS eventually force-rebooted the instance

### Contributing Factor: Memory Pressure

**Evidence:**
```
Nov 21 10:30:07 systemd-journald[132]: Under memory pressure, flushing caches.
Nov 21 10:30:10 systemd-journald[132]: Under memory pressure, flushing caches.
Nov 21 10:30:15 systemd-journald[132]: Under memory pressure, flushing caches.
Nov 21 10:30:20 systemd-journald[132]: Under memory pressure, flushing caches.
```

**Current memory status (after reboot):**
```
               total        used        free      shared  buff/cache   available
Mem:           3.7Gi       2.5Gi       608Mi        72Mi       970Mi       1.2Gi
Swap:          2.0Gi          0B       2.0Gi
```

**Analysis:**
- System has 4GB RAM (3.7GB usable)
- Currently using 2.5GB (68%)
- Only 608MB free
- Swap is configured (2GB) but not being used
- Memory pressure indicates the system was struggling to allocate memory

### CPU Usage Pattern (from sar data)

**Normal operation (03:00-05:00):**
- CPU usage: ~1% (mostly idle)

**Spike begins (05:30):**
- CPU usage: 4.21% → 4.81% → 5.92%

**Heavy load period (06:30-10:00):**
- Multiple spikes: 13.58%, 17.17%, 16.82%, 16.98%, 14.31%, 14.63%

**Final spike (10:30):**
- **17.76% user + 17.19% system + 22.69% iowait = 57.64% total**
- This consumed the last CPU credits

---

## Why No OOM Killer?

The system did NOT run out of memory (no OOM killer invoked). Instead:
1. CPU credits exhausted → CPU throttled
2. System became too slow to respond
3. Memory pressure made it worse
4. AWS watchdog detected unresponsive instance
5. AWS forced a reboot

---

## Current System Configuration

**Instance Type**: T-series (likely t3.medium or t3a.medium)  
**CPU**: 2 vCPUs with baseline performance  
**RAM**: 4GB (3.7GB usable)  
**Swap**: 2GB configured  
**Disk**: 30GB (40% used)

**Services Running:**
- Nginx (main + 4 backend instances on ports 8081-8084)
- PHP 8.3-FPM (5 pools: www, pool1-4, 40 workers total)
- PostgreSQL 16
- Redis
- Laravel Horizon (queue workers)
- Fail2ban
- Cron jobs (system health monitoring, slow query extraction)

---

## What Caused the CPU Spike?

**Likely culprits (need investigation):**

1. **Laravel Horizon Workers**
   - Currently running multiple workers
   - May have been processing heavy jobs

2. **Database Queries**
   - Slow queries can cause high CPU
   - Need to check PostgreSQL logs

3. **Cron Jobs**
   - Multiple cron jobs running every 2 minutes
   - System health monitoring script
   - Slow query extraction script

4. **External Traffic**
   - Possible DDoS or bot attack
   - Need to check nginx access logs

5. **Windsurf IDE Connection**
   - Language server consuming 20% CPU (after reboot)
   - May have contributed to the issue

---

## Immediate Risks

### 1. **This WILL happen again**
- T-series instances are designed for burst workloads
- Current workload appears to be sustained high CPU
- CPU credits will exhaust again

### 2. **No monitoring/alerting**
- No alerts when CPU credits are low
- No alerts when memory pressure occurs
- No proactive notification before crash

### 3. **No graceful degradation**
- System goes from "working" to "completely dead"
- No warning signs visible to users

---

## Recommended Solutions

### Immediate Actions (Do Now)

1. **Monitor CPU Credits**
   ```bash
   # Check current CPU credit balance
   aws cloudwatch get-metric-statistics \
     --namespace AWS/EC2 \
     --metric-name CPUCreditBalance \
     --dimensions Name=InstanceId,Value=<instance-id> \
     --start-time $(date -u -d '1 hour ago' +%Y-%m-%dT%H:%M:%S) \
     --end-time $(date -u +%Y-%m-%dT%H:%M:%S) \
     --period 300 \
     --statistics Average
   ```

2. **Set up CloudWatch Alarms**
   - Alert when CPU credit balance < 100
   - Alert when CPU credit balance < 50
   - Alert when memory usage > 80%

3. **Identify CPU-heavy processes**
   ```bash
   # Run this periodically to log top CPU consumers
   ps aux --sort=-%cpu | head -20 >> /var/log/thetradevisor/cpu_usage.log
   ```

4. **Review Laravel Horizon configuration**
   - Check queue job processing
   - Look for stuck/long-running jobs
   - Consider reducing max workers

### Short-term Solutions (This Week)

1. **Switch to Unlimited Mode** (if T-series)
   - T3/T3a instances support "Unlimited" mode
   - Allows bursting beyond credits (at extra cost)
   - Prevents complete unresponsiveness
   - **Cost**: ~$0.05/vCPU-hour when bursting

2. **Optimize Current Workload**
   - Identify and fix slow database queries
   - Optimize Laravel Horizon workers
   - Reduce cron job frequency if possible
   - Add query caching

3. **Add Swap Usage Monitoring**
   - Swap is configured but not used
   - May help during memory pressure

### Long-term Solutions (Next Month)

1. **Upgrade to M-series Instance**
   - M6i.large: 2 vCPUs, 8GB RAM, no credit system
   - Cost: ~$0.096/hour (~$70/month)
   - Provides consistent performance
   - No CPU throttling

2. **Implement Proper Monitoring**
   - CloudWatch detailed monitoring
   - Custom metrics for application performance
   - Alerting via SNS/email
   - Dashboard for real-time visibility

3. **Load Testing**
   - Identify breaking points
   - Optimize before they cause issues
   - Plan capacity accordingly

4. **Database Optimization**
   - Review and optimize slow queries
   - Add missing indexes
   - Consider read replicas if needed

5. **Implement Rate Limiting**
   - Protect against traffic spikes
   - Prevent abuse/DDoS
   - Use Cloudflare rate limiting

---

## Investigation Tasks

### 1. Check what was running at 10:30
```bash
# Check nginx access logs for traffic spike
sudo tail -1000 /var/log/nginx/access.log | grep "21/Nov/2025:10:[23]"

# Check Laravel logs
tail -500 /var/www/thetradevisor.com/storage/logs/laravel.log

# Check PostgreSQL slow queries
sudo -u postgres psql -d thetradevisor -c "SELECT * FROM pg_stat_statements ORDER BY total_exec_time DESC LIMIT 20;"

# Check Horizon failed jobs
cd /var/www/thetradevisor.com && php artisan horizon:failed
```

### 2. Review cron jobs
```bash
# List all cron jobs
crontab -l
sudo crontab -l

# Check cron logs
grep CRON /var/log/syslog | grep "10:[23]"
```

### 3. Check for external attacks
```bash
# Check for unusual IPs
sudo awk '{print $1}' /var/log/nginx/access.log | sort | uniq -c | sort -rn | head -20

# Check for 404/500 errors
sudo grep " 404 \| 500 " /var/log/nginx/access.log | tail -100
```

---

## Prevention Checklist

- [ ] Enable T3 Unlimited mode OR upgrade to M-series
- [ ] Set up CloudWatch alarms for CPU credits
- [ ] Set up CloudWatch alarms for memory usage
- [ ] Implement application performance monitoring
- [ ] Review and optimize database queries
- [ ] Review Laravel Horizon configuration
- [ ] Add rate limiting to nginx
- [ ] Document incident response procedures
- [ ] Set up automated backups (if not already)
- [ ] Test disaster recovery procedures

---

## Resolution

### Instance Upgrade (November 21, 2025 - 11:03 UTC)

**Action Taken**: Upgraded from T-series to **M5.large** instance

**New Instance Specifications**:
- **Type**: M5.large (no CPU credit system)
- **vCPUs**: 2 (consistent performance, no throttling)
- **RAM**: 8GB (doubled from 4GB)
- **Network**: Up to 10 Gbps
- **Cost**: ~$0.096/hour (~$70/month)

**Benefits**:
- ✅ No CPU credit exhaustion possible
- ✅ Consistent CPU performance at all times
- ✅ Doubled memory (4GB → 8GB)
- ✅ Better network performance
- ✅ No risk of sudden unresponsiveness

### Post-Upgrade Configuration

After the upgrade, the backend nginx instances (ports 8081-8084) were not migrated. The system was reconfigured to use direct PHP-FPM connection instead of the load balancer architecture:

**Changes Made**:
1. Simplified nginx configuration to connect directly to PHP-FPM
2. Removed dependency on backend nginx instances
3. Fixed multi-tenant route issues in public views
4. Cleared all Laravel caches

**Verification**:
```bash
$ curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" https://thetradevisor.com/
HTTP Status: 200

$ systemctl status nginx php8.3-fpm postgresql@16-main
All services: Active (running)

$ free -h
Mem:  7.6Gi total, 2.6Gi used, 4.5Gi free
```

**Site Status**: ✅ Fully operational

## Conclusion

The server crash was caused by CPU credit exhaustion on a T-series instance, exacerbated by memory pressure. The system became completely unresponsive and required an automatic reboot by AWS.

**Resolution**: Upgraded to M5.large instance, eliminating the CPU credit system entirely. This provides consistent performance and prevents future crashes from CPU throttling.

**Incidents**: The crash occurred **3 times total** before the upgrade was performed.

**Current Status**: System is stable on M5.large with 8GB RAM and consistent CPU performance.

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
