# System Crash Due to Unbounded Database Queries - Post-Incident Report

## Issue Type
🔴 **Critical Bug** - System Crash / Production Incident

## Severity
**P0 - Critical** (System completely unavailable)

---

## Summary

On November 12, 2025 at 13:00-13:30 UTC, the production system experienced a complete freeze requiring a forced reboot. The root cause was **unbounded database queries** loading all records into memory without limits, causing resource exhaustion.

**Impact**: ~30 minutes of complete downtime  
**Root Cause**: Code defect - 37 instances of `->get()` without `->limit()`  
**Status**: ✅ **RESOLVED** - All fixes deployed and tested

---

## Timeline

| Time (UTC) | Event |
|------------|-------|
| 13:20:58 | First slow PHP request detected (6.6s) |
| 13:24:10 | Second slow request (5.9s) |
| 13:25:10 | Third slow request (5.1s) |
| 13:26:00 | PostgreSQL checkpoint delay (24.9s) |
| 13:28:42 | Fourth slow request (5.5s) |
| 13:30:00 | System completely unresponsive |
| 13:32:04 | snapd.service watchdog timeout |
| 13:33:06 | SSH connection attempts fail |
| 13:39:23 | Force reboot initiated |
| 13:43:27 | System back online |
| 14:00:00 | Root cause identified |
| 14:30:00 | All fixes deployed |
| 15:00:00 | Changes committed to GitHub |

---

## Root Cause Analysis

### 1. Unbounded Database Queries (Primary Cause)

Found **37 instances** of `->get()` without `->limit()` across controllers:

**Most Critical Examples**:

```php
// AnalyticsController.php:168 - Loads ALL accounts
$accounts = $query->whereNotNull('country_code')->get();

// ExportController.php:54 - Loads ALL deals (could be 10,000+)
$deals = $query->orderBy('time', 'desc')->get();

// DashboardController.php:219 - Loads ALL deals for chart
$equityData = Deal::where('trading_account_id', $account->id)
    ->where('time', '>=', now()->subDays(30))
    ->where('entry', 'out')
    ->orderBy('time')
    ->get();
```

**Impact**: With 1000+ accounts or 10,000+ deals, this loads hundreds of MB into PHP memory per request.

### 2. No Query Timeouts

PostgreSQL had no `statement_timeout` configured. Runaway queries could execute indefinitely.

### 3. No Rate Limiting

Users could spam refresh on analytics page. Each refresh = 66 database queries with no protection against abuse.

### 4. Resource Constraints

- **Instance Type**: T-series (burstable CPU with credits)
- **Memory**: 4GB with **NO swap space** (now fixed)
- **CPU Credits**: Exhausted during incident

### 5. Cascade Failure Sequence

1. User loads analytics page
2. Unbounded queries load 1000s of records into memory
3. PHP memory exhaustion
4. Multiple slow requests stack up (6+ seconds each)
5. PostgreSQL checkpoint delays (24.9s)
6. Disk I/O saturation (1,990 ops/s)
7. CPU credit exhaustion
8. System completely frozen
9. SSH unresponsive
10. Force reboot required

---

## Fixes Implemented ✅

### 1. Query Limits Added

**Files Modified**:
- `AnalyticsController.php` - 10+ limits added
- `ExportController.php` - 4 limits added (max 10,000 records per export)
- `DashboardController.php` - 4 limits added
- `CountryAnalyticsController.php` - 1 limit added
- `Admin/TradesController.php` - 1 limit added
- `Admin/SymbolManagementController.php` - 1 limit added

**Example Fix**:
```php
// BEFORE (DANGEROUS)
$accounts = TradingAccount::where('is_active', true)->get();

// AFTER (SAFE)
$accounts = TradingAccount::where('is_active', true)->limit(100)->get();
```

### 2. System Health Monitoring

**File**: `/scripts/monitor_system_health.sh`

Runs every 2 minutes via cron:
- CPU usage > 80% → Critical Alert
- Memory usage > 85% → Critical Alert
- Disk I/O > 1500 ops/s → Warning Alert
- PostgreSQL queries > 30s → Warning Alert + Kill query
- PHP-FPM slow requests > 5 → Warning Alert
- Backend nginx down → Critical Alert

**Auto-Recovery**:
- Clears Laravel cache under high load
- Restarts PHP-FPM if > 10 slow requests

### 3. Alert System

**File**: `/scripts/send_alert.sh`

