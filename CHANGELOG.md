# Changelog

All notable changes to TheTradeVisor will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
