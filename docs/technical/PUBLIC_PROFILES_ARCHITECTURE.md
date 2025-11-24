# Public Profiles Technical Architecture

**Last Updated:** November 24, 2025  
**Version:** 2.7.0  
**Status:** Production

---

## 📖 Table of Contents

1. [System Overview](#system-overview)
2. [Database Schema](#database-schema)
3. [Service Layer](#service-layer)
4. [Optimization Techniques](#optimization-techniques)
5. [Caching Strategy](#caching-strategy)
6. [Security](#security)
7. [Performance](#performance)
8. [Troubleshooting](#troubleshooting)

---

## System Overview

### Architecture Pattern

Public Profiles follows a **Service-Repository-Controller** pattern with caching.

### Technology Stack

| Component | Technology |
|-----------|-----------|
| Backend | Laravel 11.x |
| Database | PostgreSQL 16 |
| Cache | Redis |
| Frontend | Blade + Alpine.js |
| Styling | Tailwind CSS |
| Charts | Chart.js |
| Server | Nginx + PHP-FPM 8.3 |
| CDN | Cloudflare |

---

## Database Schema

### Key Tables

#### users
```sql
- id, name, email
- public_username (unique, indexed)
- display_name
- public_display_mode (username/display_name/anonymous)
- show_on_leaderboard (boolean, indexed)
```

#### trading_accounts
```sql
- id, user_id, account_number
- broker, platform_type (MT4/MT5)
- currency, initial_balance
```

#### public_profile_accounts
```sql
- id, trading_account_id (unique)
- slug (indexed)
- is_public (boolean, indexed)
- widget_preset (minimal/balanced/maximum)
- show_symbols, show_recent_trades
```

#### deals
```sql
- id, trading_account_id, ticket, position_id
- symbol, type, entry (in/out/inout)
- volume, profit, commission, swap, time
- Indexes: trading_account_id, entry, type, time, symbol
```

#### account_snapshots
```sql
- id, trading_account_id
- equity, balance, snapshot_time
- Indexes: trading_account_id, snapshot_time
```

#### symbol_mappings
```sql
- id, raw_symbol (unique, indexed)
- normalized_symbol (indexed)
- Example: XAUUSD.sd → XAUUSD
```

### Relationships

```
users → trading_accounts → public_profile_accounts
                         → deals
                         → account_snapshots
```

---

## Service Layer

### ProfileDataAggregatorService

**Location:** `app/Services/ProfileDataAggregatorService.php`

#### Main Method: getProfileData()

```php
public function getProfileData(PublicProfileAccount $profile): array
{
    return Cache::remember("public_profile_{$profile->id}", 900, function () use ($profile) {
        return [
            'user' => $this->getUserData($profile),
            'account' => $profile->tradingAccount,
            'profile' => $profile,
            'badges' => $this->getBadges($profile),
            'stats' => $this->getStats($profile),
            'equity_curve' => $this->getEquityCurve($profile),
            'symbol_performance' => $this->getSymbolPerformance($profile),
            'recent_trades' => $this->getRecentTrades($profile),
        ];
    });
}
```

**Cache:** 15 minutes per profile

#### Stats Calculation

```php
public function getStats(PublicProfileAccount $profile): array
{
    $deals = Deal::where('trading_account_id', $account->id)
        ->where('entry', 'out')  // Closed trades only
        ->whereIn('type', ['buy', 'sell'])
        ->where('time', '>=', now()->subDays(30))
        ->get();
    
    // Calculate: profit, trades, win rate, ROI, profit factor
    return [
        'total_profit' => $deals->sum('profit'),
        'total_trades' => $deals->count(),
        'win_rate' => ($winningTrades / $totalTrades) * 100,
        'roi' => ($totalProfit / $initialBalance) * 100,
        'profit_factor' => $grossProfit / $grossLoss,
    ];
}
```

---

## Optimization Techniques

### 1. Equity Curve Optimization

**Problem:** Thousands of snapshots per account

**Solution:** Get last snapshot of each day

```sql
SELECT DATE(snapshot_time) as date, MAX(snapshot_time) as last_time
FROM account_snapshots
WHERE trading_account_id = ?
  AND snapshot_time >= NOW() - INTERVAL '30 days'
GROUP BY DATE(snapshot_time);
```

**Result:** 30 data points instead of thousands (~95% reduction)

---

### 2. Symbol Normalization

**Problem:** Raw symbols have broker suffixes (XAUUSD.sd)

**Solution:** JOIN to symbol_mappings

```sql
SELECT 
    deals.symbol as raw_symbol,
    COALESCE(symbol_mappings.normalized_symbol, deals.symbol) as normalized,
    COUNT(*) as trades,
    SUM(profit) as profit
FROM deals
LEFT JOIN symbol_mappings ON deals.symbol = symbol_mappings.raw_symbol
WHERE trading_account_id = ?
GROUP BY deals.symbol, symbol_mappings.normalized_symbol;
```

**Result:** Clean names (XAUUSD) with hover to show raw (XAUUSD.sd)

---

### 3. Leaderboard Aggregation

**Challenge:** Multiple accounts per trader

**Solution:** Aggregate across all public accounts

```php
// Sum profits and trades
$totalProfit = $accounts->sum(fn($acc) => $acc->deals->sum('profit'));
$totalTrades = $accounts->sum(fn($acc) => $acc->deals->count());

// Weighted average for win rate
$winRate = ($totalWinningTrades / $totalTrades) * 100;

// ROI from total initial balance
$roi = ($totalProfit / $totalInitialBalance) * 100;

// Reconstruct profit factor
$profitFactor = $totalGrossProfit / $totalGrossLoss;
```

---

### 4. Database Indexing

**Critical Indexes:**
```sql
-- Deals queries
CREATE INDEX idx_deals_account_entry_type_time 
ON deals(trading_account_id, entry, type, time);

-- Snapshots queries
CREATE INDEX idx_snapshots_account_time 
ON account_snapshots(trading_account_id, snapshot_time);

-- Symbol mapping
CREATE INDEX idx_symbol_mappings_raw 
ON symbol_mappings(raw_symbol);

-- Public profiles lookup
CREATE INDEX idx_public_profiles_public 
ON public_profile_accounts(is_public);
```

---

## Caching Strategy

### Multi-Layer Caching

```
Cloudflare CDN (60 min)
    ↓
Application Cache (15 min) ← Redis
    ↓
OPcache (PHP Opcode)
    ↓
Database (PostgreSQL)
```

### Cache Keys

**Format:** `public_profile_{profile_id}`

**TTL:** 900 seconds (15 minutes)

**Storage:** Redis (production), File (development)

### Cache Operations

```bash
# Clear specific profile
php artisan cache:forget public_profile_123

# Clear all application cache
php artisan cache:clear

# Clear all (config, routes, views, cache)
php artisan optimize:clear
```

### Automatic Invalidation

Cache is cleared when:
- Profile settings updated
- Account visibility changed
- TTL expires (15 minutes)

---

## Security

### Public vs Private Data

**Always Public (if profile is public):**
- Performance stats (30-day window)
- Equity curve (daily snapshots)
- Symbol performance (if enabled)
- Recent trades (if enabled)

**Never Public:**
- Email addresses
- Account credentials
- API keys
- Broker login details
- Real-time open positions
- Private accounts

### Access Control

Profile is visible only if:
1. `users.show_on_leaderboard = true`
2. `public_profile_accounts.is_public = true`
3. URL parameters match (username, slug, account)

### Rate Limiting

**Laravel:** 60 requests/minute per IP

**Cloudflare:** DDoS protection + bot detection

---

## Performance

### Target Metrics

| Metric | Target | Current |
|--------|--------|---------|
| Profile Load | < 500ms | ~350ms |
| Leaderboard | < 1s | ~750ms |
| Cache Hit Rate | > 90% | ~95% |
| DB Queries | < 10 | 3-5 |
| Memory | < 128MB | ~80MB |

### Query Optimization

**Profile Page:**
- 1 query: Get profile + account + user (eager loading)
- 1 query: Get deals (with indexes)
- 1 query: Get daily snapshots (optimized)
- 1 query: Get symbol performance (with JOIN)
- Total: 3-5 queries (cached for 15 min)

**Leaderboard:**
- 1 query: Get users with public profiles
- N queries: Get deals per user (could be optimized further)
- Cached per profile for 15 minutes

---

## Troubleshooting

### Issue: Old Data After Update

**Symptoms:** Profile shows outdated stats

**Solution:**
```bash
php artisan cache:forget public_profile_123
php artisan view:clear
```

---

### Issue: Cloudflare Serving Stale HTML

**Symptoms:** Changes not visible after cache clear

**Solution:**
1. Wait 15-60 minutes for Cloudflare TTL
2. Or purge Cloudflare cache via dashboard
3. Or disable Cloudflare temporarily

**Check:**
```bash
curl -I https://thetradevisor.com/@user/slug/account
# Look for: CF-Cache-Status: HIT/MISS
```

---

### Issue: Symbols Showing Raw Names

**Symptoms:** XAUUSD.sd instead of XAUUSD

**Solution:**
```sql
-- Check symbol_mappings table
SELECT * FROM symbol_mappings WHERE raw_symbol = 'XAUUSD.sd';

-- If missing, add mapping
INSERT INTO symbol_mappings (raw_symbol, normalized_symbol)
VALUES ('XAUUSD.sd', 'XAUUSD');
```

---

### Issue: Leaderboard Showing 0 Stats

**Symptoms:** All traders show 0 profit/trades

**Possible Causes:**
1. No deals with `entry='out'`
2. `platform_type` column missing/null
3. Deals older than 30 days
4. Cache issue

**Solution:**
```sql
-- Check deals
SELECT COUNT(*) FROM deals 
WHERE trading_account_id = ? 
  AND entry = 'out' 
  AND type IN ('buy', 'sell')
  AND time >= NOW() - INTERVAL '30 days';

-- Check platform_type
SELECT id, platform_type FROM trading_accounts WHERE id = ?;
```

---

### Issue: Profile URL Returns 404

**Symptoms:** Public profile not accessible

**Checklist:**
1. ✅ `show_on_leaderboard = true` in users table
2. ✅ `is_public = true` in public_profile_accounts table
3. ✅ URL format correct: `/@username/slug/account-number`
4. ✅ Account number matches actual account
5. ✅ Slug matches profile slug

**Debug:**
```php
// In tinker
$user = User::where('public_username', 'john_trader')->first();
$account = TradingAccount::where('account_number', '12345678')->first();
$profile = PublicProfileAccount::where('trading_account_id', $account->id)->first();

// Check values
$user->show_on_leaderboard; // Should be true
$profile->is_public; // Should be true
$profile->slug; // Should match URL
```

---

## Frontend Components

### Alpine.js: Expandable Rows

```html
<div x-data="{ expanded: {} }">
    @foreach($leaderboard as $index => $trader)
    <div @click="expanded[{{ $index }}] = !expanded[{{ $index }}]">
        <!-- Main row -->
    </div>
    <div x-show="expanded[{{ $index }}]" x-collapse>
        <!-- Sub-table -->
    </div>
    @endforeach
</div>
```

### Alpine.js: Symbol Hover

```html
<span x-data="{ show: false, norm: 'XAUUSD', raw: 'XAUUSD.sd' }"
      @mouseenter="show = true"
      @mouseleave="show = false"
      x-text="show ? raw : norm">
</span>
```

### Chart.js: Equity Curve

```javascript
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($dates),
        datasets: [{
            data: @json($equity),
            borderColor: 'rgb(59, 130, 246)',
            fill: true
        }]
    }
});
```

---

## Routes

```php
// Public (no auth)
Route::get('/@{username}/{slug}/{account}', 'PublicProfileController@show');
Route::get('/top-traders', 'PublicProfileController@leaderboard');

// Authenticated
Route::middleware('auth')->group(function () {
    Route::get('/accounts/public-profiles', 'PublicProfileController@manageAccounts');
    Route::post('/accounts/{account}/public-profile', 'PublicProfileController@updateAccountProfile');
});
```

---

## Related Documentation

- [User Guide](../guides/PUBLIC_PROFILES_USER_GUIDE.md)
- [API Documentation](../api/PUBLIC_PROFILES_API.md)
- [Implementation Details](../features/PUBLIC_PROFILES_IMPLEMENTATION.md)
- [Changelog](../changelog/2025-11-23-PUBLIC-PROFILES-PHASE-7.md)

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