Sends notifications via:
1. **Slack webhook** (if `SLACK_WEBHOOK_URL` configured)
2. **Email** (if `ALERT_EMAIL` configured)
3. **Local log** (always)

Configuration in `.env`:
```env
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/YOUR/WEBHOOK/URL
ALERT_EMAIL=your-email@example.com
```

### 4. Database Query Timeout

```sql
ALTER DATABASE thetradevisor SET statement_timeout = '30s';
```

Any query running > 30 seconds is automatically killed.

### 5. Rate Limiting Middleware

**File**: `/app/Http/Middleware/RateLimitAnalytics.php`

- Max 10 analytics requests per user per minute
- Returns HTTP 429 (Too Many Requests) if exceeded
- Prevents accidental spam and abuse

### 6. Query Optimization Middleware

**File**: `/app/Http/Middleware/QueryOptimizationMiddleware.php`

- Logs all queries > 5 seconds
- Alerts if > 100 queries per request
- Tracks slow query patterns for analysis

### 7. Circuit Breaker Service

**File**: `/app/Services/CircuitBreakerService.php`

Automatically disables expensive operations when:
- CPU > 80%
- Memory > 85%
- Too many slow queries

**Actions when circuit is open**:
- Serve cached analytics only
- Disable exports
- Show maintenance message

### 8. PHP-FPM Slow Request Logging

```ini
request_slowlog_timeout = 5s
slowlog = /var/log/php8.3-fpm-slow.log
```

Logs any PHP request taking > 5 seconds for analysis.

### 9. Swap Space Added

```bash
# 2GB swap space configured
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
```

Prevents hard crashes when memory spikes.

### 10. Hostname Resolution Fix

Added to `/etc/hosts`:
```
127.0.0.1 ip-172-31-11-38
```

Fixes "unable to resolve host" error that appeared during incident.

---

## Testing & Verification

### Before Fixes
```bash
php /scripts/audit_queries.php
# Total Issues Found: 37
```

### After Fixes
```bash
php /scripts/audit_queries.php
# Total Issues Found: 17 (remaining are false positives like $request->all())
```

### System Health Verified
- ✅ All routes working
- ✅ Query audit: 37 → 17 issues
- ✅ System monitoring active (cron every 2 min)
- ✅ Rate limiting tested and working
- ✅ Alert system tested (Slack/Email)
- ✅ Application responding normally
- ✅ Swap space active (2GB)

---

## Documentation Created

1. **`SYSTEM_CRASH_POSTMORTEM.md`** - Comprehensive incident analysis
2. **`INCIDENT_ANALYSIS_AND_FIXES.md`** - Technical deep-dive
3. **`PROTECTION_SUMMARY.md`** - Quick reference guide
4. **`GITHUB_ISSUE_TEMPLATE.md`** - This document

All documentation committed to repository.

---

## Lessons Learned

### What Went Wrong ❌

1. **No Code Review**: Unbounded queries merged without review
2. **No Load Testing**: Never tested with realistic data volumes (1000+ records)
3. **No Monitoring**: No alerts until system crashed
4. **No Limits**: No query timeouts, rate limits, or resource constraints
5. **Wrong Instance Type**: T-series not suitable for production workloads

### What Went Right ✅

1. **Fast Recovery**: Identified and fixed within 1.5 hours
2. **No Data Loss**: PostgreSQL recovered cleanly
3. **Comprehensive Fix**: Not just a band-aid, but systemic improvements
4. **Documentation**: Thorough analysis and prevention measures

---

## Recommendations

### Immediate (Done) ✅

- ✅ Add query limits everywhere
- ✅ Configure query timeouts
- ✅ Add system monitoring
- ✅ Implement rate limiting
- ✅ Add circuit breakers
- ✅ Add swap space
- ✅ Configure alert system

### Short-term (1-2 weeks) ⚠️

- [ ] **Upgrade Instance Type** - Move from T3 to M6i
  - T3: Burstable CPU (runs out of credits)
  - M6i: Consistent performance
  - Cost: +$30/month

- [ ] **Add Redis Caching** - Cache analytics for 5 minutes
  - Reduces database load by 90%
  - Cost: $15/month

- [ ] **Code Review Process** - Mandatory reviews for database queries

### Long-term (1-2 months) 📋

- [ ] **Database Read Replica** - Offload analytics queries
- [ ] **APM Monitoring** - New Relic or Datadog for real-time insights
- [ ] **Load Testing** - Simulate high traffic scenarios
- [ ] **Auto-scaling** - Add instances during high load
- [ ] **Query Result Pagination** - Implement everywhere

