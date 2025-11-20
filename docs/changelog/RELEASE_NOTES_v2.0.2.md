# Release Notes - Version 2.0.2

**Release Date:** November 19, 2025  
**Type:** Bug Fix  
**Priority:** Medium

---

## 🐛 Bug Fixes

### Dashboard - Account Profit Display Issue

**Issue:**
- MT4 accounts were showing 0.00 profit in the "Your Trading Accounts" table on the dashboard, even when trades had been executed during the day
- The stored `profit` field in the database represents floating P/L from open positions only
- When all positions are closed, this field becomes 0.00, making it appear as if no trading activity occurred

**Root Cause:**
- The dashboard was displaying the `profit` field directly from the `trading_accounts` table
- This field is synced from MT4/MT5 and represents **current floating profit/loss** from open positions only
- When there are no open positions, this value is 0.00, regardless of closed trades

**Solution:**
- Changed profit calculation to dynamically sum profit from open positions: `$account->openPositions->sum('profit')`
- Updated both the accounts table display and the total profit calculation in overview cards
- This ensures accurate representation of current floating P/L from open positions

**Files Modified:**
1. `/www/resources/views/dashboard.blade.php` (line 209-210)
   - Changed from `$account->profit` to `$account->openPositions->sum('profit')`
   
2. `/www/app/Http/Controllers/DashboardController.php` (line 86-93)
   - Updated total profit calculation to use open positions sum instead of stored field

**Impact:**
- ✅ Profit column now correctly shows 0.00 when there are no open positions (accurate)
- ✅ Profit column shows actual floating P/L when positions are open
- ✅ No longer misleading when trades have been closed during the day
- ✅ Consistent with account detail page behavior

**Note:**
- The "Profit" column in the accounts table represents **current floating P/L from open positions**
- For historical profit/loss from closed trades, users should view the account detail page or closed positions section
- This is the correct MT4/MT5 behavior and matches the platform's definition of "profit"

---

## 📊 Technical Details

### Before:
```php
// Displayed stored profit field (could be stale/0 when no open positions)
{{ $account->profit }}
```

### After:
```php
// Dynamically calculates from current open positions
{{ $account->openPositions->sum('profit') }}
```

### Cache Cleared:
- Application cache cleared to ensure changes take effect immediately
- User-specific dashboard caches will refresh on next page load (5-minute TTL)

---

## 🔍 Testing Performed

**Test Scenario:**
1. MT4 account with closed trades but no open positions
2. Expected: Profit shows 0.00 (no floating P/L)
3. Result: ✅ Correctly displays 0.00

**Test Scenario:**
1. MT4 account with open positions showing profit/loss
2. Expected: Profit shows actual floating P/L
3. Result: ✅ Correctly displays floating P/L

**Test Scenario:**
1. MT5 account with multiple open positions
2. Expected: Profit shows sum of all floating P/L
3. Result: ✅ Correctly displays total floating P/L

---

## 📝 Deployment Notes

**Deployment Steps:**
1. Pull latest changes from repository
2. No database migrations required
3. Clear application cache: `php artisan cache:clear`
4. No service restart required

**Rollback Plan:**
- Revert changes to dashboard.blade.php and DashboardController.php
- Clear cache again

---

## 🎯 User Impact

**Positive:**
- More accurate representation of current account status
- Consistent with MT4/MT5 platform behavior
- No confusion about "missing" profit when positions are closed

**Neutral:**
- Users need to understand that "Profit" = floating P/L from open positions only
- For closed trade history, use account detail page or closed positions section

---

## 📚 Related Documentation

- See `/www/docs/technical/MT4_MT5_ARCHITECTURE.md` for details on profit calculation
- Dashboard caching strategy: 5-minute TTL with user+session+IP isolation

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
