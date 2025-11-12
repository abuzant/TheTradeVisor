# TheTradeVisor Multi-Instance Architecture Deployment Guide

## Overview

This deployment implements a multi-instance architecture with:
- **1 Load Balancer Nginx** (port 443) - handles SSL and distributes traffic
- **4 Backend Nginx instances** (ports 8081-8084) - serve the application
- **4 PHP-FPM pools** (ports 9001-9004) - process PHP requests

## Architecture Diagram

```
Cloudflare → Load Balancer Nginx (:443)
                    ↓
            [least_conn algorithm]
                    ↓
    ┌───────────┬───────────┬───────────┬───────────┐
    ↓           ↓           ↓           ↓           ↓
Backend-1   Backend-2   Backend-3   Backend-4
(:8081)     (:8082)     (:8083)     (:8084)
    ↓           ↓           ↓           ↓
PHP-FPM-1   PHP-FPM-2   PHP-FPM-3   PHP-FPM-4
(:9001)     (:9002)     (:9003)     (:9004)
```

## Pre-Deployment Checklist

- [x] PHP-FPM pool configurations created
- [x] Backend nginx configurations created
- [x] Load balancer configuration created
- [x] Management scripts created
- [x] refurbish.sh updated
- [x] ServiceController updated
- [ ] Configuration tested
- [ ] Instances started
- [ ] Load balancer activated

## Configuration Files

### PHP-FPM Pools
- `/etc/php/8.3/fpm/pool.d/pool1.conf` → Port 9001
- `/etc/php/8.3/fpm/pool.d/pool2.conf` → Port 9002
- `/etc/php/8.3/fpm/pool.d/pool3.conf` → Port 9003
- `/etc/php/8.3/fpm/pool.d/pool4.conf` → Port 9004

### Backend Nginx
- `/etc/nginx/backends/nginx-backend-1-master.conf` → Port 8081
- `/etc/nginx/backends/nginx-backend-2-master.conf` → Port 8082
- `/etc/nginx/backends/nginx-backend-3-master.conf` → Port 8083
- `/etc/nginx/backends/nginx-backend-4-master.conf` → Port 8084

### Load Balancer
- `/etc/nginx/sites-enabled/thetradevisor.com` → Port 443 (SSL)

### Backup
- `/etc/nginx/sites-enabled/thetradevisor.com.backup.multi-instance` → Original config

## Deployment Steps

### Step 1: Test Configuration Syntax

```bash
# Test PHP-FPM configuration
sudo php-fpm8.3 -t

# Test backend nginx configs
for i in 1 2 3 4; do
    echo "Testing backend-${i}..."
    sudo nginx -t -c /etc/nginx/backends/nginx-backend-${i}-master.conf
done

# Test load balancer config
sudo nginx -t
```

### Step 2: Restart PHP-FPM (loads all pools)

```bash
sudo systemctl restart php8.3-fpm
sudo systemctl status php8.3-fpm
```

### Step 3: Start Backend Instances

```bash
cd /www
./start-backends.sh
```

### Step 4: Reload Load Balancer

```bash
sudo systemctl reload nginx
```

### Step 5: Verify Status

```bash
cd /www
./status-backends.sh
```

## Management Scripts

### Start All Backends
```bash
./start-backends.sh
```

### Stop All Backends
```bash
./stop-backends.sh
```

### Check Status
```bash
./status-backends.sh
```

### Refurbish (Clear Caches & Restart)
```bash
./refurbish.sh
```

## Monitoring & Debugging

### Log Files

**PHP-FPM Pools:**
- `/var/log/php8.3-fpm-pool1.log`
- `/var/log/php8.3-fpm-pool2.log`
- `/var/log/php8.3-fpm-pool3.log`
- `/var/log/php8.3-fpm-pool4.log`

**Backend Nginx:**
- `/var/log/nginx/backend-1-access.log` & `backend-1-error.log`
- `/var/log/nginx/backend-2-access.log` & `backend-2-error.log`
- `/var/log/nginx/backend-3-access.log` & `backend-3-error.log`
- `/var/log/nginx/backend-4-access.log` & `backend-4-error.log`

