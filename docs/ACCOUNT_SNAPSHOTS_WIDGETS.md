# Account Snapshots Dashboard Widgets

**Date:** November 18, 2025  
**Status:** ✅ COMPLETE  
**Phase:** 5 - Dashboard Widgets

---

## Overview

Interactive dashboard widgets for visualizing account snapshot data over time, providing users with comprehensive insights into account performance, risk metrics, and historical trends.

---

## Features Implemented

### **1. Health Metrics Cards** 💊
**Location:** Top row of snapshots page

**Displays:**
- ✅ **Balance** - Current balance with 24h change percentage
- ✅ **Equity** - Current equity with % of balance and 24h change
- ✅ **Margin Level** - Current margin level with risk indicator
- ✅ **Unrealized P/L** - Current profit/loss with 24h change

**Visual Indicators:**
- Green/Red arrows for positive/negative changes
- Color-coded risk levels for margin (Critical < 100%, High < 200%, Moderate < 500%, Healthy > 500%)
- Icons for each metric type

---

### **2. Balance & Equity Trend Chart** 📈
**Location:** Main chart area

**Features:**
- ✅ Dual-line chart (Balance solid, Equity dashed)
- ✅ Interactive tooltips with formatted values
- ✅ Time range selector (7d, 30d, 90d, 180d)
- ✅ Responsive design
- ✅ Smooth animations
- ✅ Currency-aware formatting

**Technology:** Chart.js 4.4.0

---

### **3. Maximum Drawdown Gauge** 📉
**Location:** Bottom left

**Features:**
- ✅ Visual gauge with color zones:
  - Green: 0-10% (Excellent)
  - Yellow: 10-20% (Moderate)
  - Orange: 20-30% (High Risk)
  - Red: 30%+ (Critical)
- ✅ Animated needle indicator
- ✅ Peak and lowest equity display
- ✅ Educational tooltip

**Calculation:** Peak-to-trough equity decline percentage

---

### **4. Margin Usage Stats** 📊
**Location:** Bottom right

**Features:**
- ✅ Margin and free margin timeline chart
- ✅ Peak margin display
- ✅ Average margin display
- ✅ Compact visualization
- ✅ Educational tooltip

---

## File Structure

```
app/Http/Controllers/
└── AccountSnapshotViewController.php    (Main controller)

resources/views/
├── accounts/
│   └── snapshots.blade.php              (Main page)
└── components/snapshots/
    ├── health-metrics.blade.php         (Health cards)
    ├── balance-equity-chart.blade.php   (Main chart)
    ├── max-drawdown-gauge.blade.php     (Drawdown gauge)
    └── margin-stats.blade.php           (Margin chart)

routes/
└── web.php                              (Route definition)
```

---

## Routes

### **Main Snapshots Page**
```
GET /accounts/{account}/snapshots
```

**Parameters:**
- `account` - Trading account ID (required)
- `days` - Time range: 7, 30, 90, 180 (optional, default: 30)

**Example:**
```
https://thetradevisor.com/accounts/2/snapshots?days=90
```

---

## Navigation

### **Access Points:**

1. **Accounts Table** (`/accounts`)
   - 📊 icon in Actions column
   - Tooltip: "Snapshots"

2. **Account Detail Page** (`/account/{id}`)
   - "📊 View Snapshots" button in header
   - Purple button, prominent placement

3. **Direct URL**
   - `/accounts/{account}/snapshots`

---

## Controller Logic

### **AccountSnapshotViewController**

#### **Main Method: `index()`**
- ✅ Authorization check (user owns account)
- ✅ Time range validation
- ✅ Fetches current snapshot
- ✅ Fetches previous snapshot (24h ago)
- ✅ Calculates 24h changes
- ✅ Prepares chart data
- ✅ Calculates statistics
- ✅ Returns view with data

#### **Helper Methods:**

**`calculateChanges()`**
- Computes percentage changes for all metrics
- Handles null values gracefully

**`getChartData()`**
- Aggregates daily snapshots
- Uses SQL GROUP BY for efficiency
- Formats data for Chart.js
- Returns arrays of labels and values

**`getStatistics()`**
- Calculates min/max/avg for all metrics
- Calls max drawdown calculation
- Returns structured statistics array

**`calculateMaxDrawdown()`**
- Implements peak-to-trough algorithm
- Iterates through equity snapshots
- Tracks running peak
- Returns percentage drawdown

---

## Data Flow

```
User Request
    ↓
Route → AccountSnapshotViewController
    ↓
Authorization Check
    ↓
Fetch Data from Database
    ├── Current Snapshot (latest)
    ├── Previous Snapshot (24h ago)
    ├── Chart Data (daily aggregated)
    └── Statistics (min/max/avg)
    ↓
Calculate Metrics
    ├── 24h Changes
    ├── Max Drawdown
    └── Margin Stats
    ↓
Pass to View
    ↓
Render Components
    ├── Health Metrics Cards
    ├── Balance/Equity Chart
    ├── Drawdown Gauge
    └── Margin Stats
    ↓
JavaScript Initializes Charts
    ↓
User Interacts (change time range)
    ↓
Page Reloads with New Data
```

---

## Performance Optimizations

