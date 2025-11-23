# Documentation Plan: Public Profiles & Leaderboard Feature

**Date:** November 23, 2025  
**Status:** Planning Phase  
**Total Existing Docs:** 195 markdown files

---

## 🎯 What We Built Today

### Phase 7: Top Traders Leaderboard (COMPLETED)
- Public leaderboard at `/top-traders`
- Aggregated stats across all trader accounts
- Expandable sub-table showing individual accounts per trader
- Filtering by: Total Profit, ROI, Win Rate, Profit Factor
- Top 50 traders displayed
- Guest-accessible (no login required)
- Proper navigation integration

### Public Profile Enhancements (COMPLETED)
- Fixed toggle switches → dropdown for visibility
- Fixed account URL generation
- Fixed raw Laravel code display issues
- Optimized equity curve (1 snapshot per day vs thousands)
- Symbol table with normalized names + hover to show raw
- Unified footer integration
- Guest CTA (hidden for authenticated users)
- Proper MT4/MT5 deals query using symbol_mappings table

---

## 📚 Documentation Structure Plan

### 1. **Main Feature Documentation**
**Location:** `/www/docs/features/PUBLIC_PROFILES_IMPLEMENTATION.md` (UPDATE EXISTING)

**Changes Needed:**
- ✅ Mark Phase 7 as COMPLETED
- Add leaderboard implementation details
- Add optimization notes (equity curve, symbol mapping)
- Update route list with `/top-traders`
- Add troubleshooting section for common issues
- Document Cloudflare caching considerations

**Sections to Add:**
```markdown
## Phase 7: Top Traders Leaderboard ✅ COMPLETED

### Features
- Public leaderboard showing top 50 traders
- Aggregated stats across all public accounts per trader
- Expandable rows to view individual account breakdown
- Filter tabs: Total Profit, ROI, Win Rate, Profit Factor
- Guest-accessible with simple navigation
- Responsive design with proper table styling

### Implementation Details
- Controller: `PublicProfileController@leaderboard()`
- View: `resources/views/leaderboard/index.blade.php`
- Route: `GET /top-traders` (public, no auth)
- Data: Last 30 days performance
- Caching: 15-minute cache per profile via ProfileDataAggregatorService

### Technical Notes
- Uses deals table with `entry='out'` for both MT4 and MT5
- Aggregates profit, trades, win rate, profit factor across accounts
- ROI calculated based on total initial balance
- Alpine.js for expandable rows with indexed state management

### Optimizations Applied
- Equity curve: 1 snapshot per day (30 points vs thousands)
- Symbol mapping: JOIN to symbol_mappings table
- Proper caching strategy with manual cache clearing capability
```

---

### 2. **User Guide Documentation**
**Location:** `/www/docs/guides/PUBLIC_PROFILES_USER_GUIDE.md` (NEW FILE)

**Content:**
```markdown
# Public Profiles & Leaderboard User Guide

## For Traders: Making Your Profile Public

### Step 1: Enable Public Profile
1. Go to Profile Settings
2. Set "Show on Leaderboard" to Yes
3. Choose display mode: Username, Display Name, or Anonymous

### Step 2: Configure Account Visibility
1. Navigate to Accounts → Public Profiles
2. For each account:
   - Select "Public" or "Private" from dropdown
   - Set account slug (URL-friendly name)
   - Choose widget preset
   - Configure what to show (symbols, recent trades)

### Step 3: Share Your Profile
- Your profile URL: `https://thetradevisor.com/@username/account-slug/account-number`
- Share on social media
- Embed in your website (future feature)

## Appearing on the Leaderboard

### Requirements
- At least one public account
- "Show on Leaderboard" enabled in profile settings
- Trading activity in last 30 days

### How Rankings Work
- **Total Profit:** Sum of all profits across public accounts
- **ROI:** Total profit / total initial balance
- **Win Rate:** Weighted average across all accounts
- **Profit Factor:** Aggregated gross profit / gross loss