**Load Balancer:**
- `/var/log/nginx/thetradevisor-access.log`
- `/var/log/nginx/thetradevisor-error.log`

### Tail All Backend Logs
```bash
tail -f /var/log/nginx/backend-*-error.log
```

### Tail All PHP-FPM Logs
```bash
tail -f /var/log/php8.3-fpm-pool*.log
```

### Check Which Backend Handled Request
Look for `X-Backend-Instance` header in responses:
```bash
curl -I https://thetradevisor.com | grep X-Backend-Instance
```

### Monitor Load Distribution
```bash
watch -n 1 'tail -n 1 /var/log/nginx/backend-*-access.log'
```

## Troubleshooting

### Backend Won't Start
```bash
# Check if port is already in use
sudo netstat -tlnp | grep 808[1-4]

# Check nginx error log
sudo tail -50 /var/log/nginx/backend-X-error.log

# Test config syntax
sudo nginx -t -c /etc/nginx/backends/nginx-backend-X-master.conf
```

### PHP-FPM Pool Not Responding
```bash
# Check if pool is listening
sudo netstat -tlnp | grep 900[1-4]

# Check PHP-FPM status
sudo systemctl status php8.3-fpm

# Check pool logs
sudo tail -50 /var/log/php8.3-fpm-poolX.log
```

### Load Balancer Not Distributing
```bash
# Check upstream status in nginx
sudo tail -50 /var/log/nginx/thetradevisor-error.log | grep upstream

# Test backend connectivity
for i in 1 2 3 4; do
    curl -I http://127.0.0.1:808${i}
done
```

### 521 Errors Still Occurring
1. Check if all backends are running: `./status-backends.sh`
2. Monitor slow queries: `tail -f /var/log/php8.3-fpm-pool*-slow.log`
3. Check database connections: `psql -c "SELECT count(*) FROM pg_stat_activity;"`
4. Verify Cloudflare settings (keepalive, timeouts)

## Rollback Procedure

If you need to rollback to single-instance:

```bash
# Stop all backends
./stop-backends.sh

# Restore original nginx config
sudo cp /etc/nginx/sites-enabled/thetradevisor.com.backup.multi-instance /etc/nginx/sites-enabled/thetradevisor.com

# Disable new pools (rename them)
for i in 1 2 3 4; do
    sudo mv /etc/php/8.3/fpm/pool.d/pool${i}.conf /etc/php/8.3/fpm/pool.d/pool${i}.conf.disabled
done

# Restart services
sudo systemctl restart php8.3-fpm
sudo systemctl reload nginx
```

## Performance Tuning

### Adjust Pool Size
Edit `/etc/php/8.3/fpm/pool.d/poolX.conf`:
```ini
pm.max_children = 25        ; Increase if needed
pm.start_servers = 5
pm.min_spare_servers = 3
pm.max_spare_servers = 10
```

### Adjust Backend Workers
Edit `/etc/nginx/backends/nginx-backend-X-master.conf`:
```nginx
worker_processes 2;         ; Increase if needed
worker_connections 1024;
```

### Adjust Load Balancer Algorithm
Edit `/etc/nginx/sites-enabled/thetradevisor.com`:
```nginx
upstream backend_pool {
    least_conn;             ; Options: least_conn, ip_hash, round_robin
    # ...
}
```

## Admin Panel Integration

The admin panel at `/admin/services` now shows:
- Load Balancer status
- PHP-FPM (All Pools) status
- Individual backend instance status (1-4)
- Restart buttons for each component

## Maintenance

### Weekly
- Review error logs for patterns
- Check load distribution balance
- Monitor database connection usage

### Monthly
- Review and adjust pool sizes based on traffic
- Analyze slow query logs
- Update and test rollback procedure

## Support

For issues or questions:
1. Check logs first (see Monitoring section)
2. Run `./status-backends.sh` to verify all components
3. Review this guide's Troubleshooting section
4. Check Laravel logs: `tail -f storage/logs/laravel.log`

---

**Deployment Date:** [To be filled]
**Deployed By:** [To be filled]
**Status:** Ready for deployment


---

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
�� Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)

For project support and inquiries:  
📧 [hello@thetradevisor.com](mailto:hello@thetradevisor.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)