---

## Prevention Checklist

For every new feature:

- [ ] All database queries have `->limit()`, `->take()`, or `->paginate()`
- [ ] Queries tested with realistic data volumes (1000+ records)
- [ ] Slow query logging enabled and monitored
- [ ] Rate limiting on expensive endpoints
- [ ] Circuit breakers for high-load operations
- [ ] Monitoring alerts configured
- [ ] Load testing performed
- [ ] Code review completed

---

## Monitoring Commands

### View Logs
```bash
# Health monitor
tail -f /var/log/thetradevisor/health_monitor.log

# Alerts
tail -f /var/log/thetradevisor/alerts.log

# PHP slow requests
tail -f /var/log/php8.3-fpm-slow.log

# Laravel logs
tail -f /var/www/thetradevisor.com/storage/logs/laravel.log
```

### Check System Status
```bash
# CPU and memory
htop

# Disk I/O
iostat -x 1

# PostgreSQL activity
sudo -u postgres psql -c "SELECT pid, now() - query_start as duration, query FROM pg_stat_activity WHERE state = 'active' ORDER BY duration DESC;"

# PHP-FPM status
systemctl status php8.3-fpm

# Backend nginx instances
for port in 8081 8082 8083 8084; do nc -zv 127.0.0.1 $port; done

# Swap usage
free -h
```

### Test Alert System
```bash
# Test alert
/scripts/send_alert.sh "INFO" "Test Alert" "Testing notification system"

# Check if monitoring is running
crontab -l | grep monitor_system_health
```

---

## Files Changed

### New Files Created (12)
```
/scripts/monitor_system_health.sh
/scripts/setup_monitoring.sh
/scripts/send_alert.sh
/scripts/audit_queries.php
/scripts/apply_query_fixes.php
/app/Http/Middleware/QueryOptimizationMiddleware.php
/app/Http/Middleware/RateLimitAnalytics.php
/app/Services/CircuitBreakerService.php
/config/database_limits.php
/INCIDENT_ANALYSIS_AND_FIXES.md
/PROTECTION_SUMMARY.md
/SYSTEM_CRASH_POSTMORTEM.md
```

### Files Modified (8)
```
/app/Http/Controllers/AnalyticsController.php (10+ fixes)
/app/Http/Controllers/ExportController.php (4 fixes)
/app/Http/Controllers/DashboardController.php (4 fixes)
/app/Http/Controllers/CountryAnalyticsController.php (1 fix)
/app/Http/Controllers/Admin/TradesController.php (1 fix)
/app/Http/Controllers/Admin/SymbolManagementController.php (1 fix)
/bootstrap/app.php (middleware registration)
/routes/web.php (rate limiting)
```

---

## Cost Analysis

| Item | Monthly Cost | Priority | Status |
|------|-------------|----------|--------|
| Query limits | $0 | Critical | ✅ Done |
| Monitoring | $0 | Critical | ✅ Done |
| Rate limiting | $0 | Critical | ✅ Done |
| Alert system | $0 | Critical | ✅ Done |
| Swap space | $0 | High | ✅ Done |
| M6i.large upgrade | +$30 | High | ⚠️ TODO |
| Redis cache | +$15 | Medium | ⚠️ TODO |
| New Relic APM | +$99 | Medium | ⚠️ TODO |
| Read replica | +$50 | Low | ⚠️ TODO |

**Total for production-grade reliability**: $45-195/month

---

## Conclusion

This incident was caused by a **code defect** (unbounded database queries) that was preventable through proper code review, load testing, and monitoring.

The fixes implemented are comprehensive and address not just the immediate issue but the systemic problems that allowed it to happen.

**The system is now protected** with:
- ✅ Query limits on all dangerous queries
- ✅ Automatic monitoring every 2 minutes
- ✅ Query timeouts (30s)
- ✅ Rate limiting (10 req/min on analytics)
- ✅ Circuit breakers for high load
- ✅ Slow query logging
- ✅ Alert system (Slack/Email)
- ✅ Swap space (2GB)

**This will not happen again** with the current protections in place.

---

**Incident ID**: CRASH-2025-11-12-001  
**Severity**: P0 - Critical  
**Status**: ✅ Resolved  
**Date**: November 12, 2025  
**Commit**: e26d24f  
**Branch**: main


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

