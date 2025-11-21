# New Monetization Model - Implementation Plan

## Executive Summary

**Business Model Shift:**
- FROM: Paid subscriptions with account limits
- TO: Free for all users, time-based view restrictions, broker-pays model

**Revenue Model:**
- Free users: Unlimited accounts, 7-day view only
- Enterprise brokers: $999/month, users get 180-day view
- Target: 10-20 brokers = $10-20K/month recurring

---

## Confirmed Requirements

### 1. Subscription System Removal
- ✅ Remove `subscription_tier` from users table
- ✅ Remove `max_accounts` from users table
- ✅ Remove all account limit checks
- ✅ All users = FREE with unlimited accounts

### 2. Time-Based View Restrictions
**Standard Broker Accounts:**
- Today + 7 Days ONLY
- Locked: 30D, 90D, 180D views

**Enterprise Broker Accounts:**
- Today + 7D + 30D + 90D + 180D (all unlocked)

### 3. Data Retention
- ALL accounts: 180 days minimum retention
- Reason: Instant unlock when broker upgrades
- Frontend: Restrict VIEW, not storage

### 4. Enterprise Subdomain Architecture
**Main Site:** `https://thetradevisor.com`
- Regular users
- See their own accounts only
- Time restrictions apply

**Enterprise Portal:** `https://enterprise.thetradevisor.com`
- Broker admin login
- See ALL accounts connected to their server(s)
- Same dashboard/performance/snapshot views
- Aggregated data across all their users
- Full 180-day access (obviously)

### 5. Enterprise API Endpoints
**New API routes for enterprise brokers:**
- Aggregated data for all their users
- Filterable by:
  - Day/Date range
  - Symbol
  - Country
  - Terminal type (MT4/MT5)
  - Account type
- Metrics available:
  - Drawdown data
  - Free margin data
  - Top pairs
  - Best days
  - Best hours
  - Everything a user sees for 1 account, but aggregated

### 6. Admin Module
- Global admin ONLY (you)
- Manage enterprise brokers
- Add/edit/delete broker records
- Set `official_broker_name`
- Toggle active status
- View usage statistics

### 7. Ads
- Implement LATER (not in this phase)

---

## Implementation Order

### Phase 1: Database Changes (FIRST)
1. Remove subscription fields from users table
2. Ensure enterprise_brokers table is ready
3. Ensure whitelisted_broker_usage table is ready
4. Add any missing indexes
5. Test migrations

### Phase 2: API Changes (SECOND)
1. Remove account limit enforcement
2. Add time restriction logic to all data queries
3. Add `max_days_view` to API responses
4. Create new enterprise API endpoints
5. Add enterprise API authentication
6. Test all endpoints

### Phase 3: Frontend Changes (THIRD)
1. Update main site views (time filters)
2. Create enterprise subdomain
3. Create enterprise dashboard/views
4. Add upgrade prompts for locked views
5. Update FAQ/documentation
6. Test all user flows

---

## Critical Architecture Decisions

### Enterprise Subdomain Setup

**Option A: Separate Laravel App**
- Pros: Clean separation, independent scaling
- Cons: Code duplication, separate deployment

**Option B: Same Laravel App, Different Routes**
- Pros: Shared codebase, single deployment
- Cons: Route complexity, middleware management

**Option C: Same App, Subdomain Routing**
- Pros: Best of both worlds
- Cons: Nginx configuration needed

**RECOMMENDED: Option C**
```php
// routes/web.php
Route::domain('enterprise.thetradevisor.com')->group(function () {
    Route::middleware(['auth', 'enterprise'])->group(function () {
        Route::get('/dashboard', [EnterpriseDashboardController::class, 'index']);
        Route::get('/performance', [EnterprisePerformanceController::class, 'index']);
        Route::get('/snapshots', [EnterpriseSnapshotController::class, 'index']);
    });
});
```

### Enterprise Authentication

**Question:** How do enterprise brokers authenticate?
- Same user table with `is_enterprise_admin` flag?
- Separate `enterprise_admins` table?
- Link via `enterprise_brokers.user_id`?

**Current System:** `enterprise_brokers.user_id` links to users table

