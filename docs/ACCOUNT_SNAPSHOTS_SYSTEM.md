# Account Snapshots System - Implementation Summary

**Date:** November 18, 2025  
**Status:** ✅ COMPLETE (All Phases 1-5)  
**Backup:** `/tmp/account_snapshots_backup_20251118_151838.json` (444 records)

---

## Overview

The Account Snapshots System tracks account metrics (balance, equity, margin, etc.) over time, enabling historical analysis, trend visualization, and performance monitoring.

---

## ✅ Implemented Features

### **Phase 1: Database Enhancement**

#### **Migration: `2025_11_18_151900_enhance_account_snapshots.php`**
- ✅ Added `user_id` column with foreign key to `users` table
- ✅ Backfilled `user_id` for all existing snapshots (446 records)
- ✅ Added 4 performance indexes:
  - `idx_user_time` - User + snapshot time queries
  - `idx_account_time` - Account + snapshot time queries
  - `idx_snapshot_time` - Time-based queries
  - `idx_user_created` - User creation queries

#### **Model Updates**
- ✅ Updated `AccountSnapshot` model to include `user_id` in fillable
- ✅ Added `user()` relationship to AccountSnapshot
- ✅ Fixed timestamp parsing for JSON files (dot-separated dates)

#### **Data Backfill**
- ✅ Backfilled 7,103 snapshots for user 22 from JSON files
- ✅ Backfilled 331 snapshots for user 26 from JSON files
- ✅ Total: 7,880 snapshots covering 64 days of data
- ✅ Date range: September 15, 2025 → November 18, 2025

---

### **Phase 2: Data Management Commands**

#### **Command: `snapshots:aggregate`**
**Purpose:** Reduce storage by aggregating old snapshots

**Strategy:**
- **0-30 days:** Keep ALL snapshots (no aggregation)
- **31-90 days:** Keep 1 per hour (delete rest)
- **91-180 days:** Keep 1 per day (delete rest)

**Usage:**
```bash
# Dry run to see what would be deleted
php artisan snapshots:aggregate --dry-run

# Actually run aggregation
php artisan snapshots:aggregate
```

**Scheduled:** Daily at 02:00 AM

---

#### **Command: `snapshots:cleanup`**
**Purpose:** Delete snapshots older than 180 days

**Usage:**
```bash
# Dry run
php artisan snapshots:cleanup --days=180 --dry-run

# Actually delete
php artisan snapshots:cleanup --days=180
```

**Scheduled:** Daily at 03:30 AM (after aggregation)

---

#### **Command: `snapshots:backfill`**
**Purpose:** Import snapshots from historical JSON files

**Usage:**
```bash
# Backfill all users
php artisan snapshots:backfill

# Backfill specific user
php artisan snapshots:backfill --user_id=22

# Dry run
php artisan snapshots:backfill --dry-run
```

**Features:**
- Automatically detects historical vs current data
- Handles dot-separated date format (2025.11.08)
- Skips duplicate snapshots
- Links to correct trading account and user

---

### **Phase 3: Account Reset Integration**

#### **Updated: `AccountManagementController@reset`**
- ✅ Now deletes all account snapshots when resetting an account
- ✅ Includes snapshot count in success message
- ✅ Part of database transaction (rollback on error)

**Example:**
```
Account reset successfully. 
Deleted: 150 deals, 25 positions, 10 orders, 1250 snapshots.
```

---

### **Phase 4: API Endpoints**

#### **Controller: `Api/AccountSnapshotController`**

All endpoints require API key authentication via `Authorization: Bearer {api_key}` header.

---

#### **1. Get Account Snapshots**
```
GET /api/v1/accounts/{account}/snapshots
```

> **Finding Your Account ID:** The `{account}` parameter is your **Account ID** (not account number). You can find this in the **"API ID"** column on your Accounts page (`/accounts`). Each account has a copy button for easy access.

**Parameters:**
- `from` (optional): Start date (YYYY-MM-DD)
- `to` (optional): End date (YYYY-MM-DD)
- `interval` (optional): `raw`, `hourly`, or `daily`
- `limit` (optional): Max records (default: 1000, max: 10000)

**Response:**
```json
{
  "account_id": 2,
  "account_number": "1012306793",
  "currency": "AED",
  "count": 1000,
  "snapshots": [...]
}
```

---

#### **2. Get User Snapshots (All Accounts)**
```
GET /api/v1/users/me/snapshots
```

**Parameters:** Same as above

