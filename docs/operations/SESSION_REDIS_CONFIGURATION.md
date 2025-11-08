# 🔐 Session & Redis Configuration

> **Preventing Error 419 (Page Expired) when clearing cache**

**Date**: November 8, 2025  
**Status**: ✅ Implemented

---

## 🎯 Problem

When running `php artisan cache:clear` or `./refurbish.sh`, users were getting logged out because sessions and cache were stored in the same Redis database.

**Error 419 "Page Expired"** occurs when:
- Session expires (CSRF token becomes invalid)
- Cache is cleared (if sessions are in the same Redis DB)
- User leaves form open too long
- Multiple tabs with logout in one tab

---

## ✅ Solution

Separate Redis databases for cache and sessions:

```
Redis DB 0: Default (queues, etc.)
Redis DB 1: Cache (cleared frequently)
Redis DB 2: Sessions (persistent, not cleared with cache)
```

---

## 🔧 Configuration

### 1. Environment Variables

Add to your `.env` file:

```env
# Session Configuration
SESSION_DRIVER=redis
SESSION_LIFETIME=1440          # 24 hours
SESSION_CONNECTION=session     # Use separate Redis connection

# Redis Database Separation
REDIS_DB=0                     # Default Redis DB
REDIS_CACHE_DB=1              # Cache storage
REDIS_SESSION_DB=2            # Session storage (won't be cleared)
```

### 2. Database Configuration

In `config/database.php`, we have three Redis connections:

```php
'redis' => [
    'default' => [
        'database' => env('REDIS_DB', '0'),  // Queues, general use
    ],
    
    'cache' => [
        'database' => env('REDIS_CACHE_DB', '1'),  // Application cache
    ],
    
    'session' => [
        'database' => env('REDIS_SESSION_DB', '2'),  // User sessions
    ],
],
```

### 3. Session Configuration

In `config/session.php`:

```php
'driver' => env('SESSION_DRIVER', 'redis'),
'lifetime' => env('SESSION_LIFETIME', 1440),  // 24 hours
'connection' => env('SESSION_CONNECTION', 'session'),  // Use 'session' Redis connection
```

---

## 🧪 Testing

### Verify Configuration

```bash
# Check current session driver and lifetime
php artisan tinker --execute="
    echo 'Session Driver: ' . config('session.driver') . PHP_EOL;
    echo 'Session Lifetime: ' . config('session.lifetime') . ' minutes' . PHP_EOL;
    echo 'Session Connection: ' . config('session.connection') . PHP_EOL;
"
```

### Test Redis Databases

```bash
# Check sessions in DB 2
redis-cli -n 2 KEYS "*"

# Check cache in DB 1
redis-cli -n 1 KEYS "*"

# Clear cache (should NOT affect sessions)
php artisan cache:clear

# Verify sessions still exist
redis-cli -n 2 KEYS "*"
```

---

## 🚀 Benefits

### Before
- ❌ Clearing cache logged out all users
- ❌ Error 419 after running refurbish.sh
- ❌ Users lost work on long forms
- ❌ Poor user experience

### After
- ✅ Cache clearing doesn't affect sessions
- ✅ Users stay logged in during maintenance
- ✅ No more unexpected 419 errors
- ✅ Better user experience
- ✅ 24-hour session lifetime

---

## 📊 Session Lifetime Options

Choose based on your security requirements:

| Lifetime | Minutes | Use Case |
|----------|---------|----------|
| 2 hours | 120 | High security (banking) |
| 8 hours | 480 | Standard (work day) |
| 24 hours | 1440 | **Current** - User-friendly |
| 1 week | 10080 | Very relaxed |

---

## 🔍 Debugging Error 419

If users still get Error 419:

### 1. Check Redis Connection
```bash
redis-cli ping
# Should return: PONG
```

### 2. Check Session Storage
```bash
redis-cli -n 2 KEYS "laravel_session:*"
# Should show active sessions
```

### 3. Check Logs
```bash
tail -f storage/logs/laravel.log | grep "TokenMismatchException"
```

### 4. Common Causes
- Redis server down
- Redis memory full (check with `redis-cli INFO memory`)
- Session lifetime too short
- User's browser blocking cookies
- HTTPS/HTTP mismatch in session cookie settings

---

## 🛠️ Additional Improvements

### 1. Better Error Handling

Add to `app/Exceptions/Handler.php`:

```php
public function render($request, Throwable $exception)
{
    if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
        return redirect()->back()
            ->withInput($request->except('_token', 'password'))
            ->with('error', 'Your session has expired. Please try again.');
    }

    return parent::render($request, $exception);
}
```

### 2. CSRF Token Refresh (for long forms)

Add to forms that users might keep open:

```javascript
// Refresh CSRF token every 60 minutes
setInterval(function() {
    fetch('/refresh-csrf')
        .then(response => response.json())
        .then(data => {
            document.querySelectorAll('input[name="_token"]').forEach(input => {
                input.value = data.token;
            });
        });
}, 3600000); // 60 minutes
```

### 3. Session Monitoring

```bash
# Monitor active sessions
watch -n 5 'redis-cli -n 2 DBSIZE'

# Check session expiry
redis-cli -n 2 TTL "laravel_session:YOUR_SESSION_ID"
```

---

## 📝 Maintenance Commands

```bash
# Clear only cache (sessions safe)
php artisan cache:clear

# Clear only sessions (logout all users)
redis-cli -n 2 FLUSHDB

# Clear everything (use with caution)
redis-cli FLUSHALL

# Restart Redis
sudo systemctl restart redis
```

---

## 🔒 Security Considerations

1. **Session Lifetime**: Balance between UX and security
2. **HTTPS Only**: Set `SESSION_SECURE_COOKIE=true` in production
3. **HttpOnly Cookies**: Enabled by default (prevents XSS)
4. **SameSite**: Set to 'lax' or 'strict' for CSRF protection
5. **Session Encryption**: Consider enabling for sensitive data

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
**Version**: 1.0.0