### Privacy Options
- **Username Mode:** Shows @username
- **Display Name Mode:** Shows custom name
- **Anonymous Mode:** Shows "Anonymous Trader"

## For Visitors: Viewing Profiles

### Accessing Public Profiles
- Browse leaderboard: `/top-traders`
- Filter by ranking criteria
- Click trader to expand and see individual accounts
- Click "View Profile" to see detailed stats

### What You Can See
- Performance metrics (30-day window)
- Equity curve chart
- Top symbols traded
- Win rate and profit factor
- Account details (if trader chose to show)
```

---

### 3. **API Documentation**
**Location:** `/www/docs/api/PUBLIC_PROFILES_API.md` (NEW FILE)

**Content:**
```markdown
# Public Profiles API Documentation

## Endpoints

### GET /@{username}/{slug}/{account}
View public profile for specific account

**Parameters:**
- `username`: Public username (alphanumeric + underscore)
- `slug`: Account slug (lowercase alphanumeric + hyphens)
- `account`: Account number (numeric)

**Response:** HTML page with profile data

**Caching:** 15 minutes via ProfileDataAggregatorService

---

### GET /top-traders
View leaderboard of top traders

**Query Parameters:**
- `rank_by` (optional): `total_profit`, `roi`, `win_rate`, `profit_factor`
  - Default: `total_profit`

**Response:** HTML page with leaderboard

**Data Window:** Last 30 days

**Limit:** Top 50 traders

---

## Data Structures

### Profile Data
```php
[
    'user' => User,
    'account' => TradingAccount,
    'profile' => PublicProfileAccount,
    'badges' => array,
    'stats' => [
        'total_profit' => float,
        'total_trades' => int,
        'win_rate' => float,
        'roi' => float,
        'profit_factor' => float,
        'currency' => string
    ],
    'equity_curve' => array,  // 30 data points (daily)
    'symbol_performance' => array,
    'recent_trades' => array  // if enabled
]
```

### Leaderboard Data
```php
[
    'user' => User,
    'stats' => [
        'total_profit' => float,  // aggregated
        'total_trades' => int,    // aggregated
        'win_rate' => float,      // weighted average
        'roi' => float,           // calculated from total initial balance
        'profit_factor' => float  // aggregated
    ],
    'accounts' => [
        [
            'profile' => PublicProfileAccount,
            'account' => TradingAccount,
            'stats' => array  // individual account stats
        ]
    ],
    'account_count' => int
]
```
```

---

### 4. **Technical Documentation**
**Location:** `/www/docs/technical/PUBLIC_PROFILES_ARCHITECTURE.md` (NEW FILE)

**Content:**
```markdown
# Public Profiles Technical Architecture

## Database Schema

### Tables Used
- `users` - User accounts with public_username, public_display_mode
- `trading_accounts` - Trading accounts
- `public_profile_accounts` - Public profile settings per account
- `deals` - Trade data (MT4 and MT5)
- `account_snapshots` - Equity snapshots
- `symbol_mappings` - Symbol normalization

### Key Relationships
```sql
users (1) → (many) public_profile_accounts
public_profile_accounts (1) → (1) trading_accounts
trading_accounts (1) → (many) deals
trading_accounts (1) → (many) account_snapshots
```

## Service Layer

### ProfileDataAggregatorService
**Purpose:** Aggregate and cache profile data

**Methods:**
- `getProfileData()` - Main data aggregation (15-min cache)
- `getStats()` - Calculate performance metrics
- `getEquityCurve()` - Daily snapshots (optimized)
- `getSymbolPerformance()` - Top 10 symbols with mapping
- `clearCache()` - Manual cache invalidation

**Caching Strategy:**
- Key: `public_profile_{profile_id}`
- TTL: 900 seconds (15 minutes)
- Storage: Redis (production)

### Optimization Techniques

