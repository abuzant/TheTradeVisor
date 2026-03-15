# Server Migration and Fixes Summary

## Date: March 15, 2026

## Overview
This document summarizes all changes made during the server migration and optimization process.

## Major Changes

### 1. JSON Cleanup System
- **New Script**: `scripts/cleanup_old_json_files.sh`
- **Purpose**: Automatically removes JSON files older than 7 days from `storage/app/raw_data/`
- **Features**:
  - Dry-run mode for testing
  - Parses dates from filenames (YYYY-MM-DD format)
  - Comprehensive logging
  - Configurable retention period
- **Automation**: Added to cron (Sundays at 3:00 AM)
- **Results**: Freed ~3GB of storage space by removing 111,928 old files

### 2. Supervisor Configuration
- **Fixed**: `/etc/supervisor/conf.d/horizon.conf`
- **Fixed**: `/etc/supervisor/conf.d/thetradevisor-worker.conf`
- **Changes**:
  - Corrected user from `tradeadmin` to `www-data`
  - Updated queue workers to process both `default` and `historical` queues
  - Added memory limits (256MB per worker)
  - Increased worker count to 3
  - Added log rotation settings

### 3. Monitoring Scripts Updates

#### monitor_database_locks.sh
- Updated to use Docker PostgreSQL container
- Added database specification (`-d thetradevisor_app`)
- Added validation for query results to prevent errors
- Fixed paths from `/www` to `/vhosts/thetradevisor.com`

#### monitor_system_health.sh
- Added terminal detection (shows output when run manually)
- Reduced CPU sampling time from 120 to 10 seconds
- Updated PostgreSQL commands for Docker
- Fixed send_alert script path

#### healthcheck.sh
- Updated to check Docker containers instead of system services
- Added container status checks for PostgreSQL, Redis, and Telegram Bot
- Added database connection testing
- Added PHP-FPM pool status display

### 4. Script Removals
The following obsolete scripts were deleted:
- `apply_query_fixes.php`
- `create-github-release.sh`
- `create_github_issue.sh`
- `monitor_windsurf_resources.sh`

### 5. Script Modifications
- `check_cpu_credits.sh`: Modified to always return 9999 (disabled)
- `refurbish.sh`: Updated for current server architecture

## Infrastructure Changes

### Docker Services
- PostgreSQL: Running in Docker container
- Redis: Running in Docker container
- Telegram Bot: Running in Docker container

### PHP-FPM Configuration
- 3 active pools: thetradevisor, www, sarcastic
- Optimized for current workload

### Queue System
- Laravel Horizon for queue monitoring
- 3 dedicated queue workers
- Auto-scaling configuration (1-3 workers)
- Memory limit: 256MB per worker

## Automation

### Cron Jobs
- JSON cleanup: Sundays at 3:00 AM
- System health monitoring: Every 2 minutes (recommended)

### Supervisor
- All queue processes auto-restart on failure
- Starts automatically on boot

## Performance Improvements

### Storage Optimization
- Reduced storage usage from 5.9GB to 2.9GB
- Implemented automated cleanup of old data

### Memory Management
- PHP-FPM workers limited to 256MB
- Queue workers properly constrained

### Monitoring Enhancements
- Real-time health checks
- Database lock monitoring
- Automated alert system

## Security Improvements
- All processes run as appropriate users
- Supervisor processes run as www-data
- Proper file permissions maintained

## Documentation
- Created server maintenance guide
- Updated deployment documentation
- Added troubleshooting procedures

## Next Steps
1. Set up automated backups
2. Configure monitoring dashboards
3. Implement log aggregation
4. Set up automated security updates

## Commands for Reference

### Health Check
```bash
/vhosts/thetradevisor.com/scripts/healthcheck.sh
```

### System Refresh
```bash
/vhosts/thetradevisor.com/scripts/refurbish.sh
```

### Manual JSON Cleanup
```bash
# Dry run
sudo /vhosts/thetradevisor.com/scripts/cleanup_old_json_files.sh true

# Actual cleanup
sudo /vhosts/thetradevisor.com/scripts/cleanup_old_json_files.sh false
```

### Supervisor Management
```bash
sudo supervisorctl status
sudo supervisorctl restart all
```

### Docker Container Management
```bash
docker ps
docker restart postgres
docker restart redis
```