**RECOMMENDED:** 
- Use existing users table
- Add `is_enterprise_admin` boolean flag
- Middleware checks: `auth` + `is_enterprise_admin`
- Enterprise admin can only see data for their broker(s)

### Data Aggregation Strategy

**For Enterprise Dashboard:**
```php
// Get all accounts for this broker
$broker = EnterpriseBroker::where('user_id', auth()->id())->first();
$accountIds = WhitelistedBrokerUsage::where('enterprise_broker_id', $broker->id)
    ->pluck('trading_account_id');

// Aggregate metrics
$totalBalance = TradingAccount::whereIn('id', $accountIds)->sum('balance');
$totalEquity = TradingAccount::whereIn('id', $accountIds)->sum('equity');
$totalProfit = Deal::whereIn('trading_account_id', $accountIds)
    ->where('entry', 'out')
    ->sum('profit');
```

---

## Files to Create/Modify

### Phase 1: Database
**Create:**
- `database/migrations/YYYY_MM_DD_remove_subscription_fields.php`
- `database/migrations/YYYY_MM_DD_add_enterprise_admin_flag.php`

**Modify:**
- None (migrations are additive)

### Phase 2: API
**Create:**
- `app/Http/Controllers/Api/EnterpriseApiController.php`
- `app/Http/Middleware/EnterpriseApiAuth.php`
- `routes/api-enterprise.php`

**Modify:**
- `app/Http/Controllers/Api/DataCollectionController.php` (remove limits)
- `app/Models/TradingAccount.php` (add helper methods)
- `app/Models/User.php` (remove subscription logic)
- All analytics controllers (add time restrictions)

### Phase 3: Frontend
**Create:**
- `app/Http/Controllers/Enterprise/DashboardController.php`
- `app/Http/Controllers/Enterprise/PerformanceController.php`
- `app/Http/Controllers/Enterprise/SnapshotController.php`
- `app/Http/Middleware/EnterpriseAuth.php`
- `resources/views/enterprise/dashboard.blade.php`
- `resources/views/enterprise/performance.blade.php`
- `resources/views/enterprise/snapshots.blade.php`
- `resources/views/enterprise/layouts/app.blade.php`

**Modify:**
- `resources/views/dashboard.blade.php` (time filters)
- `resources/views/performance/index.blade.php` (time filters)
- `resources/views/analytics/*.blade.php` (time filters)
- All views with date ranges

---

## Testing Strategy

### Phase 1 Tests
- [ ] Migration runs without errors
- [ ] Existing data preserved
- [ ] No broken foreign keys
- [ ] Indexes created correctly

### Phase 2 Tests
- [ ] Account limit removed (can add unlimited accounts)
- [ ] Time restriction works (7 days for standard)
- [ ] Time restriction works (180 days for enterprise)
- [ ] API returns correct `max_days_view`
- [ ] Enterprise API endpoints work
- [ ] Enterprise API authentication works
- [ ] Aggregated data is correct

### Phase 3 Tests
- [ ] Main site time filters work
- [ ] Locked views show upgrade prompt
- [ ] Enterprise subdomain accessible
- [ ] Enterprise dashboard shows aggregated data
- [ ] Enterprise performance page works
- [ ] Enterprise snapshots page works
- [ ] Broker can only see their own accounts

---

## Rollback Plan

### If Something Goes Wrong
1. **Database:** Keep backup before migration
2. **API:** Feature flag to enable/disable new logic
3. **Frontend:** Keep old views in separate directory
4. **Subdomain:** Can disable via Nginx config

### Rollback Commands
```bash
# Database
php artisan migrate:rollback --step=1

# Cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Services
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
```

---

## Success Metrics

### Technical Metrics
- [ ] Zero downtime during deployment
- [ ] No data loss
- [ ] All tests passing
- [ ] Page load times < 2s
- [ ] API response times < 500ms

### Business Metrics
- [ ] User signup rate increases
- [ ] Account creation rate increases
- [ ] First broker signs up within 30 days
- [ ] 5 brokers within 90 days
- [ ] 10+ brokers within 6 months

---

## Next Steps