#### 1. Equity Curve Optimization
**Problem:** Fetching all snapshots = thousands of rows
**Solution:** Get last snapshot of each day
```sql
SELECT DATE(snapshot_time) as date, MAX(snapshot_time) as last_snapshot_time
FROM account_snapshots
WHERE trading_account_id = ?
GROUP BY DATE(snapshot_time)
```
**Result:** 30 data points instead of thousands

#### 2. Symbol Normalization
**Problem:** Raw symbols have broker suffixes (XAUUSD.sd)
**Solution:** JOIN to symbol_mappings table
```sql
LEFT JOIN symbol_mappings ON deals.symbol = symbol_mappings.raw_symbol
SELECT COALESCE(symbol_mappings.normalized_symbol, deals.symbol) as normalized_symbol
```
**Result:** Clean symbol names with hover to show raw

#### 3. Leaderboard Aggregation
**Problem:** Multiple accounts per trader
**Solution:** Aggregate stats across all public accounts
- Sum profits and trades
- Weighted average for win rate
- Reconstruct profit factor from aggregated gross profit/loss
- Calculate ROI from total initial balance

## Frontend Components

### Alpine.js Components
- Expandable leaderboard rows (indexed state management)
- Symbol hover effect (text swap on mouseover/mouseout)

### Styling
- Tailwind CSS for all components
- Consistent table styling across site
- Fixed column widths to prevent "dancing" tables
- Responsive design for mobile

## Routes

### Public Routes (no auth)
```php
GET /@{username}/{slug}/{account} → PublicProfileController@show
GET /top-traders → PublicProfileController@leaderboard
```

### Authenticated Routes
```php
GET /accounts/public-profiles → PublicProfileController@manageAccounts
POST /accounts/{account}/public-profile → PublicProfileController@updateAccountProfile
```

## Performance Considerations

### Caching Layers
1. **Application Cache:** ProfileDataAggregatorService (15 min)
2. **View Cache:** Laravel compiled views
3. **OPcache:** PHP opcode cache
4. **Cloudflare:** HTML page cache (CDN)

### Cache Invalidation
```php
// Manual cache clear
php artisan cache:forget public_profile_{id}

// Clear all caches
php artisan optimize:clear
```

### Database Queries
- All queries use indexes on `trading_account_id`, `entry`, `type`, `time`
- Symbol mapping uses index on `raw_symbol`
- Snapshots grouped by date for efficiency

## Security

### Public Data Exposure
- Only users with `show_on_leaderboard = true` appear
- Only accounts with `is_public = true` are visible
- No sensitive data exposed (account credentials, API keys, etc.)
- Profile URLs are predictable but require opt-in

### Rate Limiting
- Standard Laravel rate limiting applies
- Cloudflare provides DDoS protection
- No special rate limits needed (cached data)

## Troubleshooting

### Common Issues

**Issue:** Old data showing after update
**Solution:** Clear profile cache
```bash
php artisan cache:forget public_profile_{id}
php artisan view:clear
```

**Issue:** Cloudflare serving old HTML
**Solution:** Purge Cloudflare cache or wait for TTL

**Issue:** Symbols showing raw names instead of normalized
**Solution:** Check symbol_mappings table has entries
```sql
SELECT * FROM symbol_mappings WHERE raw_symbol = 'XAUUSD.sd';
```

**Issue:** Leaderboard showing 0 for all stats
**Solution:** Check platform_type column exists and is populated
```sql
SELECT platform_type FROM trading_accounts WHERE id = ?;
```
```

---

### 5. **FAQ Updates**
**Location:** `/www/docs/FAQ.md` (UPDATE EXISTING)

**New Sections to Add:**
```markdown
## Public Profiles & Leaderboard

### Q: How do I make my trading profile public?
A: Go to Profile Settings and enable "Show on Leaderboard", then go to Accounts → Public Profiles and set individual accounts to "Public".

### Q: Can I choose what information to show on my public profile?
A: Yes! For each account you can configure:
- Widget preset (Minimal, Balanced, or Maximum Transparency)
- Show/hide symbol performance
- Show/hide recent trades timeline

