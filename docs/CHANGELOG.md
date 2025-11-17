# Changelog

All notable changes to TheTradeVisor will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.4.0] - 2025-11-17

### 🚀 New Features

#### Account Limit Enforcement
- **Feature**: Prevent users from bypassing account limits with one API key
- **Implementation**: 
  - Controller-level check (DataCollectionController)
  - Job-level safety net (ProcessTradingData)
  - Clear error messages with upgrade URL
  - Comprehensive logging for monitoring
- **Impact**: Prevents abuse, enforces subscription tiers
- **Documentation**: Account limits now properly enforced

#### Redirect Authenticated Users
- **Feature**: Auto-redirect logged-in users from guest pages to dashboard
- **Implementation**: Custom RedirectIfAuthenticated middleware
- **Protected Routes**: `/login`, `/register`, `/forgot-password`
- **Impact**: Better UX, prevents confusion
- **Documentation**: Middleware registered as 'guest' alias

### 💰 Pricing Model Update

#### Subscription Tier Changes
- **Removed**: PRO tier completely eliminated
- **Updated**: Basic tier now "Pay-Per-Account" model
- **Pricing**: $9.99 one-time payment per additional account
- **Free Tier**: First account remains free forever
- **Files Updated**:
  - Controller validation (removed 'pro' from allowed tiers)
  - Admin views (removed PRO badge styling)
  - Pricing page (updated to show $9.99 one-time)
  - FAQ page (updated pricing information)
  - API key settings page (clarified account management)

### 🔧 Technical Improvements

#### Code Quality
- Comprehensive PHPUnit tests for account limits
- Automated testing for pricing validation
- Clean, maintainable middleware implementation

#### Security
- Account limit enforcement prevents abuse
- Proper authentication checks
- Clear error messages without exposing internals

---

## [1.2.0] - 2025-11-13

### 🔴 Critical Security Fixes

#### User Data Bleeding - RESOLVED ✅
- **Issue**: Users seeing other users' account data, dashboards, and trading history
- **Severity**: CRITICAL - Financial data exposure, GDPR violation
- **Root Cause**: Cloudflare caching HTML pages of authenticated users
- **Fix**: PreventPageCaching middleware + Cloudflare configuration
- **Status**: Completely resolved, tested with multiple users
- **Documentation**: `docs/USER_DATA_BLEEDING_FIX.md`

#### Session Stability Improvements
- **Issue**: Random logouts every 3-4 pages
- **Fix**: Proper session cookie configuration for HTTPS/load balancer
- **Settings**: `SESSION_SECURE_COOKIE=true`, `SESSION_SAME_SITE=lax`, `SESSION_HTTP_ONLY=true`
- **Status**: Resolved

### 🎨 UI/UX Improvements

#### Admin Trades Grouping
- **Feature**: Collapsible trade grouping by position_id
- **Benefits**: 
  - Groups IN and OUT deals together
  - Shows total profit for closed positions
  - Click to expand and see opening trade details
  - Compact icons (📊 = Open, ✅ = Closed, ▶ = Expandable)
  - Legend explaining symbols
  - Table format for expanded details
- **Impact**: Much cleaner, easier to understand trade lifecycle
- **Documentation**: `docs/ADMIN_TRADES_GROUPING.md`

### 🛡️ Security Enhancements

#### PreventPageCaching Middleware (NEW)
- **Purpose**: Prevent CDN/proxy caching of authenticated pages
- **Headers**: 
  - `Cache-Control: no-store, no-cache, must-revalidate, max-age=0, private`
  - `CDN-Cache-Control: no-store`
  - `Cloudflare-CDN-Cache-Control: no-store`
  - `Vary: Cookie`
- **Applied**: All authenticated requests
- **File**: `app/Http/Middleware/PreventPageCaching.php`

#### Cloudflare Configuration
- **Page Rules**: Bypass cache for authenticated pages
  - `/dashboard*` - Cache Level: Bypass
  - `/performance*` - Cache Level: Bypass
  - `/accounts/*` - Cache Level: Bypass
  - `/analytics*` - Cache Level: Bypass
- **Cookie Rule**: Bypass cache when `laravel_session` cookie present

### 🐛 Bug Fixes

#### Open Position Profit Display
- **Issue**: Open positions showing $0.00 profit
- **Fix**: Lookup floating profit from Position model
- **Handles**: Both MT4 (ticket) and MT5 (position_identifier)
- **Fallback**: Tries both lookup methods if platform_type is empty

### ⚠️ Known Issues (Temporary Workarounds)

