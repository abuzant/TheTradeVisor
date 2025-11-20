# Platform Badges & Position Filters - Feature Documentation

**Version:** 1.1  
**Date:** November 11, 2025  
**Status:** ✅ Production

---

## 🎯 New Features Added

### 1. Platform Badges
Display MT4/MT5 platform type and account mode (Netting/Hedging) throughout the application.

### 2. Position Filters
Filter positions by status (open/closed) and profitability (profitable/losses) on account detail pages.

---

## 📍 Where Platform Badges Appear

### Dashboard (`/dashboard`)
- **Location:** Accounts table, next to account number
- **Display:** `[MT4]` or `[MT5]` with `[N]` or `[H]` badge
- **Legend:** Footer of accounts table

### Accounts Page (`/accounts`)
- **Location:** Accounts table, next to account number
- **Display:** Same as dashboard
- **Legend:** Footer of accounts table

### Account Detail Page (`/account/{id}`)
- **Location:** Page header, next to title
- **Display:** Full platform name with mode

---

## 🎨 Badge Styles

### Platform Type Badges
- **MT4:** Blue badge (`bg-blue-100 text-blue-800`)
- **MT5:** Purple badge (`bg-purple-100 text-purple-800`)
- **Unknown:** Gray badge with `?` (platform not detected yet)

### Account Mode Badges (MT5 only)
- **N (Netting):** Purple badge (`bg-purple-200 text-purple-900`)
- **H (Hedging):** Blue badge (`bg-blue-200 text-blue-900`)

### Legend
Appears at the bottom of account tables:
```
Platform Legend:
[MT4] = MetaTrader 4
[MT5] = MetaTrader 5
[N] = Netting
[H] = Hedging
```

---

## 🔍 Position Filters

### Available Filters (Account Detail Page)

#### Status Filters
1. **Show Open Only** - Display only open positions
2. **Show Closed Only** - Display only closed positions

#### Profitability Filters
3. **Show Profitable Only** - Display only positions with profit > 0
4. **Show Losses Only** - Display only positions with profit < 0

### Filter Behavior

**Combining Filters:**
- Open + Profitable = Open positions that are profitable
- Closed + Losses = Closed positions that lost money
- Multiple filters work together (AND logic)

**Clearing Filters:**
- Click "Clear Filters" link
- Or uncheck all checkboxes

**Auto-Submit:**
- Filters apply immediately when checkbox changes
- No need to click a submit button

---

## 💻 Implementation Details

### Component Created
**File:** `/www/resources/views/components/platform-badge.blade.php`

**Usage:**
```blade
<x-platform-badge :account="$account" />
```

**Output:**
- If MT4: `[MT4]`
- If MT5 Netting: `[MT5] [N]`
- If MT5 Hedging: `[MT5] [H]`
- If unknown: `[?]`

### Controller Updates
**File:** `/www/app/Http/Controllers/DashboardController.php`

**Changes:**
- Added filter parameters to `account()` method
- Filters: `show_open`, `show_closed`, `show_profitable`, `show_losses`
- Changed pagination from 50 to 20 items per page
- Filters preserved in pagination links

### View Updates

#### Dashboard
- Added platform badge component
- Added legend footer

#### Accounts Index
- Added platform badge component
- Added legend footer

#### Account Show
- Added filter form with checkboxes
- Added "Clear Filters" link
- Pagination already existed (now 20 per page)

---

## 🎯 User Experience

### For Account Lists
**Before:**
```
Account Number
123456789
```

**After:**
```
Account Number          Platform
123456789              [MT5] [N]
```

### For Position Filtering
**Before:**
- All positions shown (no filters)
- 50 per page

**After:**
- Filterable by status and profitability
- 20 per page (easier to navigate)
- Filters persist across pages

---

## 📊 Filter Examples

### Example 1: Find All Losing Trades
1. Go to `/account/111`
2. Check "Show Losses Only"
3. See only positions with negative profit

### Example 2: Review Open Profitable Positions
1. Check "Show Open Only"
2. Check "Show Profitable Only"
3. See only open positions currently in profit

### Example 3: Analyze Closed Losses
1. Check "Show Closed Only"
2. Check "Show Losses Only"
3. Review what went wrong with losing trades

---

## 🔧 Technical Notes

### Pagination
- Changed from 50 to 20 items per page
- Uses `withQueryString()` to preserve filters
- Page numbers maintain filter state

### Performance
- Filters applied at database level (efficient)
- No N+1 queries
- Indexes support filter queries

### Backward Compatibility
- Works with NULL `platform_type` (shows `?`)
- No errors if platform not detected
- Graceful degradation

---

## 📝 Code Examples

### Using Platform Badge
```blade
{{-- In any view with $account --}}
<x-platform-badge :account="$account" />
```

### Checking Platform Type
```php
// In controller or model
if ($account->platform_type === 'MT5') {
    // MT5 specific logic
}

if ($account->account_mode === 'netting') {
    // Netting specific logic
}
```

### Building Filter URL
```php
// With filters
route('account.show', [
    'account' => $account->id,
    'show_open' => 1,
    'show_profitable' => 1
])
```

---

## ✅ Testing Checklist

### Platform Badges
- [x] Dashboard shows badges
- [x] Accounts page shows badges
- [x] Account detail shows platform info
- [x] Legend appears in table footers
- [x] Unknown platforms show `?`

### Position Filters
- [x] Show Open Only works
- [x] Show Closed Only works
- [x] Show Profitable Only works
- [x] Show Losses Only works
- [x] Multiple filters combine correctly
- [x] Clear Filters works
- [x] Pagination preserves filters

---

## 🎨 Visual Reference

### Badge Colors
```
MT4:  Blue background, dark blue text
MT5:  Purple background, dark purple text
N:    Light purple background, dark purple text
H:    Light blue background, dark blue text
?:    Gray background, gray text
```

### Filter Checkboxes
```
Status:        Indigo checkboxes
Profitable:    Green checkbox
Losses:        Red checkbox
```

---

## 📞 Support

### If Badges Don't Show
1. Platform type may not be detected yet
2. Will show `?` until platform is detected
3. Detection happens on next account sync

### If Filters Don't Work
1. Clear browser cache
2. Check URL has filter parameters
3. Verify JavaScript is enabled (for auto-submit)

---

## 🚀 Future Enhancements

### Planned
1. **Platform Detection** - Auto-detect on account connection
2. **More Filters** - Date range, symbol, volume
3. **Saved Filters** - Remember user's preferred filters
4. **Export Filtered** - Export only filtered positions

---

**Status:** Production Ready ✅  
**Impact:** UI Enhancement Only  
**Compatibility:** 100% Backward Compatible

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