### Q: How do I appear on the leaderboard?
A: Enable "Show on Leaderboard" in your profile settings and make at least one trading account public. You must have trading activity in the last 30 days.

### Q: Can I be anonymous on the leaderboard?
A: Yes! Set your public display mode to "Anonymous" and you'll appear as "Anonymous Trader" on the leaderboard.

### Q: How are leaderboard rankings calculated?
A: Rankings aggregate stats across all your public accounts:
- **Total Profit:** Sum of all profits
- **ROI:** Total profit / total initial balance
- **Win Rate:** Weighted average based on trade count
- **Profit Factor:** Aggregated gross profit / gross loss

### Q: Why do I see different symbol names when I hover?
A: We show normalized symbol names by default (e.g., "XAUUSD") and the raw broker-specific name on hover (e.g., "XAUUSD.sd").

### Q: How often is the leaderboard updated?
A: Profile data is cached for 15 minutes. Your stats will update automatically within this timeframe.

### Q: Can I remove my profile from the leaderboard?
A: Yes, simply disable "Show on Leaderboard" in your profile settings or set all accounts to "Private".

### Q: What time period does the leaderboard show?
A: The leaderboard displays performance from the last 30 days only.

### Q: Can I share my public profile on social media?
A: Yes! Your profile URL is: `https://thetradevisor.com/@username/account-slug/account-number`
We've included Open Graph and Twitter Card meta tags for rich previews.
```

---

### 6. **Changelog Entry**
**Location:** `/www/docs/changelog/2025-11-23-PUBLIC-PROFILES-PHASE-7.md` (NEW FILE)

**Content:**
```markdown
# Changelog: Public Profiles Phase 7 - Top Traders Leaderboard

**Date:** November 23, 2025  
**Version:** 2.7.0  
**Type:** Feature Release

## 🎉 New Features

### Top Traders Leaderboard
- Public leaderboard at `/top-traders` showing top 50 traders
- Filter by Total Profit, ROI, Win Rate, or Profit Factor
- Expandable rows to view individual account breakdown
- Guest-accessible with simple navigation
- Responsive design for mobile and desktop

### Public Profile Enhancements
- Optimized equity curve (30 data points vs thousands)
- Symbol table with normalized names + hover for raw names
- Fixed column widths to prevent table "dancing"
- Unified footer across all public pages
- Guest CTA (hidden for authenticated users)

## 🐛 Bug Fixes
- Fixed toggle switches → replaced with dropdown for better UX
- Fixed account URL generation using dynamic account numbers
- Fixed raw Laravel code display in views
- Fixed MT4/MT5 deals query to use proper symbol_mappings table
- Fixed ROI calculation for aggregated accounts
- Fixed Alpine.js state management for expandable rows

## ⚡ Performance Improvements
- Equity curve: Reduced from thousands to 30 data points per profile
- Symbol mapping: Proper JOIN instead of N+1 queries
- Profile data caching: 15-minute cache per profile
- Database query optimization for leaderboard aggregation

## 🔧 Technical Changes
- Added `ProfileDataAggregatorService` optimizations
- Added proper symbol normalization via `symbol_mappings` table
- Added leaderboard route and controller method
- Added navigation links for leaderboard
- Updated public profile view with better table styling

## 📝 Documentation
- Updated PUBLIC_PROFILES_IMPLEMENTATION.md
- Added user guide for public profiles
- Added API documentation
- Added technical architecture docs
- Updated FAQ with leaderboard questions

## 🚀 Deployment Notes
- Run migrations: N/A (no schema changes)
- Clear caches: `php artisan optimize:clear`
- Restart services: `systemctl restart php8.3-fpm`
- Cloudflare: May need cache purge for immediate effect

## ⚠️ Breaking Changes
None

