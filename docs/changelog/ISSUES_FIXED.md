# Issues Fixed - November 8, 2025

## Root Cause: Multiple EA Instances
**Problem:** Two EA instances running on different charts (GBPUSD.sd and BTCUSD.lv) in the same MT5 terminal, one with old API key.

**Solution:** User removed duplicate EA instance.

---

## Fixed Issues

### 1. ✅ Rate Limiting (429 Errors)
**Problem:** Rate limits too strict (60 req/min IP, 120 req/min API key)

**Fix:**
- Increased IP limit: 60 → 600 req/min (10 req/sec)
- Increased API key limit: 120 → 600 req/min (10 req/sec)  
- Increased burst limit: 200 → 1000 req/min

**File:** Database `rate_limit_settings` table

### 2. ✅ API Rate Limiter Middleware Bug
**Problem:** Rate limiter couldn't find authenticated user

**Fix:** Changed `$request->user()` to `$request->get('authenticated_user')`

**File:** `/www/app/Http/Middleware/ApiRateLimiter.php` line 25

### 3. ✅ Empty Analytics/Performance/Brokers Pages
**Problem:** Stale cache showing old empty data

**Fix:** Cleared all caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Remaining Issues

### ❌ Dashboard "N/A" Time Column
**Problem:** All deals have NULL `time` field in database

**Root Cause:** EA not sending `time` field in deal data, or ProcessTradingData job not storing it

**Status:** Needs EA code review to ensure `time` field is sent

**Database Status:**
- Deals with time: 0
- Deals without time: 568

**Temporary Workaround:** Use `time_msc` field or `created_at` timestamp

---

## Recommended EA Improvements

### 1. Duplicate Instance Detection
Add to EA to prevent multiple instances:

```mql5
// In OnInit()
string instance_id = Symbol() + "_" + IntegerToString(ChartID());
string magic_number = GenerateMagicNumber(instance_id);

// Send in API request
"meta": {
    "instance_id": instance_id,
    "magic_number": magic_number,
    "symbol": Symbol(),
    "chart_id": ChartID()
}
```

**Backend Detection:**
- Track `instance_id` + `user_id` + `ip` combinations
- Alert if multiple instances detected within 5 minutes
- Log warning if same symbol from same IP with different instance_id

### 2. Required Fields
Ensure EA sends these fields for deals:
- `time` (timestamp)
- `time_msc` (milliseconds)
- `symbol`
- `type`
- `volume`
- `price`
- `profit`

---

## Files Modified

1. `/www/app/Http/Middleware/ApiRateLimiter.php` - Fixed user retrieval
2. `/www/app/Http/Middleware/ValidateApiKey.php` - Added debug logging (temporary)
3. `/www/app/Console/Commands/DeleteUser.php` - Created user deletion command
4. Database `rate_limit_settings` - Increased limits

---

## Next Steps

1. **Review EA code** - Ensure `time` field is sent for all deals
2. **Remove debug logging** - Clean up temporary file logging in ValidateApiKey
3. **Implement duplicate detection** - Add instance tracking to prevent multiple EAs
4. **Test with fresh data** - Verify all fields are populated correctly


---

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)

For project support and inquiries:  
�� [hello@thetradevisor.com](mailto:hello@thetradevisor.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)
