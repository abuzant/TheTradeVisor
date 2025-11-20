# Laravel Logging - Single File Configuration

## Issue Fixed

Laravel was creating dated log files (laravel-2025-11-12.log) instead of writing to a single laravel.log file, making the admin log viewer show an empty file.

---

## Solution

Changed `LOG_CHANNEL` from `daily` to `single` in `.env`:

```env
# Before
LOG_CHANNEL=daily

# After
LOG_CHANNEL=single
```

---

## What This Does

### Before (daily)
```
storage/logs/
├── laravel.log (empty)
├── laravel-2025-11-08.log
├── laravel-2025-11-09.log
├── laravel-2025-11-11.log
└── laravel-2025-11-12.log (current, has logs)
```

**Problem**: Admin panel shows empty laravel.log

### After (single)
```
storage/logs/
├── laravel.log (all logs here!)
├── laravel.log.1 (rotated)
├── laravel.log.2.gz (compressed)
└── laravel.log.3.gz (compressed)
```

**Solution**: Admin panel shows current logs in laravel.log

---

## Benefits

✅ **Single file** - All logs in one place  
✅ **Admin panel works** - Always shows current logs  
✅ **Easy monitoring** - One file to watch  
✅ **Still rotates** - System logrotate handles rotation  
✅ **No dated files** - Cleaner log directory  

---

## Log Rotation

Even with `single` driver, logs are still rotated by system logrotate:

```
laravel.log          → Current logs (today)
laravel.log.1        → Yesterday's logs
laravel.log.2.gz     → 2 days ago (compressed)
laravel.log.3.gz     → 3 days ago (compressed)
...
```

**Retention**: 14 days (configured in logrotate)

---

## Permissions Fixed

```bash
chmod 666 /www/storage/logs/laravel.log
chown www-data:www-data /www/storage/logs/laravel.log
```

This ensures both CLI and web server can write to the log file.

---

## Configuration

### In .env
```env
LOG_CHANNEL=single
LOG_LEVEL=error
```

### In config/logging.php
```php
'single' => [
    'driver' => 'single',
    'path' => storage_path('logs/laravel.log'),
    'level' => env('LOG_LEVEL', 'debug'),
    'replace_placeholders' => true,
    'tap' => [App\Logging\RemoveStackTraceFormatter::class],
],
```

---

## Verification

### Check logs are writing
```bash
# Write test log
cd /www && php artisan tinker --execute="Log::error('Test'); echo 'Done';"

# View log
tail /www/storage/logs/laravel.log
```

### Check in admin panel
1. Go to Admin Panel
2. Click Logs
3. Select "Laravel" from dropdown
4. Should see current logs (not empty!)

---

## Other Log Channels

If you ever need dated files again:

```env
# Daily rotation with dated files
LOG_CHANNEL=daily

# Stack multiple channels
LOG_CHANNEL=stack
LOG_STACK=single,slack
```

But for admin panel viewing, `single` is best.

---

## Summary

✅ **Changed**: LOG_CHANNEL=single in .env  
✅ **Fixed**: Permissions on laravel.log  
✅ **Result**: Admin panel shows current logs  
✅ **Rotation**: Still handled by logrotate  

**Admin panel now works correctly!**


---

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