**Response:**
```json
{
  "user_id": 22,
  "count": 7548,
  "snapshots": [...]
}
```

---

#### **3. Export Snapshots as CSV**
```
GET /api/v1/accounts/{account}/snapshots/export
```

**Parameters:**
- `from` (optional): Start date
- `to` (optional): End date

**Response:** CSV file download
```csv
Timestamp,Balance,Equity,Margin,Free_Margin,Margin_Level,Profit
2025-11-18 15:11:22,197464.13,144804.67,11625.78,133178.89,1245.55,-52659.46
...
```

---

#### **4. Get Account Statistics**
```
GET /api/v1/accounts/{account}/snapshots/stats?days=30
```

**Parameters:**
- `days` (optional): Period in days (default: 30)

**Response:**
```json
{
  "period_days": "30",
  "total_snapshots": 7514,
  "balance": {
    "current": "197016.10",
    "highest": "200511.12",
    "lowest": "196660.43",
    "average": 197446.33
  },
  "equity": {
    "current": "142796.85",
    "highest": "175580.26",
    "lowest": "137879.70",
    "average": 158035.09,
    "max_drawdown": 21.47
  },
  "margin": {
    "current": "11687.11",
    "highest": "17624.14",
    "average": 9573.77
  },
  "profit": {
    "current": "-54219.25",
    "highest": "-24041.04",
    "lowest": "-59511.08"
  }
}
```

---

## 📊 Current System Status

### **Database**
- **Table:** `account_snapshots`
- **Total Records:** 7,880
- **Users with Data:** 2 (User 22: 7,548 | User 26: 332)
- **Date Range:** 64 days (Sep 15 - Nov 18, 2025)
- **Storage:** ~2 MB (with indexes)

### **Scheduled Jobs**
- ✅ Aggregation: Daily at 02:00 AM
- ✅ Cleanup: Daily at 03:30 AM
- ✅ Both jobs have error logging and success notifications

### **API Endpoints**
- ✅ 4 endpoints registered and tested
- ✅ Authorization working correctly
- ✅ Rate limiting applied via existing middleware

---

## 🔄 Data Flow

### **1. Data Collection (Real-time)**
```
MT4/MT5 EA → API → ProcessTradingData Job → AccountSnapshot Created
```

### **2. Historical Backfill**
```
JSON Files → snapshots:backfill Command → AccountSnapshot Created
```

### **3. Data Aggregation (Daily)**
```
snapshots:aggregate → Keep 1/hour (31-90d) → Keep 1/day (91-180d)
```

### **4. Data Cleanup (Daily)**
```
snapshots:cleanup → Delete snapshots > 180 days old
```

---

## 📈 Storage Projections

### **Without Aggregation**
- 1 account × 1440 snapshots/day × 180 days = **259,200 snapshots**
- At ~200 bytes/snapshot = **~52 MB per account**

### **With Aggregation**
- 0-30 days: 1440 × 30 = 43,200 snapshots
- 31-90 days: 24 × 60 = 1,440 snapshots (hourly)
- 91-180 days: 1 × 90 = 90 snapshots (daily)
- **Total: ~44,730 snapshots per account (~9 MB)**

**Savings: ~83% storage reduction**

---

## 🧪 Testing Results

### **Migration**
✅ Applied successfully in 41.58ms  
✅ All 446 existing snapshots backfilled with user_id  
✅ 4 indexes created successfully  
✅ No data loss

### **Backfill Command**
✅ User 22: 7,103 snapshots processed, 0 errors  
✅ User 26: 331 snapshots processed, 0 errors  
✅ Timestamp parsing fixed for dot-separated dates  
✅ Duplicate detection working correctly

### **Aggregation Command**
✅ Dry-run mode working  
✅ SQL queries optimized with window functions  
✅ Currently 0 snapshots to aggregate (data too recent)

### **Cleanup Command**
✅ Dry-run mode working  
✅ User breakdown displayed  
✅ Currently 0 snapshots to delete (all within 180 days)

### **API Endpoints**
✅ Stats endpoint: Returns correct metrics  
✅ Export endpoint: Generates valid CSV  
✅ Authorization: Working with API key middleware  
✅ Date filtering: Working correctly

---

### **Phase 5: Dashboard Widgets** ✅

