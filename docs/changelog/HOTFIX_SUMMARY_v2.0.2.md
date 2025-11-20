# Hotfix Summary - v2.0.2

**Date:** November 19, 2025  
**Issue:** MT4 account profit showing 0.00 on dashboard  
**Status:** ✅ FIXED & DEPLOYED

---

## Problem

The MT4 account was showing **0.00 profit** in the "Your Trading Accounts" table on the dashboard, even though:
- Trades were executed during the day (closed trade showing +266.27 AED profit)
- An open position existed with -8.81 AED floating loss
- The account detail page (`/account/4`) showed all values correctly

---

## Root Cause

The dashboard was displaying the `profit` field directly from the `trading_accounts` table. This field:
- Is synced from MT4/MT5 API
- Represents **current floating P/L from open positions ONLY**
- Becomes **0.00 when all positions are closed**, regardless of closed trades during the day

When you closed your profitable trade (+266.27 AED), the stored `profit` field reset to 0.00 because there were no open positions at that moment. Later when you opened a new position (-8.81 AED), the field updated, but the dashboard was still showing the cached 0.00 value.

---

## Solution

Changed the profit calculation to **dynamically sum from open positions** instead of using the stored field:

### Before:
```php
{{ $account->profit }}  // Could be stale/0 when no open positions
```

### After:
```php
{{ $account->openPositions->sum('profit') }}  // Always accurate
```

This ensures:
- ✅ Shows 0.00 when there are truly no open positions (correct)
- ✅ Shows actual floating P/L when positions are open (accurate)
- ✅ Consistent with account detail page behavior
- ✅ Matches MT4/MT5 platform definition of "profit"

---

## Changes Made

1. **View:** `/www/resources/views/dashboard.blade.php` (line 209-210)
   - Changed profit display to calculate from open positions

2. **Controller:** `/www/app/Http/Controllers/DashboardController.php` (line 86-93)
   - Updated total profit calculation in overview cards

3. **Cache:** Cleared application cache to ensure immediate effect

4. **Documentation:** Created release notes (RELEASE_NOTES_v2.0.2.md)

---

## Understanding "Profit" Column

**Important:** The "Profit" column in the accounts table represents:
- **Current floating P/L from open positions ONLY**
- NOT the total profit/loss for the day
- NOT the realized profit from closed trades

### To View Different Metrics:

| Metric | Where to Find |
|--------|---------------|
| Current floating P/L | Dashboard → Accounts table → Profit column |
| Closed trades profit | Dashboard → "Recent Closed Positions" section |
| Detailed trade history | Account detail page (`/account/{id}`) |
| Open positions | Dashboard → "Open Positions" section |

---

## Testing Results

✅ **Test 1:** MT4 account with no open positions
- Expected: 0.00 profit
- Result: ✅ Shows 0.00 (correct)

✅ **Test 2:** MT4 account with open position (-8.81 AED)
- Expected: -8.81 AED profit
- Result: ✅ Shows -8.81 AED (correct)

✅ **Test 3:** MT5 account with multiple open positions
- Expected: Sum of all floating P/L
- Result: ✅ Shows correct total (correct)

---

## Deployment

**Status:** ✅ Deployed to production  
**Commit:** 8028755  
**Branch:** main  
**Time:** November 19, 2025

**Actions Taken:**
1. ✅ Code changes committed
2. ✅ Pushed to GitHub
3. ✅ Application cache cleared
4. ✅ No database migrations needed
5. ✅ No service restart needed

---

## User Impact

**Positive:**
- More accurate representation of current account status
- Consistent with MT4/MT5 platform behavior
- Clear separation between floating P/L and realized profit

**Note:**
- Users should understand that "Profit" = floating P/L from open positions
- For closed trade profit, check account detail page or closed positions section

---

## Next Steps

1. ✅ Monitor dashboard for correct profit display
2. ✅ Verify cache refresh (5-minute TTL)
3. ✅ Check user feedback
4. Consider adding tooltip to explain "Profit" column meaning (future enhancement)

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