#### 419 CSRF Errors
- **Status**: TEMPORARY WORKAROUND IN PLACE
- **Workaround**: CSRF validation disabled for `/login` and `/logout`
- **Priority**: HIGH - Must fix and re-enable CSRF protection
- **Next Steps**: Investigate Cloudflare/load balancer interaction

#### Dashboard Caching Disabled
- **Status**: DISABLED FOR DEBUGGING
- **Reason**: To isolate user bleeding issue (now resolved)
- **Next Steps**: Re-enable after 24h monitoring period
- **Impact**: Increased database load

#### Emergency Logging Active
- **Status**: TEMPORARY DEBUG LOGGING
- **Purpose**: Monitor user data isolation
- **Next Steps**: Remove after 24h of stable operation
- **Impact**: Larger log files

### 📝 Documentation

#### New Documentation Files
- `docs/USER_DATA_BLEEDING_FIX.md` - Complete analysis and fix
- `docs/ADMIN_TRADES_GROUPING.md` - Feature documentation
- `docs/PENDING_ISSUES.md` - List of issues to tackle next
- `docs/GITHUB_ISSUE_USER_DATA_BLEEDING.md` - GitHub issue template

### 🔧 Files Modified

#### New Files
- `app/Http/Middleware/PreventPageCaching.php`
- `resources/views/admin/trades/index_grouped_tbody.blade.php`
- `docs/USER_DATA_BLEEDING_FIX.md`
- `docs/ADMIN_TRADES_GROUPING.md`
- `docs/PENDING_ISSUES.md`
- `docs/GITHUB_ISSUE_USER_DATA_BLEEDING.md`

#### Modified Files
- `bootstrap/app.php` - Registered PreventPageCaching middleware, CSRF exclusions
- `app/Http/Controllers/Admin/TradesController.php` - Grouping logic, position lookup
- `resources/views/admin/trades/index.blade.php` - Added legend, included grouped tbody
- `.env` - Session cookie settings (not in repo)

### 🔄 Commits (November 13, 2025)

Security Fixes:
- `e5d572e` - CRITICAL SECURITY FIX: Prevent page caching for authenticated users
- `03b675b` - Re-enable CSRF protection - User bleeding issue resolved
- `a2645ad` - TEMPORARY: Disable CSRF on login/logout - Intermittent 419 errors

Trade Grouping Feature:
- `3aedfe0` - Implement collapsible grouped trades by position_id
- `a4d772f` - UI improvements for grouped trades view
- `b05740b` - Fix open position lookup - handle empty platform_type

### 📊 Impact Summary

#### Security:
- ✅ User data isolation restored
- ✅ No more cross-user data contamination
- ✅ GDPR/Privacy compliance maintained
- ✅ Financial data protected
- ⚠️ CSRF temporarily disabled (must fix)

#### Performance:
- ⚠️ Dashboard caching disabled (temporary)
- ✅ Trade grouping has no performance impact
- ✅ Same number of database queries

#### User Experience:
- ✅ Users see correct data
- ✅ No more random logouts
- ✅ Cleaner admin trades view
- ✅ Better trade lifecycle visibility

### 🎯 Next Steps (Priority Order)

1. **Monitor for 24 hours** - Ensure user bleeding fix is stable
2. **Fix 419 CSRF errors** - Re-enable CSRF protection (2-3 hours)
3. **Re-enable dashboard caching** - After monitoring period (30 min)
4. **Remove emergency logging** - After stability confirmed (15 min)
5. **Disable Cloudflare Dev Mode** - After page rules verified (15 min)
6. **Review session configuration** - Load balancer optimization (1-2 hours)

### 📚 References

