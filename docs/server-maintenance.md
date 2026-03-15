# Server Maintenance Guide

## Overview

This guide covers routine maintenance tasks and automated systems in place for TheTradeVisor application.

## Automated Cleanup Systems

### JSON File Cleanup
- **Script**: `/vhosts/thetradevisor.com/scripts/cleanup_old_json_files.sh`
- **Purpose**: Removes JSON files older than 7 days from `storage/app/raw_data/`
- **Schedule**: Every Sunday at 3:00 AM (via cron)
- **Retention**: 7 days (configurable)
- **Logs**: `/var/log/thetradevisor/json_cleanup.log`

```bash
# Manual cleanup (dry-run)
sudo /vhosts/thetradevisor.com/scripts/cleanup_old_json_files.sh true

# Actual cleanup
sudo /vhosts/thetradevisor.com/scripts/cleanup_old_json_files.sh false
```

### Backup Cleanup
- **Script**: `/vhosts/thetradevisor.com/scripts/cleanup_backups.sh`
- **Purpose**: Keeps only the last 5 backups
- **Location**: `/vhosts/thetradevisor.com/backups/`

## Monitoring Scripts

### System Health Monitor
- **Script**: `/vhosts/thetradevisor.com/scripts/monitor_system_health.sh`
- **Purpose**: Monitors CPU, memory, disk I/O, PostgreSQL, and PHP-FPM
- **Schedule**: Every 2 minutes (recommended via cron)
- **Logs**: `/var/log/thetradevisor/health_monitor.log`

### Database Lock Monitor
- **Script**: `/vhosts/thetradevisor.com/scripts/monitor_database_locks.sh`
- **Purpose**: Detects and resolves database lock contention
- **Database**: Uses Docker PostgreSQL container `thetradevisor_app`

## Service Management

### Supervisor (Queue Workers)
Horizon and queue workers are managed by Supervisor:

```bash
# Check status
sudo supervisorctl status

# Restart all
sudo supervisorctl restart all

# View logs
tail -f /vhosts/thetradevisor.com/storage/logs/horizon.log
tail -f /vhosts/thetradevisor.com/storage/logs/worker.log
```

### Docker Services
- **PostgreSQL**: Container `postgres`
- **Redis**: Container `redis`
- **Telegram Bot**: Container `telegram-bot`

```bash
# Check containers
docker ps

# Restart a container
docker restart postgres
```

## PHP-FPM Pools
Active pools:
- `thetradevisor.conf` - Main application
- `www.conf` - Default pool
- `sarcastic.conf` - Sarcastic.news

## Cache Management

### Full Cache Refresh
```bash
/vhosts/thetradevisor.com/scripts/refurbish.sh
```

This script:
1. Clears Laravel caches
2. Flushes Redis
3. Restarts Docker containers
4. Restarts PHP-FPM and Nginx

## Health Check
Quick health status:
```bash
/vhosts/thetradevisor.com/scripts/healthcheck.sh
```

## Log Locations
- Application logs: `/vhosts/thetradevisor.com/storage/logs/laravel.log`
- Horizon logs: `/vhosts/thetradevisor.com/storage/logs/horizon.log`
- Worker logs: `/vhosts/thetradevisor.com/storage/logs/worker.log`
- System logs: `/var/log/thetradevisor/`

## Performance Tuning

### Current Settings
- **CPU sampling**: 10 seconds (monitor_system_health.sh)
- **Memory per worker**: 256MB
- **Queue timeout**: 3600 seconds
- **Max concurrent locks**: 5
- **Disk I/O threshold**: 1500 ops/s

### Recommendations
1. Monitor disk space in `/vhosts/thetradevisor.com/storage/app/raw_data/`
2. Check queue backlog in Horizon dashboard
3. Review slow queries in PostgreSQL logs
4. Monitor PHP-FPM slow requests in `/var/log/php8.3-fpm-slow.log`

## Emergency Procedures

### If Queue Workers Stop
```bash
sudo supervisorctl restart horizon
sudo supervisorctl restart thetradevisor-worker:*
```

### If Database Locks Occur
```bash
/vhosts/thetradevisor.com/scripts/monitor_database_locks.sh
```

### If System Resources High
```bash
/vhosts/thetradevisor.com/scripts/refurbish.sh
```

## Scheduled Tasks (Cron)
To view all cron jobs:
```bash
sudo crontab -l
```

Current scheduled tasks:
- JSON cleanup: Sundays 3:00 AM
- (Add other cron jobs as configured)

## Security Notes
- All scripts run as appropriate users (www-data for web-related, ubuntu for system)
- Supervisor processes run as www-data
- Docker containers use non-root users where possible
- Logs are rotated to prevent disk fill