### **Database Queries:**
1. ✅ **Aggregation at DB level** - Uses SQL GROUP BY instead of PHP loops
2. ✅ **Indexed queries** - Leverages existing indexes on `trading_account_id` and `snapshot_time`
3. ✅ **Limited data fetch** - Only fetches data for selected time range
4. ✅ **Efficient calculations** - Max drawdown uses single pass algorithm

### **Frontend:**
1. ✅ **Chart.js CDN** - Fast loading from CDN
2. ✅ **Lazy chart initialization** - Charts only render when DOM ready
3. ✅ **Responsive design** - Single layout adapts to screen size
4. ✅ **Minimal JavaScript** - Only essential chart code

### **Expected Performance:**
- Page load: < 2 seconds
- Chart render: < 500ms
- Works with 10,000+ snapshots

---

## Security

### **Authorization:**
```php
if ($account->user_id !== auth()->id()) {
    abort(403, 'Unauthorized access to this account.');
}
```

- ✅ Users can only view their own account snapshots
- ✅ 403 error for unauthorized access
- ✅ No data leakage between users

---

## User Experience

### **Time Range Selector:**
- Visual active state (blue background)
- One-click switching
- URL parameter preserved
- Snapshot count displayed

### **Export Functionality:**
- "📥 Export CSV" button in header
- Downloads data for current time range
- Uses existing API endpoint

### **Responsive Design:**
- Mobile: Single column layout
- Tablet: 2-column grid
- Desktop: 4-column grid for metrics
- Charts adapt to container width

---

## Educational Elements

### **Info Boxes:**
1. **Snapshots Info** (bottom of page)
   - Explains automatic capture
   - Describes aggregation policy
   - Notes 180-day retention

2. **Max Drawdown Tooltip**
   - Defines the metric
   - Provides professional benchmark (< 20%)

3. **Margin Tooltip**
   - Explains margin concept
   - Warns about margin calls

---

## Testing Checklist

- [x] Route registered correctly
- [x] Controller authorization works
- [x] View renders without errors
- [x] All components display properly
- [x] Charts initialize correctly
- [x] Time range selector works
- [x] Navigation links functional
- [x] Export button links correctly
- [x] Responsive on mobile
- [x] No JavaScript errors
- [x] No PHP errors
- [x] Blade syntax valid

---

## Future Enhancements

### **Phase 5B (Optional):**
1. **Profit/Loss Heatmap** - Calendar view of daily P/L
2. **Multi-Account Comparison** - Compare multiple accounts
3. **Custom Date Range** - Date picker for specific periods
4. **Real-Time Updates** - WebSocket for live data
5. **Download as PDF** - Export charts as PDF report
6. **Email Reports** - Schedule automated reports

---

## Troubleshooting

### **Charts Not Displaying:**
- Check browser console for JavaScript errors
- Verify Chart.js CDN is accessible
- Ensure `@push('scripts')` is in layout

### **No Data Showing:**
- Verify account has snapshots in database
- Check time range has data
- Confirm user owns the account

### **Performance Issues:**
- Reduce time range (use 7d or 30d)
- Check database indexes
- Monitor query execution time

---

## API Integration

The widgets use the same data structure as the API endpoints:

**Internal Data Fetching:**
- Controller queries database directly
- More efficient than API calls
- Same calculation logic as API

**Export Uses API:**
- CSV export button links to API endpoint
- Maintains consistency
- Leverages existing authentication

---

## Code Quality

### **Principles Followed:**
- ✅ **DRY** - Reusable components
- ✅ **Separation of Concerns** - Controller/View/Component split
- ✅ **Security First** - Authorization checks
- ✅ **Performance** - Optimized queries
- ✅ **Maintainability** - Clear code structure
- ✅ **Documentation** - Inline comments

### **Laravel Best Practices:**
- ✅ Route model binding
- ✅ Blade components
- ✅ Eloquent ORM
- ✅ Authorization gates
- ✅ View composition

---

## Metrics

### **Lines of Code:**
- Controller: ~220 lines
- Main View: ~130 lines
- Components: ~450 lines (total)
- **Total: ~800 lines**

### **Files Created:**
- 1 Controller
- 1 Main View
- 4 Components
- 1 Route
- 1 Documentation
- **Total: 8 files**

### **Features Delivered:**
- 4 Widget types
- 2 Navigation links
- 1 Time range selector
- 1 Export button
- 3 Educational tooltips
- **Total: 11 features**

---

## Maintenance

### **Regular Tasks:**
- Monitor page load times
- Check for JavaScript errors in logs
- Verify chart rendering on new browsers
- Update Chart.js version periodically

### **Database Maintenance:**
- Aggregation runs daily (automated)
- Cleanup runs daily (automated)
- No manual intervention needed

---

## Support

For issues or questions:
- 📧 Email: [hello@thetradevisor.com](mailto:hello@thetradevisor.com)
- 📚 Documentation: `/docs/ACCOUNT_SNAPSHOTS_SYSTEM.md`
- 🔧 Technical: `/docs/ACCOUNT_SNAPSHOTS_WIDGETS.md` (this file)

---

**Status:** ✅ **PRODUCTION READY**

All widgets are functional, tested, and ready for user access.