## 🔮 Coming Next
- Phase 8: Widget Presets Implementation
- Phase 9: Additional Widgets
- OG Image Generation
- Profile verification badges
```

---

### 7. **README Updates**
**Location:** `/www/README.md` (UPDATE EXISTING)

**Section to Add:**
```markdown
## 🏆 Public Profiles & Leaderboard

TheTradeVisor now features public trading profiles and a competitive leaderboard system.

### Features
- **Public Profiles:** Share your trading performance with custom URLs
- **Top Traders Leaderboard:** Compete with other traders globally
- **Privacy Controls:** Choose what to show and how to appear
- **Real-time Stats:** 30-day performance window with 15-minute cache

### Quick Links
- [User Guide](docs/guides/PUBLIC_PROFILES_USER_GUIDE.md)
- [API Documentation](docs/api/PUBLIC_PROFILES_API.md)
- [Technical Architecture](docs/technical/PUBLIC_PROFILES_ARCHITECTURE.md)
- [Implementation Details](docs/features/PUBLIC_PROFILES_IMPLEMENTATION.md)
```

---

## 📋 Implementation Checklist for Tomorrow

### Phase 1: Core Documentation (2-3 hours)
- [ ] Update `/docs/features/PUBLIC_PROFILES_IMPLEMENTATION.md`
- [ ] Create `/docs/guides/PUBLIC_PROFILES_USER_GUIDE.md`
- [ ] Create `/docs/api/PUBLIC_PROFILES_API.md`
- [ ] Create `/docs/technical/PUBLIC_PROFILES_ARCHITECTURE.md`

### Phase 2: Supporting Documentation (1-2 hours)
- [ ] Create `/docs/changelog/2025-11-23-PUBLIC-PROFILES-PHASE-7.md`
- [ ] Update `/docs/FAQ.md` with new Q&A
- [ ] Update `/www/README.md` with feature overview
- [ ] Update `/docs/getting-started/QUICK_START.md` if needed

### Phase 3: Integration & Cross-References (1 hour)
- [ ] Add cross-references between related docs
- [ ] Update navigation/index files if they exist
- [ ] Ensure all internal links work
- [ ] Add "See Also" sections

### Phase 4: Review & Polish (30 minutes)
- [ ] Spell check all new documentation
- [ ] Verify code examples are correct
- [ ] Ensure consistent formatting
- [ ] Add diagrams if helpful

---

## 🎨 Documentation Style Guide

### Formatting Standards
- Use proper markdown headers (# ## ###)
- Code blocks with language specification
- Tables for structured data
- Bullet points for lists
- Bold for emphasis, italic for terms

### Code Examples
- Always include language identifier
- Show complete, runnable examples
- Include comments for clarity
- Use realistic data in examples

### Cross-References
- Use relative paths for internal links
- Always verify links work
- Add "See Also" sections at end
- Link to related features

---

## 📊 Metrics to Document

### Performance Metrics
- Cache hit rate
- Average page load time
- Database query count per page
- Memory usage

### Usage Metrics
- Number of public profiles
- Leaderboard views per day
- Profile views per day
- Most popular ranking filter

---

## 🔍 Areas Needing Special Attention

### Known Issues to Document
1. Cloudflare cache can serve stale HTML (15-60 min TTL)
2. Symbol mappings table needs population for new brokers
3. Profile cache must be manually cleared after updates
4. Alpine.js expandable rows need proper state management

### Common Pitfalls
1. Forgetting to enable "Show on Leaderboard"
2. Setting account to public but no trades in 30 days
3. Expecting instant updates (15-min cache)
4. Not understanding aggregated vs individual stats

### Future Enhancements to Note
- Widget presets (Phase 8)
- OG image generation
- Profile verification badges
- Embeddable widgets
- Historical leaderboard snapshots

---

## 📅 Tomorrow's Schedule

1. **Morning (9am-12pm):** Core documentation writing
2. **Afternoon (1pm-3pm):** Supporting docs and updates
3. **Late Afternoon (3pm-5pm):** Integration, review, polish
4. **Evening:** Final review and commit

---

**END OF PLAN**
