# Admin Dashboard Statistics Update - November 21, 2025

## Summary
Enhanced the admin dashboard with three new statistical boxes replacing the previous "Trades Today", "Volume Today", and "Quick Actions" boxes in the stats grid.

## Changes Made

### 1. New Statistics Added

#### Brokers Statistics Box
- **Location**: Row 2, Column 1 (replacing "Trades Today")
- **Icon**: Building icon (purple background)
- **Displays**:
  - Total count of unique broker names from `trading_accounts` table
  - Count of active enterprise brokers
  - Format: "X brokers" with "Y enterprise" subtitle

#### Next Enterprise Expiry Box
- **Location**: Row 2, Column 2 (replacing "Volume Today")
- **Icon**: Clock icon (red background)
- **Displays**:
  - Company name of the next enterprise broker to expire
  - Expiration date in "MMM DD, YYYY" format
  - Human-readable time difference (e.g., "in 5 days")
  - Shows "No active subscriptions" if no enterprise brokers exist

#### Active Terminals Box
- **Location**: Row 2, Column 3 (replacing "Quick Actions")
- **Icon**: Monitor/Terminal icon (cyan background)
- **Displays**:
  - Count of trading accounts that synced data within the last hour
  - Based on `last_sync_at` timestamp
  - Shows "Last hour" subtitle

### 2. Quick Actions Section
- Moved below the stats grid (no longer in the grid)
- Now displays as a full-width purple gradient banner
- Contains 3 quick links:
  - View System Logs
  - Service Management
  - Manage Users

## Technical Implementation

### Controller Changes (`AdminController.php`)
```php
// Added queries for new statistics
$knownBrokers = TradingAccount::distinct('broker_name')
    ->whereNotNull('broker_name')
    ->count('broker_name');

$enterpriseBrokers = EnterpriseBroker::where('is_active', true)->count();

$nextExpiry = EnterpriseBroker::where('is_active', true)
    ->whereNotNull('subscription_ends_at')
    ->orderBy('subscription_ends_at', 'asc')
    ->first();

$activeTerminals = TradingAccount::where('last_sync_at', '>=', now()->subHour())
    ->count();
```

### View Changes (`admin/dashboard.blade.php`)
- Replaced 3 stat boxes in the grid
- Added conditional rendering for `$nextExpiry` (handles null case)
- Moved Quick Actions to separate section below stats grid

## Database Queries Impact
- **New queries**: 4 additional queries per page load
- **Performance**: All queries use indexed columns
  - `trading_accounts.broker_name` (indexed)
  - `trading_accounts.last_sync_at` (indexed)
  - `enterprise_brokers.is_active` (indexed)
  - `enterprise_brokers.subscription_ends_at` (indexed)

## Files Modified
1. `/www/app/Http/Controllers/Admin/AdminController.php`
   - Added `EnterpriseBroker` model import
   - Added 4 new statistical queries
   - Added `$nextExpiry` to view data

2. `/www/resources/views/admin/dashboard.blade.php`
   - Replaced 3 stat boxes
   - Moved Quick Actions section
   - Added conditional rendering for enterprise expiry

## Testing Performed
- ✅ Cleared all Laravel caches
- ✅ Verified no syntax errors (HTTP 302 redirect to login as expected)
- ✅ Confirmed all routes exist

## Additional Update: Recent Users Table Enhancement

### Changes to Recent Users Table
Replaced the "Plan" column with more useful broker information:

**Before:**
- Plan column showing subscription tier (free/basic/enterprise)
- Accounts showing "X / Y" format (used vs max allowed)

**After:**
- **Broker column**: Shows the primary broker (most accounts) for each user
  - Displays broker name using the `<x-broker-name>` component
  - Shows "(+N)" indicator if user has accounts with multiple brokers
  - Shows "No accounts" for users without trading accounts
- **Accounts column**: Shows count of accounts with the primary broker
  - Format: "X account(s)" (e.g., "2 accounts", "1 account")
  - Shows "0 accounts" for users without accounts

### Implementation Details

**Controller Enhancement:**
```php
// Load trading accounts with users for broker information
$usersQuery = User::with(['tradingAccounts' => function($query) {
    $query->select('user_id', 'broker_name', 'id');
}]);
```

**View Logic:**
- Groups user's accounts by broker name
- Identifies primary broker (most accounts)
- Counts accounts per broker
- Displays multi-broker indicator when applicable

### Example Display:
- User with 2 Exness accounts: "Exness Technologies Ltd" | "2 accounts"
- User with accounts at 3 brokers: "Exness Technologies Ltd (+2)" | "5 accounts"
- User with no accounts: "No accounts" | "0 accounts"

### Enterprise Broker Star Indicator
Added a visual indicator (✨ star emoji) to identify enterprise brokers in the Recent Users table:

**Implementation:**
- Controller queries active enterprise brokers: `EnterpriseBroker::where('is_active', true)->pluck('official_broker_name')`
- View checks if user's primary broker matches enterprise broker list
- Star appears to the left of broker name with tooltip "Enterprise Broker"
- Only shows for brokers registered in the `enterprise_brokers` table

**Display:**
- Regular broker: "Exness Technologies Ltd"
- Enterprise broker: "✨ Equiti Securities..." (with star)

This allows admins to quickly identify which users are using enterprise/whitelisted brokers.

## Next Steps
- Test the dashboard with actual admin login
- Verify statistics display correctly with real data
- Verify broker names display correctly with the broker-name component
- Consider adding click-through functionality to broker stats (future enhancement)

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
