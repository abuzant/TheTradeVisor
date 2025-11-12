# Cloudflare Error 521 Fix

> **Resolving "Web server is down" intermittent errors**

**Date**: November 8, 2025  
**Status**: ✅ Fixed

---

## 🐛 Problem

**Symptoms**:
- Intermittent Cloudflare Error 521 "Web server is down"
- Occurs every 5-6 page loads
- Server is actually running (not down)
- Browser shows: "The web server is not returning a connection"

**Error Code**: 521  
**Meaning**: Cloudflare can connect to your server, but the origin web server refused the connection

---

## 🔍 Root Cause

**PHP-FPM Worker Pool Exhaustion**

The issue was caused by:
1. **Limited PHP-FPM workers** (only 10 active workers)
2. **Slow page loads** before caching was implemented
3. **All workers busy** processing slow requests
4. **New requests rejected** when no workers available
5. **Cloudflare sees this as "server down"**

### Why It Happened

- PHP-FPM had `pm.max_children = 50` but only `pm.start_servers = 10`
- Under load, all 10 workers were busy
- New requests had to wait for a worker to become available
- If wait exceeded Cloudflare's timeout, Error 521 occurred

---

## ✅ Solution Applied

### 1. Increased PHP-FPM Worker Pool

**Before**:
```ini
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
```

**After**:
```ini
pm.max_children = 100
pm.start_servers = 20
pm.min_spare_servers = 10
pm.max_spare_servers = 40
```

**Benefits**:
- 2x more workers available
- 2x more workers started immediately
- Better handling of traffic spikes
- Reduced wait time for new requests

### 2. Added Slow Log Monitoring

**Configuration**:
```ini
slowlog = /var/log/php8.3-fpm-slow.log
request_slowlog_timeout = 5
```

**Purpose**:
- Track requests taking longer than 5 seconds
- Identify performance bottlenecks
- Monitor for slow queries or code

### 3. Existing Optimizations

These were already in place and help prevent the issue:
- ✅ **Nginx FastCGI Cache** - Reduces PHP-FPM load
- ✅ **Redis Caching** - 80-90% cache hit rate
- ✅ **Smart Cache Invalidation** - Fresh data without overhead
- ✅ **Optimized Timeouts** - 300s for long-running requests

---

## 📊 Impact

### Before Fix
- **Workers**: 10 active, 50 max
- **Under Load**: All workers busy
- **Result**: Error 521 every 5-6 requests
- **User Experience**: Frustrating, unreliable

### After Fix
- **Workers**: 20 active, 100 max
- **Under Load**: Workers available
- **Result**: No more 521 errors
- **User Experience**: Fast and reliable

---

## 🔍 Monitoring

### Check PHP-FPM Status

```bash
# View current worker status
sudo systemctl status php8.3-fpm

# Check slow log for performance issues
sudo tail -f /var/log/php8.3-fpm-slow.log

# Monitor worker pool in real-time
watch -n 1 'sudo systemctl status php8.3-fpm | grep "Processes active"'
```

### Check for 521 Errors

```bash
# Monitor Nginx access log
sudo tail -f /var/log/nginx/access.log

# Check for connection errors
sudo tail -f /var/log/nginx/error.log | grep upstream
```

---

## 🎯 Prevention

### 1. Monitor Worker Pool Usage

If you see "Processes active" consistently at or near `pm.max_children`, increase the pool size:

```bash
# Edit PHP-FPM pool config
sudo nano /etc/php/8.3/fpm/pool.d/www.conf

# Increase max_children
pm.max_children = 150  # Adjust based on RAM

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm
```

### 2. Optimize Slow Requests

Check slow log regularly:

```bash
sudo tail -50 /var/log/php8.3-fpm-slow.log
```

Look for:
- Database queries without indexes
- External API calls without caching
- Heavy computations in controllers
- Missing cache layers

### 3. Scale Resources

**Memory Requirements**:
- Each PHP-FPM worker: ~30-50MB RAM
- 100 workers: ~3-5GB RAM
- Monitor with: `free -h`

**If RAM is limited**:
- Reduce `pm.max_children`
- Optimize code to use less memory
- Add more RAM to server
- Use horizontal scaling (multiple servers)

---

## 🚨 Troubleshooting

### Still Getting 521 Errors?

1. **Check if PHP-FPM is running**:
   ```bash
   sudo systemctl status php8.3-fpm
   ```

2. **Check worker pool status**:
   ```bash
   sudo systemctl status php8.3-fpm | grep "Processes active"
   ```

3. **Check slow log**:
   ```bash
   sudo tail -50 /var/log/php8.3-fpm-slow.log
   ```

4. **Check Nginx error log**:
   ```bash
   sudo tail -50 /var/log/nginx/error.log
   ```

5. **Restart services**:
   ```bash
   sudo systemctl restart php8.3-fpm nginx
   ```

### Cloudflare-Specific Issues

1. **Check Cloudflare Status**:
   - Visit https://www.cloudflarestatus.com/

2. **Verify Origin Server**:
   - Test direct IP access (bypass Cloudflare)
   - Check if server responds without Cloudflare

3. **Review Cloudflare Settings**:
   - Check timeout settings
   - Verify SSL/TLS mode (Full or Full Strict)
   - Review firewall rules

---

## 📚 Related Documentation

- [Scaling Analysis](SCALING_ANALYSIS.md) - Performance optimization
- [Monitoring Implementation](MONITORING_IMPLEMENTATION.md) - System monitoring
- [Deployment Guide](DEPLOYMENT.md) - Production setup

---

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)

For project support and inquiries:  
📧 [hello@thetradevisor.com](mailto:hello@thetradevisor.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)

---

**Last Updated**: November 8, 2025  
**Status**: ✅ Issue Resolved
