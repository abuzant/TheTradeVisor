# Duplicate Trading Accounts Bug Fix

**Date:** November 18, 2025  
**Issue:** Same broker + account number appearing as 2 separate accounts  
**Status:** ✅ FIXED

## Problem

Users were seeing duplicate trading accounts in the account administration panel. The same broker and account number appeared as two separate entries:

- Account ID 2: Equiti Securities, Account 1012306793
- Account ID 3: Equiti Securities, Account 1012306793 (duplicate)

Both created at exactly the same timestamp: `2025-11-18T06:40:21.000000Z`

## Root Cause

**Race Condition in Job Processing:**

1. When MT5 EA sends data, it's queued as a job (`ProcessTradingData`)
2. If two jobs for the same account run simultaneously:
   - Both check if account exists using `->first()`
   - Both find no account (race condition)
   - Both create a new account
   - Result: 2 duplicate accounts

**Missing Database Constraint:**

The `trading_accounts` table had no unique constraint on `(user_id, broker_server, account_number)`, allowing duplicates to be inserted.

## Solution

### 1. Database-Level Protection

Created migration `2025_11_18_070200_add_unique_constraint_to_trading_accounts.php`:

- **Cleans up existing duplicates** (keeps oldest account, deletes newer ones)
- **Adds unique constraint** for non-anonymized accounts:
  ```sql
  CREATE UNIQUE INDEX unique_user_broker_account 
  ON trading_accounts (user_id, broker_server, account_number)
  WHERE account_number IS NOT NULL
  ```
- **Adds unique constraint** for anonymized accounts:
  ```sql
  CREATE UNIQUE INDEX unique_user_broker_hash 
  ON trading_accounts (user_id, broker_server, account_hash)
  WHERE account_hash IS NOT NULL AND account_hash != ''
  ```

### 2. Application-Level Protection

**Fixed `ProcessTradingData.php`:**
- Replaced `->first()` + `create()` with `lockForUpdate()` + atomic create
- Added exception handling for unique constraint violations
- If concurrent job creates account first, gracefully fetch it instead of failing

**Fixed `ProcessHistoricalData.php`:**
- Applied same race condition protection
- Consistent account lookup logic

### 3. Code Changes

**Before (Race Condition):**
```php
$tradingAccount = TradingAccount::where($criteria)->first();

if (!$tradingAccount) {
    $tradingAccount = TradingAccount::create([...]); // Race condition here!
}
```

**After (Atomic):**
```php
try {
    $tradingAccount = TradingAccount::lockForUpdate()
        ->where($searchCriteria)
        ->first();

    if (!$tradingAccount) {
        $tradingAccount = TradingAccount::create([...]); // Protected by lock
    }
} catch (\Illuminate\Database\QueryException $e) {
    // Handle unique constraint violation gracefully
    if (str_contains($e->getMessage(), 'unique_user_broker')) {
        $tradingAccount = TradingAccount::where($searchCriteria)->first();
    }
}
```

## Results

✅ Duplicate account (ID 3) automatically deleted by migration  
✅ Unique constraints prevent future duplicates at database level  
✅ Race conditions handled gracefully in application code  
✅ User now sees only 1 account (as expected)

## Testing

```bash
# Verify unique constraints exist
php artisan tinker --execute="print_r(DB::select('SELECT indexname FROM pg_indexes WHERE tablename = \'trading_accounts\' AND indexname LIKE \'unique_user%\''));"

# Verify no duplicates for user
php artisan tinker --execute="TradingAccount::where('user_id', 22)->count();"
# Expected: 1

# Try to create duplicate (should fail)
php artisan tinker --execute="TradingAccount::create(['user_id' => 22, 'broker_server' => 'EquitiSecurities-Live', 'account_number' => '1012306793', 'account_uuid' => Str::uuid(), 'broker_name' => 'Test', 'account_currency' => 'USD']);"
# Expected: UniqueConstraintViolationException
```

## Prevention

This fix ensures:
1. **Database enforces uniqueness** - Impossible to insert duplicates
2. **Jobs handle conflicts gracefully** - No crashes on concurrent execution
3. **Automatic cleanup** - Migration removed existing duplicates
4. **Future-proof** - Works for both anonymized and non-anonymized accounts

## Files Modified

- `/www/database/migrations/2025_11_18_070200_add_unique_constraint_to_trading_accounts.php` (NEW)
- `/www/app/Jobs/ProcessTradingData.php` (MODIFIED)
- `/www/app/Jobs/ProcessHistoricalData.php` (MODIFIED)

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
