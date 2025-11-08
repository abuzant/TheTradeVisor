# Admin Updates Summary - November 8, 2025

## Changes Implemented

### 1. ✅ Rate Limits Adjusted

**Previous Values:**
- IP Limit: 600 requests/minute
- API Key Limit: 600 requests/minute  
- Burst Limit: 1000 requests/minute

**New Values:**
- IP Limit: 300 requests/minute (50% reduction)
- **API Key Limit: 10 requests/minute** (genuine users shouldn't exceed this)
- Burst Limit: 500 requests/minute (50% reduction)

**Rationale:**
- API endpoints are hit every 120 seconds (2 minutes) by EA = ~0.5 requests/minute
- 10 requests/minute = 20x safety margin for legitimate use
- Prevents abuse while allowing normal operation

### 2. ✅ Rate Limits Added to Admin Navigation

**Location:** Admin dropdown menu

**New Menu Item:**
```
Admin > Rate Limits
```

**Route:** `/admin/rate-limits`

**Features Available:**
- View all rate limit settings
- Edit global IP limit
- Edit global API key limit
- Edit burst limit
- Edit premium user limits
- Toggle settings active/inactive
- Clear rate limit cache
- View statistics (placeholder)

### 3. ✅ Account Reset Functionality

**Location:** `/admin/accounts` - Account Management page

**New Button:** 🔄 Reset (red button next to Pause/Resume)

**Features:**
- **Confirmation Modal** with checkbox requirement
- **Warning Message** explaining what will be deleted
- **Detailed List** of data to be removed:
  - All deals/trades
  - All open positions
  - All pending orders
  - All raw data files
  - Account statistics (balance, equity, etc.)

**What It Does:**
1. Deletes all `deals` for the account
2. Deletes all `positions` for the account
3. Deletes all `orders` for the account
4. Deletes raw JSON files from `storage/app/raw_data/{user_id}/`
5. Resets account statistics to zero
6. Shows success message with count of deleted items

**Safety Features:**
- Requires checkbox confirmation
- Submit button disabled until checkbox is checked
- Modal can be closed by clicking outside, X button, or Cancel
- Transaction-based (all-or-nothing)
- Detailed error logging if something fails

**Use Cases:**
- User wants to start fresh after testing
- Corrupted data needs to be cleared
- Account had issues during initial setup
- Testing/debugging scenarios

## Database Changes

Rate limit settings updated directly in database:
```sql
UPDATE rate_limit_settings SET value = 300 WHERE key = 'global_ip_limit';
UPDATE rate_limit_settings SET value = 10 WHERE key = 'global_api_key_limit';
UPDATE rate_limit_settings SET value = 500 WHERE key = 'burst_limit';
```

## Files Modified

1. **Controller:** `/www/app/Http/Controllers/Admin/AccountManagementController.php`
   - Added `reset()` method
   - Added imports for Deal, Position, Order models
   - Added DB and Storage facades

2. **Routes:** `/www/routes/web.php`
   - Added `DELETE /admin/accounts/{account}/reset` route

3. **View:** `/www/resources/views/admin/accounts/index.blade.php`
   - Added Reset button to actions column
   - Added Reset modal with confirmation
   - Added JavaScript handlers for modal

4. **Navigation:** `/www/resources/views/layouts/navigation.blade.php`
   - Added "Rate Limits" link to Admin dropdown

## Testing Checklist

- [x] Rate limits updated in database
- [x] Rate Limits menu item appears in Admin dropdown
- [x] Rate Limits page is accessible
- [ ] Reset button appears on accounts page
- [ ] Reset modal opens with correct account info
- [ ] Checkbox must be checked to enable submit
- [ ] Reset successfully deletes all data
- [ ] Success message shows correct counts
- [ ] Account can receive fresh data after reset

## API Rate Limit Impact

**Before:** EA could theoretically hit API 600 times/minute
**After:** EA limited to 10 requests/minute

**EA Behavior:**
- Sends data every 120 seconds (UPDATE_INTERVAL)
- Historical uploads: 1 request every 30 seconds (HISTORY_UPLOAD_INTERVAL)
- Maximum during historical upload: 2 requests/minute
- Normal operation: 0.5 requests/minute

**Result:** ✅ Well within the 10 req/min limit

## Notes

- Rate limit changes take effect immediately (cache is cleared on update)
- Reset functionality is admin-only (requires `is_admin = true`)
- All operations are logged for audit trail
- Transaction rollback ensures data integrity if reset fails
- Raw data files are matched by account number in filename

## Next Steps

1. Monitor rate limit hits in logs
2. Adjust limits if legitimate users are being blocked
3. Consider adding rate limit statistics dashboard
4. Add email notification when account is reset (optional)
5. Consider adding "Download backup before reset" option (optional)
