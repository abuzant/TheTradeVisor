# Circuit Breaker Implementation - Complete Guide

**Protecting TheTradeVisor from System Overload**

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [How It Works](#how-it-works)
3. [Implementation](#implementation)
4. [Configuration](#configuration)
5. [Protected Features](#protected-features)
6. [Monitoring](#monitoring)
7. [Testing](#testing)
8. [Troubleshooting](#troubleshooting)
9. [Credits](#credits)

---

## Overview

### What is a Circuit Breaker?

A circuit breaker is a design pattern that prevents system failure by automatically disabling expensive operations when the system is under stress. Like an electrical circuit breaker that prevents overload, our implementation protects the server from resource exhaustion.

### Why Do We Need It?

**Problem**: High-load operations (analytics, exports) can overwhelm the server during peak usage or attacks.

**Solution**: Automatically disable these operations when system resources are critical, allowing the system to recover.

### Benefits

✅ **Prevents Crashes** - System stays online during high load  
✅ **Automatic Recovery** - Re-enables features when load decreases  
✅ **User Experience** - Shows friendly message instead of timeout  
✅ **Resource Protection** - Protects CPU, memory, and database  
✅ **Fair Usage** - Ensures core features remain available  

---

## How It Works

### Circuit States

```
┌─────────────┐
│   CLOSED    │ ← Normal operation, all features available
│  (Normal)   │
└──────┬──────┘
       │ High load detected
       ↓
┌─────────────┐
│    OPEN     │ ← Circuit breaker activated, expensive features disabled
│  (Protected)│
└──────┬──────┘
       │ Load decreases + recovery time elapsed
       ↓
┌─────────────┐
│   CLOSED    │ ← Circuit closes, features re-enabled
│  (Recovered)│
└─────────────┘
```

### Trigger Conditions

Circuit breaker opens when **any** of these thresholds are exceeded:

| Metric | Threshold | Action |
|--------|-----------|--------|
| **CPU Usage** | > 80% | Open circuit |
| **Memory Usage** | > 85% | Open circuit |
| **Slow Queries** | > 5/min | Open circuit |

### Recovery

- **Recovery Time**: 5 minutes (300 seconds)
- **Auto-Close**: Circuit automatically closes after recovery time
- **Gradual**: Features re-enable one by one

---

## Implementation

### Architecture

```
User Request
     ↓
Rate Limiting Middleware (First line of defense)
     ↓
Circuit Breaker Middleware (Second line of defense)
     ↓
Controller (Only if circuit is closed)
```

### Components

**1. Circuit Breaker Service**
- `app/Services/CircuitBreakerService.php`
- Core logic for opening/closing circuit
- Monitors system metrics

**2. Circuit Breaker Middleware**
- `app/Http/Middleware/CircuitBreakerMiddleware.php`
- Intercepts requests to protected routes
- Returns 503 when circuit is open

**3. Error Page**
- `resources/views/errors/circuit-breaker.blade.php`
- User-friendly explanation
- Shows system status
- Auto-refreshes after recovery time

**4. Configuration**
- `config/database_limits.php`
- Thresholds and settings
- Feature toggles

---

## Configuration

### File: `config/database_limits.php`

```php
'circuit_breaker' => [
    // Enable/disable circuit breaker
    'enabled' => env('CIRCUIT_BREAKER_ENABLED', true),
    
    // Thresholds for opening circuit
    'cpu_threshold' => 80,              // CPU usage percentage
    'memory_threshold' => 85,           // Memory usage percentage
    'slow_query_threshold' => 5,        // Slow queries per minute
    
    // Features to disable when circuit is open
    'disable_analytics' => true,        // Disable analytics pages
    'disable_exports' => true,          // Disable exports
    'serve_cached_only' => true,        // Serve only cached data
    
    // Recovery settings
    'recovery_time' => 300,             // Seconds before auto-close (5 min)
],
```

### Environment Variables

```env
# Enable/disable circuit breaker
CIRCUIT_BREAKER_ENABLED=true

# Optional: Override thresholds
DB_QUERY_TIMEOUT=30
ANALYTICS_CACHE_DURATION=300
MAX_ANALYTICS_REQUESTS=5
```

---

## Protected Features

### 1. Analytics (High Priority)

**Routes**:
- `/analytics/{days?}` - Global analytics
- `/analytics/countries` - Country analytics
- `/broker-analytics` - Broker comparison
- `/broker/{broker}` - Broker details

**Why**: 66+ database queries per page, very expensive

**When Disabled**: Shows circuit breaker page

### 2. Exports (High Priority)

**Routes**:
- `/export/trades/csv`
- `/export/trades/pdf`
- `/export/symbol/{symbol}/csv`
- `/export/dashboard/csv`
- `/export/account-data`

**Why**: Loads up to 10,000 records, generates large files

**When Disabled**: Returns 503 error

### 3. Not Protected (Always Available)

- Dashboard (cached, fast)
- Trades list (paginated)
- Profile pages
- Settings
- Login/Register

**Why**: Core features must remain available

---

## Monitoring

### Check Circuit Status

```bash
# Check if circuit is open
redis-cli GET "circuit_breaker_state"
# Returns: "1" (open) or empty (closed)

# Check current metrics
redis-cli GET "circuit_breaker_metrics"
# Returns: JSON with CPU, memory, slow queries
```

### View Circuit Breaker Logs

```bash
# Check for circuit breaker events
grep "Circuit breaker" /www/storage/logs/laravel.log

# View recent openings
grep "Circuit breaker opened" /www/storage/logs/laravel.log | tail -10

# View recent closings
grep "Circuit breaker closed" /www/storage/logs/laravel.log | tail -10
```

### Monitor System Metrics

```bash
# View health monitor log
tail -f /var/log/thetradevisor/health_monitor.log

# Check current CPU/memory
top -bn1 | grep "Cpu(s)"
free -m | grep "Mem:"
```

---

## Testing

### Test Circuit Breaker Manually

**1. Open Circuit Manually**:
```bash
cd /www && php artisan tinker
>>> app(\App\Services\CircuitBreakerService::class)->open('Manual test');
>>> exit
```

**2. Try Accessing Protected Route**:
```bash
curl https://thetradevisor.com/analytics/30
# Should return 503 with circuit breaker page
```

**3. Check Status**:
```bash
redis-cli GET "circuit_breaker_state"
# Returns: "1"
```

**4. Close Circuit Manually**:
```bash
cd /www && php artisan tinker
>>> app(\App\Services\CircuitBreakerService::class)->close();
>>> exit
```

### Simulate High Load

**Stress Test CPU**:
```bash
# WARNING: Only on test environment!
stress --cpu 4 --timeout 60s
```

**Monitor Circuit**:
```bash
# Watch for circuit breaker to open
watch -n 1 'redis-cli GET "circuit_breaker_state"'
```

---

## Troubleshooting

### Circuit Won't Open

**Check if enabled**:
```bash
grep CIRCUIT_BREAKER_ENABLED /www/.env
# Should be: CIRCUIT_BREAKER_ENABLED=true
```

**Check thresholds**:
```bash
# View current config
cd /www && php artisan tinker
>>> config('database_limits.circuit_breaker')
```

**Check monitoring**:
```bash
# Ensure health monitor is running
crontab -l | grep monitor_system_health
```

### Circuit Won't Close

**Check recovery time**:
```bash
# View TTL (time to live)
redis-cli TTL "circuit_breaker_state"
# Returns seconds until auto-close
```

**Force close**:
```bash
redis-cli DEL "circuit_breaker_state"
```

### Users See Circuit Breaker Page

**This is normal!** It means:
1. System is under high load
2. Circuit breaker is protecting the system
3. Wait 5 minutes for automatic recovery

**Check metrics**:
```bash
redis-cli GET "circuit_breaker_metrics"
```

---

## Best Practices

### DO ✅

1. **Monitor regularly**
   - Check logs weekly
   - Track circuit breaker events
   - Identify patterns

2. **Adjust thresholds**
   - Based on your server capacity
   - After hardware upgrades
   - During traffic analysis

3. **Test periodically**
   - Verify circuit breaker works
   - Test error pages
   - Check auto-recovery

4. **Communicate with users**
   - Show clear error messages
   - Provide retry time
   - Explain what happened

### DON'T ❌

1. **Don't disable circuit breaker**
   - It protects your system
   - Prevents crashes
   - Essential for stability

2. **Don't set thresholds too low**
   - Circuit will open too often
   - Users frustrated
   - Features unavailable

3. **Don't ignore circuit breaker events**
   - Indicates real problems
   - Needs investigation
   - May require optimization

---

## Performance Impact

### Before Circuit Breaker

**Scenario**: System under high load
```
CPU: 95% → System slow for everyone
Memory: 92% → Risk of crash
Response Time: 10-30 seconds
Result: System hangs, users frustrated
```

### After Circuit Breaker

**Scenario**: System under high load
```
CPU: 85% → Circuit opens
Memory: 88% → Expensive features disabled
Response Time: 50ms (error page)
Result: System stable, core features available
```

**Improvement**: System stays online, users can access core features

---

## Integration with Other Protections

### Defense in Depth

```
Layer 1: Rate Limiting (10-20 req/min)
    ↓ (If user exceeds limit)
Layer 2: Circuit Breaker (System load > 80%)
    ↓ (If system overloaded)
Layer 3: Query Limits (Max 10,000 records)
    ↓ (If query too large)
Layer 4: Query Timeout (30 seconds)
    ↓ (If query too slow)
Layer 5: Monitoring & Alerts
```

**Result**: Multiple layers of protection ensure system stability

---

## Credits

### Implementation

**Developed by**: Cascade AI Assistant  
**Date**: November 12, 2025  
**Project**: TheTradeVisor System Hardening  
**Version**: 1.0  

### Based On

- **Circuit Breaker Pattern**: Martin Fowler's design pattern
- **Netflix Hystrix**: Inspiration for implementation
- **Laravel Best Practices**: Framework conventions
- **Production Experience**: Real-world incident response

### Contributors

- **System Architecture**: Cascade AI
- **Monitoring Integration**: Cascade AI
- **Error Page Design**: Cascade AI with Tailwind CSS
- **Documentation**: Cascade AI
- **Testing**: TheTradeVisor Team

### References

- [Martin Fowler - Circuit Breaker](https://martinfowler.com/bliki/CircuitBreaker.html)
- [Netflix Hystrix](https://github.com/Netflix/Hystrix)
- [Laravel Documentation](https://laravel.com/docs)
- [Resilience Patterns](https://docs.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker)

---

## Summary

**Circuit breaker is fully implemented and protecting your system!**

✅ **Service**: CircuitBreakerService  
✅ **Middleware**: CircuitBreakerMiddleware  
✅ **Error Page**: User-friendly circuit breaker page  
✅ **Configuration**: Flexible thresholds  
✅ **Monitoring**: Integrated with health checks  
✅ **Protected**: Analytics, exports, broker analytics  
✅ **Recovery**: Automatic after 5 minutes  

**Protection Level**: Maximum 🛡️

**Thresholds**:
- CPU: > 80%
- Memory: > 85%
- Slow Queries: > 5/min

**Recovery Time**: 5 minutes

**This completes the "Circuit breakers for high-load operations" recommendation!** 🚀

---

## Quick Reference

```bash
# Check circuit status
redis-cli GET "circuit_breaker_state"

# View metrics
redis-cli GET "circuit_breaker_metrics"

# Open circuit manually
cd /www && php artisan tinker
>>> app(\App\Services\CircuitBreakerService::class)->open('Test');

# Close circuit manually
redis-cli DEL "circuit_breaker_state"

# View logs
grep "Circuit breaker" /www/storage/logs/laravel.log

# Monitor health
tail -f /var/log/thetradevisor/health_monitor.log
```

---

**Documentation Version**: 1.0  
**Last Updated**: November 12, 2025  
**Status**: ✅ Production Ready  
**Maintained by**: TheTradeVisor Team

---

*This implementation protects TheTradeVisor from system overload while maintaining availability of core features. The circuit breaker pattern ensures graceful degradation under stress, preventing complete system failure.*

**Built with ❤️ by Cascade AI for TheTradeVisor**
