# Instance Upgrade Summary - M5.large Migration

**Date**: November 21, 2025  
**Previous Instance**: T-series (likely t3.medium)  
**New Instance**: M5.large  
**Reason**: CPU credit exhaustion causing complete server unresponsiveness

---

## What Changed

### Hardware
- **CPU**: 2 vCPUs (T-series burstable → M5 consistent performance)
- **RAM**: 4GB → 8GB (doubled)
- **Network**: Improved bandwidth and performance
- **Cost**: ~$70/month (from ~$30/month)

### Architecture Simplification
The previous multi-instance nginx load balancer setup was simplified:

**Before**:
```
Internet → Nginx (main) → Backend Nginx (8081-8084) → PHP-FPM
```

**After**:
```
Internet → Nginx → PHP-FPM (direct)
```

**Reason**: Backend nginx instances were not preserved during migration, and the simpler architecture is more appropriate for current scale.

### Configuration Files Changed

1. **`/etc/nginx/sites-enabled/thetradevisor.com`**
   - Removed upstream backend_pool configuration
   - Changed from proxy_pass to fastcgi_pass
   - Direct connection to PHP-FPM socket

2. **Laravel Views** (public-facing)
   - Changed `route('name')` to `url('/path')` for public routes
   - Fixed multi-tenant routing issues
   - Files affected:
     - `resources/views/public/landing.blade.php`
     - `resources/views/components/public-layout.blade.php`
     - `resources/views/components/unified-footer.blade.php`

### Backups Created
- `/etc/nginx/sites-enabled/thetradevisor.com.backup-before-m5-fix`

---

## Current System Status

### Instance Details
```bash
Instance Type: M5.large
vCPUs: 2 (Intel Xeon Platinum 8259CL @ 2.50GHz)
RAM: 8GB
Swap: 2GB
Disk: 30GB (40% used)
```

### Services Running
- ✅ Nginx (main web server)
- ✅ PHP 8.3-FPM (5 pools: www, pool1-4)
- ✅ PostgreSQL 16
- ✅ Redis
- ✅ Laravel Horizon (queue workers)
- ✅ Fail2ban
- ✅ Cron jobs

### Performance
```bash
Memory: 2.6GB used / 7.6GB total (34%)
Load Average: 0.40, 0.43, 0.20
Site Response: 200 OK (~150ms)
```

---

## Benefits of M5.large

### 1. **No CPU Credit System**
- T-series instances use CPU credits that can be exhausted
- M5 provides consistent baseline performance
- No risk of sudden CPU throttling
- No need to monitor CPU credit balance

### 2. **Doubled Memory**
- 4GB → 8GB RAM
- Better handling of traffic spikes
- More room for caching
- Reduced memory pressure

### 3. **Predictable Performance**
- Consistent CPU performance 24/7
- No performance degradation during high load
- Better for production workloads

### 4. **Better Network**
- Up to 10 Gbps network bandwidth
- Lower latency
- Better for API-heavy applications

---

## What to Monitor

### Critical Metrics
1. **Memory Usage**
   - Current: 34% (2.6GB / 7.6GB)
   - Alert if > 80%
   - Consider upgrade if consistently > 70%

2. **CPU Usage**
   - No longer limited by credits
   - Monitor for sustained high usage
   - Alert if > 80% for extended periods

3. **Disk Space**
   - Current: 40% (12GB / 30GB)
   - Alert if > 80%
   - Clean up logs regularly

4. **Database Performance**
   - Monitor slow queries
   - Check connection pool usage
   - Review query optimization

### Recommended CloudWatch Alarms
```bash
# Memory
CPUUtilization > 80% for 5 minutes
MemoryUtilization > 80% for 5 minutes

# Disk
DiskSpaceUtilization > 80%

# Network
NetworkIn > threshold (set based on baseline)
NetworkOut > threshold (set based on baseline)

# Status Checks
StatusCheckFailed (any)
```

---

## Cost Comparison

| Instance Type | vCPU | RAM | Cost/Month | Notes |
|---------------|------|-----|------------|-------|
| t3.medium | 2 | 4GB | ~$30 | Burstable, credit-based |
| t3a.medium | 2 | 4GB | ~$27 | AMD, burstable |
| **m5.large** | **2** | **8GB** | **~$70** | **Consistent performance** |
| m5.xlarge | 4 | 16GB | ~$140 | Future upgrade path |

**ROI**: The $40/month increase eliminates:
- Downtime from CPU exhaustion (3 incidents)
- Emergency troubleshooting time
- Potential revenue loss from outages
- Stress and uncertainty

---

## Future Considerations

### When to Upgrade Further

**To M5.xlarge (4 vCPU, 16GB RAM)** if:
- Memory usage consistently > 70%
- CPU usage consistently > 70%
- Database queries become slow
- User base grows significantly
- Adding more features/services

**To M5.2xlarge (8 vCPU, 32GB RAM)** if:
- Running multiple heavy services
- High concurrent user load
- Complex analytics processing
- Real-time data processing needs

### Alternative Architectures

If the application grows significantly, consider:
1. **Separate Database Server** (RDS PostgreSQL)
2. **Load Balancer** (ALB) with multiple app servers
3. **Caching Layer** (ElastiCache Redis)
4. **CDN** (CloudFront) for static assets
5. **Auto Scaling** based on load

---

## Lessons Learned

1. **T-series instances are NOT suitable for production workloads with sustained CPU usage**
   - They're designed for burst workloads
   - CPU credits can exhaust silently
   - System becomes unresponsive with no warning

2. **Monitor CPU credits on T-series instances**
   - Set alarms when credits < 100
   - Critical alarm when credits < 50
   - Emergency response when credits = 0

3. **Simplicity is better than premature optimization**
   - The 4-instance nginx backend was overkill for current scale
   - Direct PHP-FPM connection is simpler and sufficient
   - Add complexity only when needed

4. **Multi-tenant routing requires careful handling**
   - Named routes with domain parameters can break
   - Direct URLs are more reliable for public pages
   - Test thoroughly after infrastructure changes

5. **Always have backups before major changes**
   - Configuration backups saved
   - Database backups in place
   - Quick rollback plan ready

---

## Action Items

### Completed ✅
- [x] Upgrade to M5.large
- [x] Reconfigure nginx for direct PHP-FPM
- [x] Fix multi-tenant routing issues
- [x] Verify site functionality
- [x] Document changes

### Recommended Next Steps
- [ ] Set up CloudWatch detailed monitoring
- [ ] Configure CloudWatch alarms (memory, CPU, disk)
- [ ] Review and optimize slow database queries
- [ ] Implement application performance monitoring (APM)
- [ ] Set up automated backup verification
- [ ] Create disaster recovery runbook
- [ ] Load test the new instance
- [ ] Review Laravel Horizon configuration
- [ ] Optimize cron job frequency if needed
- [ ] Document standard operating procedures

---

## Emergency Contacts

**If the server becomes unresponsive again**:
1. Check AWS Console for instance status
2. Check CloudWatch metrics
3. SSH into server and check:
   - `systemctl status nginx php8.3-fpm postgresql@16-main`
   - `free -h` (memory)
   - `df -h` (disk)
   - `top` (processes)
4. Check logs:
   - `/var/log/nginx/thetradevisor-error.log`
   - `/var/www/thetradevisor.com/storage/logs/laravel.log`
   - `journalctl -xe`

**Escalation**:
- System Administrator: Ruslan Abuzant
- Email: ruslan@abuzant.com
- Support: hello@thetradevisor.com

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