1. **Clarify enterprise subdomain architecture** with user
2. **Clarify enterprise authentication** mechanism
3. **Start Phase 1:** Database migrations
4. **Test Phase 1:** Verify data integrity
5. **Start Phase 2:** API changes
6. **Test Phase 2:** Verify all endpoints
7. **Start Phase 3:** Frontend changes
8. **Test Phase 3:** End-to-end testing
9. **Deploy:** Production rollout
10. **Monitor:** Logs and metrics

---

## ✅ CONFIRMED REQUIREMENTS (Nov 21, 2025)

### 1. Account Privacy in Enterprise Portal
**CONFIRMED:** Show account numbers only (no names/emails)
- Broker already has full details on their MT servers
- We show: Account number, balance, equity, profit, trades
- We DON'T show: User name, email, personal info
- Broker can cross-reference account numbers in their MT server if needed

### 2. Multiple Broker Servers = Multiple Subscriptions
**CONFIRMED:** Each unique broker name = separate $999/month subscription
- Example: Equiti with 4 legal entities = 4 × $999 = $3,996/month
- Reason: MetaQuotes limitation (each server has unique broker name)
- Each enterprise_brokers record = one unique `official_broker_name`
- NO mixing of different broker names in one subscription

### 3. Enterprise Admin Creation Flow
**CONFIRMED:** Manual creation by global admin (you)
- You create enterprise broker from admin panel
- You set initial credentials
- Broker receives login details
- No self-signup, no approval workflow

### 4. Enterprise API Authentication
**CONFIRMED:** Separate API key with custom prefix
- Standard user API keys: `tvsr_...`
- Enterprise API keys: `ent_...` (or similar prefix)
- Set manually when creating enterprise broker
- Used for programmatic access to aggregated data

### 5. Data Aggregation Scope & Filters
**CONFIRMED:** All accounts (active + dormant) with filtration
- Default view: Last 30 days, all accounts
- Time filters: 1D, 7D, 30D, 60D, 90D, 180D
- Account filters:
  - By country
  - By platform (MT4/MT5)
  - By symbol
  - By active/dormant status
- Show stats: "X active accounts, Y dormant accounts"
- Dormant = no activity in last 30 days

### 6. Subdomain Technical Setup
**CONFIRMED:** DNS ready, need SSL + Nginx config
- DNS: Already configured on Cloudflare
- SSL: Need Let's Encrypt setup for enterprise.thetradevisor.com
- Nginx: Need server block configuration
- Laravel: Subdomain routing in routes/web.php

---

## Enterprise Portal Detailed Specifications

### Enterprise Dashboard Page
**URL:** `https://enterprise.thetradevisor.com/dashboard`

**Top Stats Cards (Aggregated for all broker accounts):**
1. **Total Accounts**
   - Count of all accounts connected to this broker
   - Active vs Dormant breakdown
   - Sparkline showing growth over time

