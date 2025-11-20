# Admin Wiki - System Administration Guide

## Overview

The Admin Wiki is a comprehensive, centralized documentation system built directly into TheTradeVisor's admin panel. It provides administrators with instant access to all tools, scripts, commands, and system information needed to maintain and operate the platform.

## Access

**URL**: `/admin/wiki`  
**Route**: `admin.wiki`  
**Permission**: Admin users only  
**Navigation**: Admin dropdown menu → 📚 Admin Wiki

## Features

### 1. Quick Navigation
Jump directly to any section:
- ⚡ Artisan Commands
- 📜 Shell Scripts
- ⏰ Scheduled Tasks
- 🔧 Services

### 2. System Information
Real-time display of:
- **Environment**: App environment, debug mode, PHP version, Laravel version
- **Database**: Driver, database name, host
- **Cache**: Cache driver configuration
- **Queue**: Queue driver configuration
- **Paths**: Application, storage, logs, scripts directories

### 3. Artisan Commands

Comprehensive documentation of all custom artisan commands, organized by category:

#### Data Management
- `accounts:cleanup-inactive` - Delete inactive accounts after 180 days
- `accounts:detect-platforms` - Detect MT4/MT5 platform types

#### System Maintenance
- `geoip:update` - Update GeoIP database
- `cache:clear` - Clear application cache
- `config:clear` - Clear configuration cache
- `route:clear` - Clear route cache
- `view:clear` - Clear compiled views

#### Queue Management
- `horizon:terminate` - Stop Horizon gracefully
- `queue:work` - Process queue jobs
- `queue:failed` - List failed jobs
- `queue:retry` - Retry failed jobs

#### Database
- `migrate` - Run migrations
- `migrate:status` - Show migration status
- `db:show` - Display database info

#### Monitoring & Debugging
- `schedule:list` - List scheduled tasks
- `schedule:test` - Test scheduled tasks
- `route:list` - List all routes

Each command includes:
- ✅ Full description
- ✅ Usage syntax
- ✅ Available options
- ✅ Practical examples
- ✅ Schedule (if automated)
- ✅ Documentation links

### 4. Shell Scripts

Documentation for all maintenance and monitoring scripts:

#### Monitoring Scripts
- **monitor_system_health.sh** - Comprehensive system monitoring
  - CPU, memory, disk I/O monitoring
  - PostgreSQL long query detection
  - PHP-FPM slow request tracking
  - Auto-recovery under high load
  - Runs every 2 minutes via cron

- **send_alert.sh** - Alert notification system

#### Maintenance Scripts
- **cleanup_backups.sh** - Remove old backup files
- **extract_slow_queries.sh** - Analyze slow database queries

#### Development Tools
- **add_credits_to_docs.sh** - Add author credits to docs
- **fix_all_credits.sh** - Fix credits in all docs
- **create_github_issue.sh** - Create GitHub issues from CLI

Each script includes:
- ✅ Full path
- ✅ Description
- ✅ Usage examples
- ✅ Features list
- ✅ Log file locations
- ✅ Schedule (if automated)

### 5. Scheduled Tasks

Complete overview of all cron jobs:
- Task name and description
- Schedule frequency
- Command executed
- Next run time (via `php artisan schedule:list`)

Current scheduled tasks:
1. **Update Currency Rates** - Hourly
2. **Update GeoIP Database** - Every 14 days at 2:00 AM
3. **Cleanup Inactive Accounts** - Daily at 3:00 AM
4. **System Health Monitor** - Every 2 minutes

### 6. Middleware

Documentation of all custom middleware:
- **api.key** - API key validation
- **admin** - Admin access restriction
- **recaptcha** - reCAPTCHA verification
- **api.rate.limit** - API rate limiting (1000/hour)
- **rate.limit.analytics** - Analytics rate limiting (10/min)
- **circuit.breaker** - Auto-disable under high load

Each middleware shows:
- Alias name
- Class name
- Description
- Usage context

### 7. System Services

Complete service management information:

#### Nginx (Load Balancer)
- Port: 443 (HTTPS)
- Status: `sudo systemctl status nginx`
- Restart: `sudo systemctl restart nginx`
- Logs: `/var/log/nginx/error.log`

#### PHP 8.3-FPM
- 5 pools (www, pool1-4)
- Status: `sudo systemctl status php8.3-fpm`
- Restart: `sudo systemctl restart php8.3-fpm`
- Logs: `/var/log/php8.3-fpm.log`
- Slow log: `/var/log/php8.3-fpm-slow.log`

#### PostgreSQL 16
- Primary database
- Status: `sudo systemctl status postgresql@16-main`
- Restart: `sudo systemctl restart postgresql@16-main`
- Logs: `/var/log/postgresql/postgresql-16-main.log`

#### Laravel Horizon
- Queue worker supervisor
- URL: `/horizon`
- Terminate: `php artisan horizon:terminate`

### 8. Useful One-Liners

Quick reference for common administrative tasks:

