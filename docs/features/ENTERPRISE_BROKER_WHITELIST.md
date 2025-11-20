# Enterprise Broker Whitelist Feature

## Overview

The Enterprise Broker Whitelist feature allows forex brokers to subscribe to an enterprise plan, enabling all their clients to receive unlimited free account monitoring on TheTradeVisor. This creates a win-win-win scenario:

- **Brokers**: Competitive advantage and client retention tool
- **Traders**: Free unlimited account monitoring with their broker
- **TheTradeVisor**: Recurring revenue from enterprise subscriptions

## How It Works

### 1. Broker Subscription
- Broker purchases Enterprise Plan ($X/month per MT4/MT5 server)
- Broker configures their official broker name in settings
- System whitelists the broker name

### 2. User Experience
- Any user creates a free account on TheTradeVisor
- User connects their MT4/MT5 account using their API key
- System checks if broker name matches a whitelisted broker
- If match found: Account limits bypassed, unlimited free accounts
- If no match: Normal subscription limits apply

### 3. Multi-Broker Support
- Users can have accounts with multiple whitelisted brokers
- Each whitelisted broker bypasses limits independently
- Non-whitelisted brokers still count toward subscription limits

## Database Schema

### `enterprise_brokers` Table
```sql
- id: Primary key
- user_id: Foreign key to users (broker admin account)
- company_name: Display name for the broker
- official_broker_name: Exact MT4/MT5 server name (unique)
- is_active: Boolean (subscription status)
- monthly_fee: Decimal (subscription cost)
- subscription_ends_at: Timestamp (when subscription expires)
- grace_period_ends_at: Timestamp (30-day grace period)
- created_at, updated_at: Timestamps
```

### `whitelisted_broker_usage` Table
```sql
- id: Primary key
- user_id: Foreign key to users
- trading_account_id: Foreign key to trading_accounts
- enterprise_broker_id: Foreign key to enterprise_brokers
- account_number: MT4/MT5 account number
- first_seen_at: Timestamp (first data received)
- last_seen_at: Timestamp (most recent data)
- created_at, updated_at: Timestamps
```

## Models

### EnterpriseBroker
**Location**: `app/Models/EnterpriseBroker.php`

**Relationships**:
- `belongsTo(User::class)` - Admin user who owns the broker
- `hasMany(WhitelistedBrokerUsage::class)` - Usage records

**Key Methods**:
- `isCurrentlyActive()`: Checks if active or in grace period
- `isInGracePeriod()`: Checks if in 30-day grace period
- `getTotalUsersCount()`: Count of unique users
- `getActiveUsersCount()`: Count of users active in last 7 days

### WhitelistedBrokerUsage
**Location**: `app/Models/WhitelistedBrokerUsage.php`

**Relationships**:
- `belongsTo(User::class)` - User who owns the account
- `belongsTo(TradingAccount::class)` - The trading account
- `belongsTo(EnterpriseBroker::class)` - The enterprise broker

## Data Collection Flow

### Modified DataCollectionController
**Location**: `app/Http/Controllers/Api/DataCollectionController.php`

**Logic**:
1. Receive data from EA
2. Extract broker name from account info
3. Query `enterprise_brokers` for matching `official_broker_name`
4. If found and active: Set `$bypassLimits = true`
5. If found but in grace period: Set `$bypassLimits = true` + warning message
6. If not found or expired: Apply normal subscription limits
7. Track usage in `whitelisted_broker_usage` table

**Key Code**:
```php
$whitelistedBroker = EnterpriseBroker::where('official_broker_name', $brokerName)->first();

if ($whitelistedBroker && $whitelistedBroker->isCurrentlyActive()) {
    $bypassLimits = true;
}

if (!$existingAccount && !$bypassLimits) {
    // Check account limits
}
```

## Enterprise Admin Panel

### Routes
**Location**: `routes/web.php`

```php
Route::prefix('enterprise')->name('enterprise.')->group(function () {
    Route::get('/dashboard', [EnterpriseController::class, 'dashboard']);
    Route::get('/settings', [EnterpriseController::class, 'settings']);
    Route::post('/settings', [EnterpriseController::class, 'updateSettings']);
    Route::get('/analytics', [EnterpriseController::class, 'analytics']);
});
```

### Views
**Location**: `resources/views/enterprise/`

1. **dashboard.blade.php**: Overview with stats cards
2. **settings.blade.php**: Configure broker name
3. **analytics.blade.php**: User activity table

### Controller
**Location**: `app/Http/Controllers/EnterpriseController.php`

**Methods**:
- `dashboard()`: Show stats overview
- `settings()`: Show settings form
- `updateSettings()`: Update broker configuration
- `analytics()`: Show detailed user analytics

## Grace Period System

### How It Works
1. When broker cancels subscription:
   - `is_active` set to `false`
   - `subscription_ends_at` set to current time
   - `grace_period_ends_at` set to 30 days from now

2. During grace period:
   - Service continues to work normally
   - Users see warning message in API response
   - Broker sees warning in dashboard

