# 🎯 Custom Artisan Commands - TheTradeVisor

> **Complete guide to custom artisan commands for maintenance, debugging, and data management**

---

## 📚 Table of Contents

1. [Symbol Management](#symbol-management)
2. [Data Maintenance](#data-maintenance)
3. [Cache Management](#cache-management)
4. [Quick Reference](#quick-reference)

---

## 🔤 Symbol Management

### `php artisan symbols:sync`

**Purpose:** Automatically detect and import all unique symbols from the database into the symbol mappings table.

**What It Does:**
- Scans `deals`, `positions`, and `orders` tables
- Finds all unique trading symbols
- Creates symbol mappings with auto-normalized names
- Marks new symbols as "unverified" for manual review
- Skips symbols that already exist

**When to Use:**
- ✅ After importing historical data
- ✅ When you notice missing symbols in Symbol Management admin
- ✅ After connecting new trading accounts with different brokers
- ✅ Periodically (weekly/monthly) to catch new symbols

**Example Output:**
```bash
$ php artisan symbols:sync

Scanning database for unique symbols...
Found 36 unique symbols
✓ Created: XAUUSD.sd → XAUUSD
✓ Created: EURUSD.sd → EURUSD
✓ Created: BTCUSD.lv → BTCUSD
...
Sync complete!
Created: 25
Skipped: 11
Total in mapping table: 36
```

**Use Cases:**
```bash
# After importing new trading data
php artisan symbols:sync

# Check what was added
php artisan tinker
>>> \App\Models\SymbolMapping::where('is_verified', false)->count()
```

---

## 🔧 Data Maintenance

### `php artisan deals:fix-times`

**Purpose:** Fix NULL time values in deals by converting from `time_msc` (milliseconds since epoch).

**What It Does:**
- Finds all deals with NULL `time` but valid `time_msc`
- Converts milliseconds to proper datetime format
- Updates database with correct timestamps
- Shows progress bar during processing
- Reports success/failure statistics

**When to Use:**
- ✅ After discovering deals with missing timestamps
- ✅ After manual data imports
- ✅ When dashboard shows "N/A" for trade times
- ✅ One-time fix for historical data issues

**Example Output:**
```bash
$ php artisan deals:fix-times

Finding deals with NULL time but valid time_msc...
Found 532 deals to fix
 532/532 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

✓ Fixed: 532 deals
Remaining deals with NULL time: 0
```

**Technical Details:**
- Converts: `time_msc / 1000` → Unix timestamp
- Uses: `Carbon::createFromTimestamp()`
- Safe to run multiple times (idempotent)
- Only processes deals with valid `time_msc > 0`

**Verification:**
```bash
# Check if any deals still have NULL time
php artisan tinker
>>> \App\Models\Deal::whereNull('time')->count()
0  # Should be 0 after running command
```

---

## 🗑️ Cache Management

### `./refurbish.sh`

**Purpose:** Complete cache refresh - clears all application, infrastructure, and system caches.

**What It Does:**
1. **Laravel Caches:**
   - Application cache
   - Configuration cache
   - Route cache
   - View cache
   - Event cache

2. **Infrastructure Caches:**
   - Redis cache (FLUSHDB)
   - Nginx FastCGI cache

3. **Services:**
   - Restarts PHP-FPM
   - Reloads Nginx
   - Restarts Horizon

4. **Rebuilds:**
   - Optimized config cache
   - Optimized route cache
   - Pre-compiled views
   - Event cache

**When to Use:**
- ✅ After code changes (models, controllers, routes)
- ✅ When seeing stale data
- ✅ After configuration changes
- ✅ When debugging cache-related issues
- ✅ After deployment
- ✅ When "something just isn't working"

**Usage:**
```bash
cd /var/www/thetradevisor.com
./refurbish.sh
```

**Example Output:**
```bash
🧹 TheTradeVisor Cache Refurbish Script
========================================

📦 Clearing Laravel caches...
✓ Laravel caches cleared

🔴 Flushing Redis cache...
✓ Redis cache flushed

🌐 Clearing Nginx FastCGI cache...
✓ Nginx cache cleared

🐘 Restarting PHP-FPM...
✓ PHP-FPM restarted

🌐 Reloading Nginx...
✓ Nginx reloaded

⚡ Rebuilding optimized caches...
✓ Optimized caches rebuilt

🔄 Restarting Horizon...
✓ Horizon restarted

✅ Refurbish complete!
```

**What Gets Cleared:**
| Cache Type | Location | Impact |
|------------|----------|--------|
| Laravel App | Redis | All cached data (dashboard, metrics, etc.) |
| Config | `/bootstrap/cache/config.php` | Configuration values |
| Routes | `/bootstrap/cache/routes-v7.php` | Route definitions |
| Views | `/storage/framework/views/` | Compiled Blade templates |
| Nginx | `/var/cache/nginx/fastcgi/` | Page cache |

**Permissions:**
- Script uses `sudo` for system operations
- Requires password for: Nginx, PHP-FPM, Supervisor
- Safe to run as `tradeadmin` user

---

## 📋 Quick Reference

### Command Cheat Sheet

```bash
# Symbol Management
php artisan symbols:sync              # Sync all symbols from database

# Data Maintenance
php artisan deals:fix-times           # Fix NULL timestamps in deals

# Cache Management
./refurbish.sh                        # Complete cache refresh

# Laravel Built-in (useful)
php artisan cache:clear               # Clear application cache only
php artisan config:clear              # Clear config cache only
php artisan route:clear               # Clear route cache only
php artisan view:clear                # Clear compiled views only
php artisan horizon:terminate         # Gracefully terminate Horizon
php artisan queue:work                # Start queue worker manually
php artisan queue:failed              # List failed jobs
php artisan queue:retry all           # Retry all failed jobs
```

---

## 🔄 Workflow Examples

### After Importing Historical Data
```bash
# 1. Sync symbols
php artisan symbols:sync

# 2. Fix any time issues
php artisan deals:fix-times

# 3. Clear caches
./refurbish.sh

# 4. Verify in browser
# Visit: /admin/symbols
# Visit: /dashboard
```

### After Code Changes
```bash
# 1. Pull latest code
git pull origin main

# 2. Update dependencies (if needed)
composer install --no-dev
npm install && npm run build

# 3. Run migrations (if any)
php artisan migrate --force

# 4. Clear all caches
./refurbish.sh
```

### Debugging Cache Issues
```bash
# 1. Clear everything
./refurbish.sh

# 2. Check Horizon status
php artisan horizon:status

# 3. Check queue
php artisan queue:work --once

# 4. Check logs
tail -f storage/logs/laravel.log
```

### Weekly Maintenance
```bash
# 1. Sync new symbols
php artisan symbols:sync

# 2. Clear old caches
./refurbish.sh

# 3. Prune Telescope (if enabled)
php artisan telescope:prune --hours=168  # Keep 1 week

# 4. Check failed jobs
php artisan queue:failed
```

---

## 🛠️ Troubleshooting

### Command Not Found
```bash
# Make sure you're in the project directory
cd /var/www/thetradevisor.com

# Check if command exists
php artisan list | grep symbols
php artisan list | grep deals
```

### Permission Denied (refurbish.sh)
```bash
# Make script executable
chmod +x refurbish.sh

# Run with sudo if needed
sudo ./refurbish.sh
```

### Horizon Not Restarting
```bash
# Check supervisor status
sudo supervisorctl status horizon

# Manually restart
sudo supervisorctl restart horizon

# Check logs
tail -f storage/logs/horizon.log
```

### Redis Connection Error
```bash
# Check Redis status
redis-cli ping
# Should return: PONG

# Restart Redis if needed
sudo systemctl restart redis

# Check connection in Laravel
php artisan tinker
>>> \Cache::get('test')
```

---

## 📊 Command Comparison

| Command | Speed | Scope | When to Use |
|---------|-------|-------|-------------|
| `symbols:sync` | Fast | Symbols only | After new data import |
| `deals:fix-times` | Medium | Deals only | One-time fix for timestamps |
| `refurbish.sh` | Slow | Everything | After code changes, deployments |
| `cache:clear` | Fast | App cache | Quick cache clear |
| `config:clear` | Fast | Config only | After .env changes |

---

## 🎯 Best Practices

### DO ✅
- Run `refurbish.sh` after major changes
- Run `symbols:sync` after importing data
- Run `deals:fix-times` once to fix historical data
- Check command output for errors
- Verify results in admin panel

### DON'T ❌
- Don't run `deals:fix-times` repeatedly (unnecessary)
- Don't clear cache during high traffic
- Don't skip cache rebuild after clearing
- Don't ignore error messages
- Don't run multiple cache clears simultaneously

---

## 📝 Adding New Commands

### Create New Command
```bash
php artisan make:command YourCommandName
```

### Command Template
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class YourCommandName extends Command
{
    protected $signature = 'your:command';
    protected $description = 'Description of what it does';

    public function handle()
    {
        $this->info('Starting...');
        
        // Your logic here
        
        $this->info('✓ Complete!');
        return 0;
    }
}
```

### Register in Kernel (if needed)
```php
// app/Console/Kernel.php
protected $commands = [
    Commands\YourCommandName::class,
];
```

---

## 🔗 Related Documentation

- [Laravel Artisan Console](https://laravel.com/docs/artisan)
- [Laravel Horizon](https://laravel.com/docs/horizon)
- [Supervisor Configuration](http://supervisord.org/)
- [Redis Commands](https://redis.io/commands)

---

## 📞 Support

### Getting Help
```bash
# Show all available commands
php artisan list

# Get help for specific command
php artisan help symbols:sync
php artisan help deals:fix-times

# Check Laravel version
php artisan --version
```

### Logs to Check
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Horizon logs
tail -f storage/logs/horizon.log

# Nginx error logs
sudo tail -f /var/log/nginx/thetradevisor-error.log

# PHP-FPM logs
sudo tail -f /var/log/php8.3-fpm.log
```

---

**Last Updated:** November 8, 2025  
**Maintained By:** Ruslan Abuzant  
**Version:** 1.0
