# Admin Wiki - Quick Start Guide

## What Is It?

A comprehensive, web-based documentation system built into TheTradeVisor's admin panel. It provides instant access to all tools, scripts, commands, and system information needed for system administration.

## Access

**URL**: `https://thetradevisor.com/admin/wiki`  
**Navigation**: Admin Menu → 📚 Admin Wiki  
**Permission**: Admin users only

## What's Included

### ⚡ Artisan Commands (20+ commands)
- Data management (cleanup, platform detection)
- System maintenance (cache, config, routes)
- Queue management (Horizon, workers, failed jobs)
- Database operations (migrations, status)
- Monitoring & debugging (schedules, routes)

### 📜 Shell Scripts (8 scripts)
- System monitoring (`monitor_system_health.sh`)
- Alert notifications (`send_alert.sh`)
- Backup cleanup (`cleanup_backups.sh`)
- Query analysis (`extract_slow_queries.sh`)
- Development tools (credits, GitHub issues)

### ⏰ Scheduled Tasks (4 tasks)
- Currency rate updates (hourly)
- GeoIP database updates (every 14 days)
- Inactive account cleanup (daily at 3 AM)
- System health monitoring (every 2 minutes)

### 🔧 System Services (4 services)
- Nginx (load balancer)
- PHP 8.3-FPM (5 pools)
- PostgreSQL 16 (database)
- Laravel Horizon (queue supervisor)

### 🛡️ Middleware (6 middleware)
- API key validation
- Admin access control
- reCAPTCHA verification
- Rate limiting (API, analytics)
- Circuit breaker

### 💡 Useful One-Liners (6+ commands)
- System resource checks
- Error log viewing
- Active connections
- Database queries
- Cache clearing
- Real-time log monitoring

### 📚 Documentation Links
- API Error Codes
- Inactive Accounts Cleanup
- 404 Page Features
- System Crash Postmortem

## Key Features

✅ **Copy-Paste Ready** - All commands formatted for immediate use  
✅ **Comprehensive Examples** - Real-world usage examples  
✅ **Schedule Information** - Know when automated tasks run  
✅ **Log Locations** - Quick access to log files  
✅ **Service Management** - Status and restart commands  
✅ **System Information** - Real-time configuration display  
✅ **Smooth Navigation** - Jump to any section instantly  
✅ **Mobile Responsive** - Works on all devices  

## Quick Navigation

Click these sections in the wiki:
- **⚡ Artisan Commands** - Laravel CLI tools
- **📜 Shell Scripts** - Bash automation scripts
- **⏰ Scheduled Tasks** - Cron job overview
- **🔧 Services** - System service management

## Common Use Cases

### 1. Clear All Caches
```bash
php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear
```

### 2. Check System Health
```bash
/www/scripts/monitor_system_health.sh
```

### 3. View Scheduled Tasks
```bash
php artisan schedule:list
```

### 4. Monitor Logs
```bash
tail -f /www/storage/logs/laravel.log
```

### 5. Cleanup Inactive Accounts (Preview)
```bash
php artisan accounts:cleanup-inactive --dry-run
```

### 6. Restart Services
```bash
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm
```

## Benefits

### For Daily Operations
- **Faster** - No need to search files or remember commands
- **Accurate** - Always up-to-date syntax and options
- **Efficient** - Everything in one place

### For Troubleshooting
- **Quick Reference** - Instant access to diagnostic commands
- **Log Locations** - Know where to look for errors
- **Service Commands** - Restart services immediately

### For New Admins
- **Learning Tool** - Understand system architecture
- **Best Practices** - See recommended commands
- **Documentation** - Comprehensive guides

## Files Created

- **Controller**: `/www/app/Http/Controllers/Admin/AdminWikiController.php`
- **View**: `/www/resources/views/admin/wiki/index.blade.php`
- **Route**: Added to `/www/routes/web.php`
- **Navigation**: Updated in `/www/resources/views/layouts/navigation.blade.php`
- **Docs**: `/www/docs/ADMIN_WIKI.md`

## Technical Details

- **Framework**: Laravel 11
- **Styling**: TailwindCSS
- **Icons**: Emoji + SVG
- **Data**: Dynamically generated from config
- **Security**: Admin middleware protected
- **Performance**: Static data, no heavy queries

## Support

**Full Documentation**: `/www/docs/ADMIN_WIKI.md`  
**Route Check**: `php artisan route:list | grep wiki`  
**Access Issues**: Verify `is_admin = true` in users table

---

**Status**: ✅ Deployed and Active  
**Last Updated**: November 13, 2025  
**Access**: Admin users only via `/admin/wiki`