- [Cloudflare Cache Documentation](https://developers.cloudflare.com/cache/)
- [Laravel Session Documentation](https://laravel.com/docs/11.x/session)
- [OWASP Session Management](https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html)

---

## [Unreleased] - 2025-11-12

### 🛡️ System Protection & Performance

#### Circuit Breakers
- **Automatic Protection**: Opens when CPU > 80% or Memory > 85%
- **Graceful Degradation**: Disables expensive operations under load
- **Auto-Recovery**: Closes after 5 minutes
- **User-Friendly**: Beautiful error page with system status

#### Comprehensive Rate Limiting
- **Analytics**: 10 requests/minute per user
- **Exports**: 5 exports/minute per user
- **Broker Analytics**: 20 requests/minute per user
- **HTTP 429**: Standard rate limit exceeded response

#### Query Optimization
- **Pagination Everywhere**: All queries use `->paginate()` or `->limit()`
- **Database Aggregation**: Statistics calculated in PostgreSQL
- **Chart Data Limits**: Maximum 5000 data points
- **Export Limits**: Maximum 10,000 records per export

#### Slow Query Logging
- **PostgreSQL**: Logs queries > 1 second
- **Laravel**: Logs queries > 1 second with SQL and bindings
- **Admin Panel**: View slow queries directly
- **Automated Extraction**: Cron job every 5 minutes

#### System Monitoring
- **Health Checks**: Every 2 minutes via cron
- **Metrics**: CPU, Memory, Disk I/O, PostgreSQL, PHP-FPM
- **Auto-Recovery**: Clears cache and restarts services under load
- **Alert System**: Slack/Email notifications for critical events

#### Storage Permissions
- **Group-Based**: Both www-data and tradeadmin in www-data group
- **SGID Bit**: New files inherit group ownership
- **Permissions**: 775 (rwxrwxr-x) on storage directories

#### Logging Improvements
- **Single Log File**: All logs to `laravel.log` (no date stamps)
- **Clean Logs**: Stack traces removed
- **Custom Formatter**: Smaller, readable log files

### 📚 Documentation

#### Complete Overhaul
- **Author Credits**: Added to all 93 .md files
- **Main README**: 24 shields.io badges
- **Navigation Hub**: Comprehensive docs/README.md
- **Installation Guide**: Step-by-step setup instructions
- **Protection Summary**: Current protection status
- **Monitoring Guide**: Latest monitoring features

### 🐛 Bug Fixes

#### System Stability
- **Fixed**: System crash on November 12 due to unbounded queries
- **Fixed**: 37 instances of `->get()` without limits
- **Fixed**: No query timeouts causing runaway queries
- **Fixed**: Permission denied errors on log files

### 🔧 Configuration Changes

#### Environment Variables
- `LOG_CHANNEL=single` - Single log file
- `CIRCUIT_BREAKER_ENABLED=true` - Enable circuit breakers
- `SLACK_WEBHOOK_URL` - Slack notifications (optional)

#### Database
- `statement_timeout = 30000` - 30 second query timeout
- `log_min_duration_statement = 1000` - Log slow queries

---

## [1.2.0] - 2025-11-11

### 🎯 Major Features Added

#### MT4/MT5 Position System
- **Platform Detection**: Automatic detection of MT4 vs MT5 platforms
- **Account Mode Detection**: Identifies Netting vs Hedging modes
- **Smart Position Aggregation**: Groups deals into positions correctly for MT5 Netting
- **Expandable Position Rows**: View individual deals within aggregated positions
- **Platform Badges**: Visual indicators showing platform type and mode throughout the application

#### Client-Side Filtering
- **Instant Filters**: Real-time position filtering without page reload
- **Multiple Filter Options**: 
  - Show Open Only
  - Show Closed Only
  - Show Profitable Only
  - Show Losses Only
- **Performance**: 50x faster than server-side filtering (~10ms vs ~500ms)
- **Clean URLs**: No GET parameters, filters work entirely client-side

### 🐛 Bug Fixes

#### Position Type Display
- **Fixed**: Dashboard was showing deal types instead of position types
- **Issue**: Closing a SELL position with a BUY deal showed as "BUY" (incorrect)
- **Solution**: Dashboard now shows actual position types (SELL shows as SELL)
- **Impact**: Eliminates confusion for traders viewing their positions

#### Platform Detection
- **Fixed**: Accounts showing "Platform not detected yet" despite having MT5 data
- **Solution**: Created `accounts:detect-platforms` command to detect from existing data
- **Detection Logic**: Analyzes deals table for position_id field (MT5 indicator)

### 📊 Database Changes

#### New Columns Added

**trading_accounts table:**
- `platform_type` (VARCHAR) - 'MT4' or 'MT5'
- `account_mode` (VARCHAR) - 'netting' or 'hedging'
- `platform_build` (INTEGER) - Platform build number
- `platform_detected_at` (TIMESTAMP) - Detection timestamp

**positions table:**
- `position_identifier` (VARCHAR) - MT5 position ID
- `entry_type` (VARCHAR) - Entry type
- `close_time` (TIMESTAMP) - Close timestamp
- `close_price` (DECIMAL) - Close price
- `total_volume_in` (DECIMAL) - Total volume entered
- `total_volume_out` (DECIMAL) - Total volume exited
- `deal_count` (INTEGER) - Number of deals in position
- `platform_type` (VARCHAR) - Platform type

**deals table:**
- `platform_type` (VARCHAR) - Platform type

#### Migrations
- `2025_11_11_055100_add_platform_detection_to_trading_accounts.php`
- `2025_11_11_055200_enhance_positions_for_aggregation.php`
- `2025_11_11_055300_add_platform_info_to_deals.php`

### 🎨 UI/UX Improvements

#### Platform Badges
- **Dashboard**: Badges next to account numbers in accounts table
- **Accounts Page**: Badges in accounts listing
- **Account Detail**: Platform info in page header
- **Legend**: Footer explanation of badge meanings
- **Styling**: 
  - MT4: Blue badge
  - MT5: Purple badge
  - Netting: Purple "N" badge
  - Hedging: Blue "H" badge

#### Dashboard Improvements
- **Recent Closed Positions**: Replaced "Recent Trades" with position-based view
- **Entry/Exit Prices**: Added columns for better clarity
- **Correct Types**: Shows position types instead of deal types
- **Platform Legend**: Footer with badge explanations

#### Account Detail Page
- **Filter Bar**: Clean, intuitive filter interface
- **Instant Feedback**: Filters apply immediately
- **Clear Filters**: One-click reset button
- **Pagination**: Reduced from 50 to 20 items per page
- **Expandable Rows**: Click to view individual deals (MT5 Netting)

### 🔧 Technical Improvements

#### New Services
- `PlatformDetectionService`: Detects platform type and account mode
- `PositionAggregationService`: Aggregates deals into positions

#### New Commands
- `accounts:detect-platforms`: Detects and updates platform type for existing accounts

#### New Components
- `platform-badge.blade.php`: Reusable platform badge component
- `expandable-position-row.blade.php`: Expandable position row with deals

#### Performance
- Client-side filtering using Alpine.js
- Optimized database queries with proper indexes
- Eager loading to prevent N+1 queries
- Caching strategy maintained

### 📝 Documentation

#### New Documentation Files
- `MT4_MT5_POSITION_SYSTEM.md` - Feature overview and usage
- `IMPLEMENTATION_DETAILS.md` - Technical implementation details
- `BUG_FIX_POSITION_TYPE.md` - Position type bug fix documentation
- `PLATFORM_BADGES_AND_FILTERS.md` - Badges and filters feature guide
- `CLIENT_SIDE_FILTERING_AND_PLATFORM_DETECTION.md` - Latest updates
- `MT4_MT5_FEATURE_SUMMARY.md` - Quick reference guide
- `TRADING_ARCHITECTURE_ANALYSIS.md` - Architecture analysis

### 🔄 Breaking Changes
**None** - All changes are backward compatible

### ⚠️ Deprecations
**None**

### 🔐 Security
**No security changes**

### ⬆️ Dependencies
**No new dependencies** - Uses existing Alpine.js

### 🗑️ Removed
**None**

---

## [1.1.0] - 2025-11-09

### Added
- Currency display improvements (single account vs multi-account context)
- Analytics fixes and improvements
- Flag icons for country display
- Various UI/UX enhancements

### Fixed
- Analytics calculation issues
- Currency conversion edge cases
- Performance metrics accuracy

### Documentation
- Multiple documentation cleanup passes
- Reorganized documentation structure
- Added comprehensive guides

---

## [1.0.0] - 2025-10-29

### Initial Release
- Trading account management
- Position tracking
- Deal history
- Basic analytics
- User authentication
- Multi-broker support
- Real-time data sync

---

## Migration Guide

### Upgrading to 1.2.0

#### Database Migration
```bash
# Run migrations
php artisan migrate

# Detect platform types for existing accounts
php artisan accounts:detect-platforms

# Clear caches
php artisan optimize:clear
```

#### No Code Changes Required
All changes are backward compatible. Existing functionality continues to work.

#### New Features Available
- Platform badges will appear automatically
- Filters work immediately on account detail pages
- Position types display correctly on dashboard

---

## Rollback Instructions

### Rolling Back 1.2.0

```bash
# Rollback migrations
php artisan migrate:rollback --step=3

# Restore from backup (if needed)
sudo -u postgres psql thetradevisor < /tmp/thetradevisor_backup_20251111_055014.sql

# Clear caches
php artisan optimize:clear
```

---

## Support

For issues, questions, or feature requests, please contact the development team or create an issue in the repository.

---

## Contributors

- Development Team
- QA Team
- Documentation Team

---

**Note**: This changelog follows semantic versioning. Version numbers indicate:
- **Major**: Breaking changes
- **Minor**: New features (backward compatible)
- **Patch**: Bug fixes (backward compatible)


---

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