2. **Total Balance**
   - Sum of all account balances
   - Change % from previous period
   - Currency: USD (or broker's base currency)

3. **Total Equity**
   - Sum of all account equities
   - Change % from previous period
   - Floating P/L indicator

4. **Total Profit/Loss**
   - Sum of all closed trade profits
   - Win rate %
   - Best/worst day

5. **Total Trades**
   - Count of all trades executed
   - Average trades per account
   - Trades per day

**Filters Section:**
- Time Range: [1D] [7D] [30D] [60D] [90D] [180D] (default: 30D)
- Platform: [All] [MT4] [MT5]
- Country: [All] [USA] [UK] [UAE] ... (dropdown)
- Status: [All] [Active] [Dormant]
- Symbol: [All] [EURUSD] [GBPUSD] ... (dropdown)

**Charts Section:**
1. **Equity Curve (Aggregated)**
   - Line chart showing combined equity over time
   - X-axis: Date, Y-axis: Total Equity

2. **Profit by Symbol**
   - Bar chart showing profit/loss per symbol
   - Top 10 most traded symbols

3. **Profit by Country**
   - Pie chart or bar chart
   - Geographic distribution of profits

4. **Activity Heatmap**
   - Trading activity by hour/day
   - Shows when traders are most active

**Accounts Table (Bottom):**
- Columns: Account Number, Platform, Country, Balance, Equity, Profit, Trades, Last Activity
- Sortable by any column
- Paginated (50 per page)
- Click row → view individual account details

---

### Enterprise Performance Page
**URL:** `https://enterprise.thetradevisor.com/performance`

**Aggregated Metrics:**
1. **Win Rate**
   - Overall win rate across all accounts
   - Breakdown by symbol
   - Breakdown by platform (MT4/MT5)

2. **Average Trade Duration**
   - Mean holding time for all trades
   - By symbol
   - By account

3. **Profit Factor**
   - Gross profit / Gross loss
   - Industry benchmark comparison

4. **Drawdown Analysis**
   - Maximum drawdown across all accounts
   - Average drawdown per account
   - Recovery time

5. **Best/Worst Performers**
   - Top 10 accounts by profit
   - Bottom 10 accounts by loss
   - Most consistent accounts (lowest volatility)

**Filters:** Same as dashboard (Time, Platform, Country, Status, Symbol)

**Charts:**
1. **Cumulative Profit Chart**
   - Stacked area chart showing profit accumulation
   - Can toggle individual accounts on/off

2. **Trade Distribution**
   - Histogram of trade profits/losses
   - Shows risk distribution

3. **Symbol Performance Matrix**
   - Heatmap showing profit by symbol and time period

---

### Enterprise Snapshots Page
**URL:** `https://enterprise.thetradevisor.com/snapshots`

**Purpose:** List all account snapshots with detailed filtering

**Table Columns:**
- Account Number
- Platform (MT4/MT5)
- Country
- Balance
- Equity
- Margin
- Free Margin
- Margin Level %
- Open Trades
- Floating P/L
- Last Update
- Actions: [View Details]

**Filters:** Same as dashboard

**Export Options:**
- Export to CSV
- Export to Excel
- Export to PDF

**Bulk Actions:**
- Compare selected accounts
- Generate report for selected accounts

---

### Enterprise Analytics Page
**URL:** `https://enterprise.thetradevisor.com/analytics`

**Advanced Analytics:**
1. **Trading Hours Analysis**
   - Profit by hour of day
   - Best trading hours
   - Worst trading hours

2. **Symbol Correlation**
   - Which symbols are traded together
   - Correlation matrix

3. **Risk Metrics**
   - Average risk per trade
   - Risk-adjusted returns
   - Sharpe ratio (if applicable)

4. **Trader Behavior**
   - Average trades per day
   - Position sizing patterns
   - Hold time distribution

5. **Geographic Insights**
   - Performance by country
   - Platform preference by country
   - Active hours by timezone

**Filters:** Same as dashboard

---

## Enterprise API Endpoints Specification

### Base URL
`https://thetradevisor.com/api/enterprise/v1/`

### Authentication
```http
Authorization: Bearer ent_abc123xyz...
```

### Endpoints

#### 1. Get All Accounts
```http
GET /accounts
```

**Query Parameters:**
- `platform`: mt4|mt5
- `country`: US|UK|AE|...
- `status`: active|dormant|all
- `page`: 1
- `per_page`: 50

**Response:**
```json
{
  "success": true,
  "data": {
    "total": 150,
    "active": 120,
    "dormant": 30,
    "accounts": [
      {
        "account_number": "12345678",
        "platform": "MT5",
        "country": "AE",
        "balance": 10000.00,
        "equity": 10250.00,
        "profit": 250.00,
        "trades": 45,
        "last_activity": "2025-11-21T06:00:00Z"
      }
    ]
  },
  "pagination": {
    "current_page": 1,
    "total_pages": 3,
    "per_page": 50
  }
}
```

#### 2. Get Aggregated Metrics
```http
GET /metrics
```

**Query Parameters:**
- `period`: 1d|7d|30d|60d|90d|180d
- `platform`: mt4|mt5|all
- `country`: US|UK|AE|all
- `symbol`: EURUSD|GBPUSD|all

**Response:**
```json
{
  "success": true,
  "data": {
    "total_accounts": 150,
    "total_balance": 1500000.00,
    "total_equity": 1525000.00,
    "total_profit": 25000.00,
    "total_trades": 6750,
    "win_rate": 62.5,
    "profit_factor": 1.85,
    "max_drawdown": 12.3,
    "best_symbol": "EURUSD",
    "worst_symbol": "GBPJPY"
  }
}
```

#### 3. Get Performance Data
```http
GET /performance
```

**Query Parameters:** Same as metrics

**Response:**
```json
{
  "success": true,
  "data": {
    "equity_curve": [
      {"date": "2025-11-01", "equity": 1500000},
      {"date": "2025-11-02", "equity": 1505000}
    ],
    "profit_by_symbol": {
      "EURUSD": 15000,
      "GBPUSD": 8000,
      "USDJPY": -2000
    },
    "profit_by_country": {
      "AE": 12000,
      "US": 8000,
      "UK": 5000
    }
  }
}
```

#### 4. Get Top Performers
```http
GET /top-performers
```

**Query Parameters:**
- `period`: 1d|7d|30d|60d|90d|180d
- `limit`: 10 (default)
- `sort`: profit|win_rate|trades

**Response:**
```json
{
  "success": true,
  "data": {
    "top_accounts": [
      {
        "account_number": "12345678",
        "profit": 5000.00,
        "win_rate": 75.0,
        "trades": 120
      }
    ]
  }
}
```

#### 5. Get Trading Hours Analysis
```http
GET /trading-hours
```

**Response:**
```json
{
  "success": true,
  "data": {
    "best_hours": [
      {"hour": 14, "profit": 5000, "trades": 450},
      {"hour": 15, "profit": 4500, "trades": 420}
    ],
    "worst_hours": [
      {"hour": 3, "profit": -1000, "trades": 50}
    ]
  }
}
```

#### 6. Export Data
```http
GET /export
```

**Query Parameters:**
- `format`: csv|excel|pdf
- `type`: accounts|metrics|performance
- `period`: 1d|7d|30d|60d|90d|180d

**Response:** File download

---

## Critical Technical Questions Before Implementation

### Question 1: Enterprise API Key Storage
**Where should we store the enterprise API key?**

**Option A:** In `users` table
```sql
ALTER TABLE users ADD COLUMN enterprise_api_key VARCHAR(255) UNIQUE;
```
- Pro: Simple, one place
- Con: Mixes user auth with API auth

**Option B:** In `enterprise_brokers` table
```sql
ALTER TABLE enterprise_brokers ADD COLUMN api_key VARCHAR(255) UNIQUE;
```
- Pro: Cleaner separation
- Con: Need to join tables for auth

**Option C:** New `enterprise_api_keys` table
```sql
CREATE TABLE enterprise_api_keys (
    id BIGSERIAL PRIMARY KEY,
    enterprise_broker_id BIGINT REFERENCES enterprise_brokers(id),
    key VARCHAR(255) UNIQUE,
    name VARCHAR(255), -- "Production API", "Dev API"
    last_used_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```
- Pro: Multiple keys per broker, better security
- Con: More complex

**RECOMMENDATION:** Option C (allows key rotation, multiple environments)

---

### Question 2: Broker Admin Panel Location
**Where should YOU manage enterprise brokers?**

**Option A:** Main site admin panel
- URL: `https://thetradevisor.com/admin/brokers`
- Pro: Centralized admin
- Con: Mixes with user admin

**Option B:** Separate admin subdomain
- URL: `https://admin.thetradevisor.com/brokers`
- Pro: Clean separation
- Con: Another subdomain to manage

**Option C:** Enterprise subdomain admin section
- URL: `https://enterprise.thetradevisor.com/admin/brokers`
- Pro: All enterprise stuff in one place
- Con: Confusing (admin managing brokers on broker portal?)

**RECOMMENDATION:** Option A (main site admin panel)

---

### Question 3: Account Country Detection ✅ VERIFIED
**How do we determine account country?**

**CONFIRMED:** `trading_accounts` table has country fields:
- `country_code` (VARCHAR 2) - ISO 2-letter code (e.g., "AE", "US", "UK")
- `country_name` (VARCHAR 100) - Full name (e.g., "United Arab Emirates")
- `detected_country` (VARCHAR 10) - Legacy field
- Indexed on `country_code`

**Source:** IP-based detection when EA connects

**For enterprise filters:** Use `trading_accounts.country_code`

---

### Question 4: Symbol Data Storage ✅ VERIFIED
**Where is symbol data stored?**

**CONFIRMED:** Multiple tables with `symbol` column:
1. **`deals` table** - Closed trades
   - `symbol` (VARCHAR 20, nullable, indexed)
   - Used for profit/loss by symbol
   
2. **`positions` table** - Open positions
   - `symbol` (VARCHAR 20, indexed)
   - Used for current exposure by symbol
   
3. **`orders` table** - Pending orders
   - `symbol` (VARCHAR 20, indexed)
   
4. **`symbol_mappings` table** - Symbol normalization
   - `raw_symbol` (VARCHAR 50) - Broker-specific (e.g., "EURUSD.a")
   - `normalized_symbol` (VARCHAR 20) - Standard (e.g., "EURUSD")

**For enterprise aggregation:** Query `deals` table grouped by `symbol`

---

### Question 5: Active vs Dormant Logic ✅ CONFIRMED
**You said dormant = no activity in last 30 days**

**What counts as "activity"?**
- Last data received from EA?
- Last trade executed?
- Last snapshot created?

**Field to check:**
- `whitelisted_broker_usage.last_seen_at`? ✅ (most likely)
- `trading_accounts.updated_at`?
- `account_snapshots.created_at`?

**RECOMMENDATION:** Use `whitelisted_broker_usage.last_seen_at` (already tracking this)

---

### Question 6: Time Filter Implementation
**For regular users with 7-day limit, should we:**

**Option A:** Hide locked options completely
```html
<select>
    <option>Today</option>
    <option>7 Days</option>
    <!-- 30D, 90D, 180D not shown at all -->
</select>
```

**Option B:** Show but disable with tooltip
```html
<select>
    <option>Today</option>
    <option>7 Days</option>
    <option disabled>🔒 30 Days (Enterprise only)</option>
    <option disabled>🔒 90 Days (Enterprise only)</option>
    <option disabled>🔒 180 Days (Enterprise only)</option>
</select>
```

**Option C:** Show with upgrade modal on click
```html
<select>
    <option>Today</option>
    <option>7 Days</option>
    <option data-locked="true">30 Days</option>
    <!-- Clicking shows modal: "Unlock by asking your broker..." -->
</select>
```

**RECOMMENDATION:** Option C (creates desire, shows what they're missing)

---

### Question 7: Nginx Configuration Strategy
**For enterprise subdomain, should we:**

**Option A:** Separate server block (recommended)
```nginx
server {
    server_name enterprise.thetradevisor.com;
    root /var/www/thetradevisor.com/public;
    # ... same PHP-FPM config
}
```

**Option B:** Reuse existing server block with multiple server_name
```nginx
server {
    server_name thetradevisor.com enterprise.thetradevisor.com;
    # Laravel handles routing
}
```

**RECOMMENDATION:** Option A (cleaner, separate logs, easier SSL management)

---

### Question 8: SSL Certificate Setup
**For Let's Encrypt, should we:**

**Option A:** Expand existing certificate
```bash
certbot certonly --expand \
  -d thetradevisor.com \
  -d www.thetradevisor.com \
  -d enterprise.thetradevisor.com
```

**Option B:** Separate certificate
```bash
certbot certonly -d enterprise.thetradevisor.com
```

**RECOMMENDATION:** Option A (simpler renewal, one cert for all)

---

## Database Schema Additions Needed

### 1. Remove Subscription Fields
```sql
-- Migration: remove_subscription_fields_from_users
ALTER TABLE users 
    DROP COLUMN IF EXISTS subscription_tier,
    DROP COLUMN IF EXISTS max_accounts;
```

### 2. Add Enterprise Admin Flag
```sql
-- Migration: add_enterprise_admin_to_users
ALTER TABLE users 
    ADD COLUMN is_enterprise_admin BOOLEAN DEFAULT FALSE;

CREATE INDEX idx_users_enterprise_admin ON users(is_enterprise_admin);
```

### 3. Create Enterprise API Keys Table
```sql
-- Migration: create_enterprise_api_keys_table
CREATE TABLE enterprise_api_keys (
    id BIGSERIAL PRIMARY KEY,
    enterprise_broker_id BIGINT NOT NULL REFERENCES enterprise_brokers(id) ON DELETE CASCADE,
    key VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    last_used_at TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_enterprise_api_keys_broker ON enterprise_api_keys(enterprise_broker_id);
CREATE INDEX idx_enterprise_api_keys_key ON enterprise_api_keys(key);
```

### 4. Country Fields Already Exist ✅
**No migration needed** - `trading_accounts` already has:
- `country_code` (VARCHAR 2, indexed)
- `country_name` (VARCHAR 100)
- `last_seen_at` (TIMESTAMP)

---

## Implementation Checklist (Detailed)

### Phase 1: Database Migrations ✅ READY TO START

**Step 1.1: Create migration files**
```bash
php artisan make:migration remove_subscription_fields_from_users
php artisan make:migration add_enterprise_admin_to_users
php artisan make:migration create_enterprise_api_keys_table
```

**Step 1.2: Write migration code**
- Remove subscription_tier, max_accounts
- Add is_enterprise_admin
- Create enterprise_api_keys table

**Step 1.3: Test on dev**
```bash
php artisan migrate --pretend  # Dry run
php artisan migrate            # Actual run
```

**Step 1.4: Verify data integrity**
```sql
SELECT COUNT(*) FROM users WHERE subscription_tier IS NOT NULL; -- Should error
SELECT COUNT(*) FROM users WHERE is_enterprise_admin = true;    -- Should be 0
SELECT * FROM enterprise_api_keys;                              -- Should be empty
```

**Step 1.5: Update models**
- Remove subscription logic from User model
- Add is_enterprise_admin to fillable
- Create EnterpriseApiKey model

---

### Phase 2: API Changes ✅ READY AFTER PHASE 1

**Step 2.1: Remove account limits**
- Edit DataCollectionController.php
- Remove lines 124-147 (account limit check)
- Keep broker whitelist logic
- Test: Create 10+ accounts, should work

**Step 2.2: Add time restriction helpers**
- Add to TradingAccount model:
  - `getMaxDaysView()` method
  - `isEnterpriseWhitelisted()` method
- Test: Standard account returns 7, enterprise returns 180

**Step 2.3: Update API responses**
- Add `max_days_view` field to DataCollectionController response
- Test: API returns correct value

**Step 2.4: Add time restrictions to analytics queries**
- Modify all controllers that fetch historical data
- Add `->where('created_at', '>=', now()->subDays($maxDays))`
- Test: Standard account can't see 30-day data

**Step 2.5: Create enterprise API controller**
- Create `app/Http/Controllers/Api/EnterpriseApiController.php`
- Implement all 6 endpoints
- Test each endpoint

**Step 2.6: Create enterprise API middleware**
- Create `app/Http/Middleware/EnterpriseApiAuth.php`
- Validate `ent_` prefix API keys
- Check enterprise_api_keys table
- Update last_used_at
- Test: Invalid key = 401, valid key = 200

---

### Phase 3: Frontend Changes ✅ READY AFTER PHASE 2

**Step 3.1: Update main site time filters**
- Add time filter dropdowns to all views
- Lock 30D/90D/180D for standard accounts
- Show upgrade modal on locked option click
- Test: Standard user sees lock, enterprise user doesn't

**Step 3.2: Create enterprise subdomain routes**
- Add subdomain routing to routes/web.php
- Create enterprise middleware
- Test: Route resolves correctly

**Step 3.3: Create enterprise controllers**
- DashboardController
- PerformanceController
- SnapshotsController
- AnalyticsController
- Test: Each controller returns data

**Step 3.4: Create enterprise views**
- dashboard.blade.php
- performance.blade.php
- snapshots.blade.php
- analytics.blade.php
- layouts/app.blade.php (enterprise theme)
- Test: Views render correctly

**Step 3.5: Implement filters**
- Time range filter
- Platform filter (MT4/MT5)
- Country filter
- Status filter (Active/Dormant)
- Symbol filter
- Test: Filters work, data updates

**Step 3.6: Implement charts**
- Equity curve chart
- Profit by symbol chart
- Profit by country chart
- Activity heatmap
- Test: Charts display correctly

**Step 3.7: Create admin broker management**
- Create admin routes
- Create BrokerManagementController
- Create admin views (index, create, edit)
- Add API key generation
- Test: Can create/edit/delete brokers

---

### Phase 4: Infrastructure Setup ✅ READY AFTER PHASE 3

**Step 4.1: SSL Certificate**
```bash
sudo certbot certonly --expand \
  -d thetradevisor.com \
  -d www.thetradevisor.com \
  -d enterprise.thetradevisor.com
```

**Step 4.2: Nginx Configuration**
```bash
sudo nano /etc/nginx/sites-available/thetradevisor.com
# Add enterprise subdomain server block
sudo nginx -t
sudo systemctl reload nginx
```

**Step 4.3: Test subdomain**
```bash
curl -I https://enterprise.thetradevisor.com
# Should return 200 OK
```

---

## Timeline Estimate

### Conservative Estimate
- Phase 1 (Database): 1 day
- Phase 2 (API): 3-4 days
- Phase 3 (Frontend): 4-5 days
- Testing & Fixes: 2-3 days
- **Total: 10-13 days**

### Aggressive Estimate
- Phase 1: 4 hours
- Phase 2: 2 days
- Phase 3: 3 days
- Testing: 1 day
- **Total: 6-7 days**

---

## Risk Assessment

### High Risk
- ❌ Data loss during migration
- ❌ Breaking existing user accounts
- ❌ API downtime affecting live accounts

### Medium Risk
- ⚠️ Performance degradation with 180-day queries
- ⚠️ Storage costs exceeding budget
- ⚠️ Subdomain DNS/SSL issues

### Low Risk
- ✅ UI/UX confusion (can iterate)
- ✅ Missing edge cases (can patch)
- ✅ Documentation gaps (can update)

---

## ✅ READY TO START - Final Confirmation

### All Questions Answered ✅
1. ✅ Enterprise API keys: Separate table with `ent_` prefix
2. ✅ Admin panel location: Main site `/admin/brokers`
3. ✅ Country detection: `trading_accounts.country_code` (already exists)
4. ✅ Symbol data: `deals` table with `symbol` column (already exists)
5. ✅ Active/dormant: `whitelisted_broker_usage.last_seen_at` < 30 days
6. ✅ Time filters: Show locked options with upgrade modal (Option C)
7. ✅ Nginx: Separate server block for enterprise subdomain
8. ✅ SSL: Expand existing cert with `--expand` flag

### Database Structure Verified ✅
- ✅ `enterprise_brokers` table exists
- ✅ `whitelisted_broker_usage` table exists
- ✅ `trading_accounts.country_code` exists
- ✅ `trading_accounts.last_seen_at` exists
- ✅ `deals.symbol` exists (indexed)
- ✅ `positions.symbol` exists (indexed)

### Implementation Order Confirmed ✅
1. **Phase 1: Database** (3 migrations)
   - Remove subscription fields
   - Add enterprise admin flag
   - Create enterprise API keys table

2. **Phase 2: API** (6 steps)
   - Remove account limits
   - Add time restrictions
   - Create enterprise endpoints
   - Add enterprise middleware

3. **Phase 3: Frontend** (7 steps)
   - Update main site time filters
   - Create enterprise subdomain
   - Create enterprise views
   - Create admin broker management

4. **Phase 4: Infrastructure** (3 steps)
   - SSL certificate
   - Nginx configuration
   - Testing

### Next Action
**Awaiting your approval to start Phase 1: Database Migrations**

Once you say "go", I will:
1. Create 3 migration files
2. Write migration code
3. Test with `--pretend` flag
4. Run migrations on dev
5. Verify data integrity
6. Update models
7. Show you proof of success

**Estimated time for Phase 1:** 2-3 hours

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