```bash
# Check System Resources
top -bn1 | grep "Cpu(s)" && free -h && df -h

# View Recent Errors
tail -100 /www/storage/logs/laravel.log | grep ERROR

# Check Active Connections
sudo netstat -tuln | grep LISTEN

# PostgreSQL Active Queries
sudo -u postgres psql -d thetradevisor -c "SELECT pid, now() - query_start as duration, query FROM pg_stat_activity WHERE state = 'active';"

# Clear All Caches
php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear

# Monitor Real-Time Logs
tail -f /www/storage/logs/laravel.log
```

### 9. Documentation Links

Quick access to all documentation files:
- API Error Codes (`/www/docs/API_ERROR_CODES.md`)
- Inactive Accounts Cleanup (`/www/docs/INACTIVE_ACCOUNTS_CLEANUP.md`)
- 404 Page Features (`/www/docs/404_PAGE_FEATURES.md`)
- System Crash Postmortem (`/www/docs/SYSTEM_CRASH_POSTMORTEM.md`)

## Design Features

### User Experience
- **Smooth Scrolling** - Click navigation links for smooth scroll to sections
- **Hover Effects** - Interactive cards with hover states
- **Color Coding** - Different colors for different categories
- **Code Highlighting** - Terminal-style code blocks with syntax highlighting
- **Responsive Design** - Works on desktop, tablet, and mobile
- **Search-Friendly** - Organized sections with clear headings

### Visual Elements
- **Emoji Icons** - Quick visual identification (⚡, 📜, ⏰, 🔧)
- **Status Badges** - Schedule indicators with color coding
- **Code Blocks** - Dark terminal-style for commands
- **Bordered Cards** - Clean separation of content
- **Grid Layouts** - Efficient use of space

## Technical Implementation

### Controller
**Location**: `/www/app/Http/Controllers/Admin/AdminWikiController.php`

Methods:
- `index()` - Main wiki page
- `getArtisanCommands()` - Fetch command documentation
- `getScripts()` - Fetch script documentation
- `getSystemInfo()` - Gather system information
- `getScheduledTasks()` - List cron jobs
- `getMiddlewareInfo()` - Document middleware
- `getServicesInfo()` - Service management info

### View
**Location**: `/www/resources/views/admin/wiki/index.blade.php`

Features:
- Blade templating
- Responsive grid layouts
- TailwindCSS styling
- Alpine.js for interactivity
- Smooth scroll CSS

### Route
**Location**: `/www/routes/web.php`

```php
Route::get('/wiki', [App\Http\Controllers\Admin\AdminWikiController::class, 'index'])
    ->name('wiki');
```

### Navigation
**Location**: `/www/resources/views/layouts/navigation.blade.php`

Added to:
- Desktop admin dropdown
- Mobile admin menu

## Maintenance

### Adding New Commands
1. Add command to `getArtisanCommands()` method
2. Include description, usage, examples
3. Specify schedule if automated
4. Link to documentation if available

### Adding New Scripts
1. Add script to `getScripts()` method
2. Include path, description, usage
3. List features and log locations
4. Specify schedule if automated

### Adding New Services
1. Add service to `getServicesInfo()` method
2. Include status and restart commands
3. Specify log file locations
4. Add port or URL if applicable

### Updating System Info
System information is pulled dynamically from:
- `config('app.*')` - Application config
- `config('database.*')` - Database config
- `config('cache.*')` - Cache config
- `config('queue.*')` - Queue config
- `base_path()`, `storage_path()` - File paths

## Benefits

### For Administrators
- ✅ **Single Source of Truth** - All documentation in one place
- ✅ **Always Available** - No need to SSH or search files
- ✅ **Up-to-Date** - Dynamically generated information
- ✅ **Copy-Paste Ready** - All commands ready to use
- ✅ **Searchable** - Browser search works (Ctrl+F)

### For System Maintenance
- ✅ **Faster Troubleshooting** - Quick access to commands
- ✅ **Reduced Errors** - Correct syntax always available
- ✅ **Better Onboarding** - New admins can learn quickly
- ✅ **Consistent Operations** - Standardized procedures

### For Documentation
- ✅ **Centralized** - No scattered docs
- ✅ **Accessible** - Web-based, no file access needed
- ✅ **Organized** - Logical categorization
- ✅ **Comprehensive** - Everything documented

## Future Enhancements

Potential additions:
- [ ] Search functionality within wiki
- [ ] Command execution from wiki (with confirmation)
- [ ] Real-time service status indicators
- [ ] Log viewer integration
- [ ] Performance metrics dashboard
- [ ] Troubleshooting guides
- [ ] Video tutorials
- [ ] FAQ section
- [ ] Change log
- [ ] API documentation viewer

## Security

- ✅ **Admin-Only Access** - Protected by `admin` middleware
- ✅ **No Sensitive Data** - No passwords or keys displayed
- ✅ **Read-Only** - No command execution from wiki
- ✅ **Audit Trail** - Access logged via Laravel logs

## Performance

- ✅ **Fast Loading** - Static data, no heavy queries
- ✅ **Cached Config** - Uses Laravel config cache
- ✅ **Optimized Views** - Blade template compilation
- ✅ **Minimal JavaScript** - Mostly CSS-based

## Support

For questions or issues with the Admin Wiki:
- Check Laravel logs: `/www/storage/logs/laravel.log`
- Review route list: `php artisan route:list | grep wiki`
- Verify permissions: Ensure user has `is_admin = true`

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