3. After grace period expires:
   - Account limits enforced for new accounts
   - Existing accounts continue to work
   - Users must upgrade to add more accounts

### Implementation
```php
if ($broker->is_active) {
    $bypassLimits = true;
} elseif ($broker->grace_period_ends_at && $broker->grace_period_ends_at > now()) {
    $bypassLimits = true;
    $gracePeriodMessage = "Grace period ends: " . $broker->grace_period_ends_at->format('Y-m-d');
}
```

## API Response Changes

### Success Response (Whitelisted)
```json
{
    "success": true,
    "message": "Data received successfully",
    "data_type": "current",
    "timestamp": "2025-11-19T08:00:00Z",
    "queued": true,
    "whitelisted_broker": true
}
```

### Success Response (Grace Period)
```json
{
    "success": true,
    "message": "Data received successfully",
    "data_type": "current",
    "timestamp": "2025-11-19T08:00:00Z",
    "queued": true,
    "whitelisted_broker": true,
    "grace_period_warning": "Broker's enterprise plan expired. Grace period ends: 2025-12-19"
}
```

## Usage Tracking

### Purpose
Track which users are benefiting from enterprise plan for broker analytics.

### Tracking Logic
```php
WhitelistedBrokerUsage::updateOrCreate(
    [
        'user_id' => $user->id,
        'trading_account_id' => $tradingAccount->id,
    ],
    [
        'enterprise_broker_id' => $whitelistedBroker->id,
        'account_number' => $accountInfo['account_number'],
        'last_seen_at' => now(),
        'first_seen_at' => DB::raw('COALESCE(first_seen_at, NOW())'),
    ]
);
```

## Business Model

### Pricing Structure
- **Per MT4/MT5 Server**: $X/month
- **Multiple Legal Entities**: Separate subscription per server name
- **Example**: Equiti with 4 entities = 4 × $X/month

### Value Proposition
- Broker pays $500/month
- Each client gets $29/month value (Pro plan equivalent)
- With 100 clients: $2,900/month value delivered
- ROI: 580% value to clients

## Security Considerations

### Broker Name Validation
- Broker name comes from `AccountCompany()` in MQL
- **Cannot be spoofed** by EA or user
- Read-only value from MT4/MT5 server
- No manual approval needed

### Account Limit Bypass
- Only applies to whitelisted brokers
- Checked on every data ingestion
- Real-time validation
- No stored coupons or tokens

## Testing

### Test Checklist
- ✅ Database tables created
- ✅ Models load correctly
- ✅ Routes registered
- ✅ Views render
- ✅ Broker whitelist check works
- ✅ Account limit bypass works
- ✅ Grace period logic works
- ✅ Usage tracking works

### Manual Testing Steps
1. Create enterprise broker record
2. Set official_broker_name to test broker
3. Create user account
4. Send data from EA with matching broker name
5. Verify account limits bypassed
6. Check usage tracking in database
7. View enterprise dashboard
8. Update broker settings
9. View analytics page

## Future Enhancements

### Potential Features
1. **Broker Branding**: Custom logo/colors in user dashboard
2. **White-label Dashboard**: Broker-specific subdomain
3. **Bulk User Management**: CSV import/export
4. **Advanced Analytics**: Detailed performance metrics
5. **API Access**: Programmatic broker management
6. **Multi-Server Support**: Single subscription for multiple servers
7. **Tiered Pricing**: Based on number of active users

## Troubleshooting

### Common Issues

**Issue**: Broker name not matching
- **Cause**: Typo in official_broker_name
- **Solution**: Check exact string from `AccountCompany()`

**Issue**: Users still hitting limits
- **Cause**: Broker not active or grace period expired
- **Solution**: Check broker `is_active` and `grace_period_ends_at`

**Issue**: Usage not tracking
- **Cause**: Trading account not found
- **Solution**: Ensure account created before tracking

## Files Modified/Created

### Created Files
1. `database/migrations/2025_11_19_081720_create_enterprise_brokers_table.php`
2. `database/migrations/2025_11_19_081807_create_whitelisted_broker_usage_table.php`
3. `app/Models/EnterpriseBroker.php`
4. `app/Models/WhitelistedBrokerUsage.php`
5. `app/Http/Controllers/EnterpriseController.php`
6. `resources/views/enterprise/dashboard.blade.php`
7. `resources/views/enterprise/settings.blade.php`
8. `resources/views/enterprise/analytics.blade.php`
9. `docs/ENTERPRISE_BROKER_WHITELIST.md`

### Modified Files
1. `app/Http/Controllers/Api/DataCollectionController.php` - Added whitelist logic
2. `app/Models/User.php` - Added enterpriseBroker relationship
3. `app/Models/TradingAccount.php` - Added whitelistedBrokerUsage relationship
4. `routes/web.php` - Added enterprise routes

## Deployment Checklist

- [x] Run migrations
- [x] Clear cache
- [x] Test routes
- [x] Test views
- [x] Test data collection
- [ ] Create first enterprise broker (manual)
- [ ] Test with real MT4/MT5 data
- [ ] Monitor logs for errors
- [ ] Update user documentation
- [ ] Create sales materials

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