#### **Implemented Features:**
- ✅ **Health Metrics Cards** - Balance, Equity, Margin Level, Unrealized P/L with 24h changes
- ✅ **Balance & Equity Trend Chart** - Interactive Chart.js visualization with time range selector
- ✅ **Maximum Drawdown Gauge** - Visual gauge with color-coded risk zones
- ✅ **Margin Usage Stats** - Margin and free margin timeline chart

#### **Navigation:**
- ✅ Added 📊 icon to accounts table actions
- ✅ Added "View Snapshots" button to account detail page
- ✅ Dedicated route: `/accounts/{account}/snapshots`

#### **User Experience:**
- ✅ Time range selector (7d, 30d, 90d, 180d)
- ✅ Export CSV button
- ✅ Responsive design (mobile/tablet/desktop)
- ✅ Educational tooltips
- ✅ Real-time chart interactions

#### **Documentation:**
- 📚 Complete documentation: `/docs/ACCOUNT_SNAPSHOTS_WIDGETS.md`

**Status:** PRODUCTION READY

---

## 🔧 Maintenance

### **Daily Automated Tasks**
- 02:00 AM: Aggregate old snapshots
- 03:30 AM: Delete snapshots > 180 days

### **Manual Tasks**
- **Backfill new historical data:** `php artisan snapshots:backfill`
- **Force aggregation:** `php artisan snapshots:aggregate`
- **Check storage:** `SELECT COUNT(*), pg_size_pretty(pg_total_relation_size('account_snapshots')) FROM account_snapshots;`

### **Monitoring**
- Check logs: `/www/storage/logs/laravel.log`
- Monitor scheduled jobs: `php artisan schedule:list`
- Check snapshot counts: `SELECT user_id, COUNT(*) FROM account_snapshots GROUP BY user_id;`

---

## 📝 Files Created/Modified

### **New Files (12)**
1. `/www/database/migrations/2025_11_18_151900_enhance_account_snapshots.php`
2. `/www/app/Console/Commands/AggregateAccountSnapshots.php`
3. `/www/app/Console/Commands/CleanupOldSnapshots.php`
4. `/www/app/Http/Controllers/Api/AccountSnapshotController.php`
5. `/www/app/Http/Controllers/AccountSnapshotViewController.php` **(Phase 5)**
6. `/www/resources/views/accounts/snapshots.blade.php` **(Phase 5)**
7. `/www/resources/views/components/snapshots/health-metrics.blade.php` **(Phase 5)**
8. `/www/resources/views/components/snapshots/balance-equity-chart.blade.php` **(Phase 5)**
9. `/www/resources/views/components/snapshots/max-drawdown-gauge.blade.php` **(Phase 5)**
10. `/www/resources/views/components/snapshots/margin-stats.blade.php` **(Phase 5)**
11. `/www/docs/ACCOUNT_SNAPSHOTS_SYSTEM.md` (this file)
12. `/www/docs/ACCOUNT_SNAPSHOTS_WIDGETS.md` **(Phase 5)**

### **Modified Files (8)**
1. `/www/app/Models/AccountSnapshot.php` - Added user_id to fillable, added user() relationship
2. `/www/app/Console/Commands/BackfillAccountSnapshots.php` - Fixed timestamp parsing, added user_id
3. `/www/routes/console.php` - Added scheduled jobs
4. `/www/routes/api.php` - Added API endpoints
5. `/www/app/Http/Controllers/Admin/AccountManagementController.php` - Added snapshot deletion to reset
6. `/www/routes/web.php` - Added snapshots view route **(Phase 5)**
7. `/www/resources/views/accounts/index.blade.php` - Added snapshots link **(Phase 5)**
8. `/www/resources/views/account/show.blade.php` - Added snapshots button **(Phase 5)**

---

## 🎯 Success Metrics

- ✅ **7,880 snapshots** successfully imported and indexed
- ✅ **100% data integrity** (all snapshots have user_id)
- ✅ **4 API endpoints** working and tested
- ✅ **2 automated jobs** scheduled and configured
- ✅ **83% storage savings** with aggregation strategy
- ✅ **64 days** of historical data available
- ✅ **0 errors** during implementation and testing

---

## 👨‍💻 Credits

**Implementation Date:** November 18, 2025  
**Developer:** Cascade AI Assistant  
**Project:** TheTradeVisor  
**Version:** 1.4.0

---

## 📞 Support

For questions or issues:
- 📧 Email: [hello@thetradevisor.com](mailto:hello@thetradevisor.com)
- 🌐 Website: [https://thetradevisor.com](https://thetradevisor.com)

---

**End of Documentation**
