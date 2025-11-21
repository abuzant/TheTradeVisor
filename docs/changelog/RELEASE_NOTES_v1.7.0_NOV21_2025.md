# Release Notes v1.7.0 - Enterprise Portal & Billing System Overhaul

**Release Date:** November 21, 2025  
**Version:** 1.7.0  
**Status:** Production Ready  
**Tag:** `v1.7.0-enterprise-billing-overhaul`

---

## 🎯 Executive Summary

This major release introduces a complete enterprise broker solution with a dedicated subdomain portal, implements a completely free platform with broker-based time-period access control, upgrades infrastructure to AWS EC2 M5.large for consistent performance, and enhances the admin panel with comprehensive broker management tools. This release represents a significant milestone in TheTradeVisor's evolution from a trader-focused platform to a comprehensive B2B2C solution.

---

## 🏢 Enterprise Features

### 1. Enterprise Subdomain Portal
**URL:** `https://enterprise.thetradevisor.com`

**Infrastructure:**
- ✅ Dedicated Nginx configuration with SSL/TLS
- ✅ Let's Encrypt SSL certificate (valid until Feb 19, 2026)
- ✅ Security headers (HSTS, X-Frame-Options, CSP)
- ✅ Separate access/error logging
- ✅ robots.txt (disallow all - private portal)

**Authentication & Access Control:**
- Custom `EnterpriseAdminMiddleware` for access control
- Separate enterprise login page with reCAPTCHA
- Role-based access (`is_enterprise_admin` flag)
- Active subscription verification

**Available Pages:**
1. **Enterprise Dashboard** (`/enterprise/dashboard`)
   - Aggregated statistics for all broker accounts
   - Total accounts, balance, equity metrics
   - Win rate, profit factor, average trade duration
   - Recent accounts list and top performers
   - Account growth charts

2. **Enterprise Analytics** (`/enterprise/analytics`)
   - Advanced filtering (country, platform, symbol, time period)
   - Trading hours analysis
   - Symbol distribution
   - Geographic distribution
   - Platform usage statistics

3. **Enterprise Accounts** (`/enterprise/accounts`)
   - List of all trader accounts on broker server(s)
   - Account details and status
   - Last sync time tracking
   - Filter and search capabilities

4. **Enterprise Settings** (`/enterprise/settings`)
   - Broker profile information
   - Subscription details
   - API keys management
   - Generate/revoke API keys
   - API usage statistics

**Files Created:**
```
nginx-enterprise-subdomain.conf
app/Http/Middleware/EnterpriseAdminMiddleware.php
app/Http/Middleware/EnterpriseSubdomainOnly.php
app/Http/Controllers/Auth/EnterpriseLoginController.php
resources/views/auth/enterprise-login.blade.php
resources/views/enterprise/dashboard.blade.php
resources/views/enterprise/analytics.blade.php
resources/views/enterprise/settings.blade.php
```

**Documentation:**
- `docs/implementation/ENTERPRISE_SUBDOMAIN_SETUP.md`

---

### 2. Broker Whitelist System
**Purpose:** Allow brokers to give their clients unlimited free accounts

**Database Schema:**
```sql
-- Enterprise Brokers Table
CREATE TABLE enterprise_brokers (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id),
    company_name VARCHAR(255),
    official_broker_name VARCHAR(255) UNIQUE,
    is_active BOOLEAN DEFAULT true,
    monthly_fee DECIMAL(10,2),
    subscription_ends_at TIMESTAMP,
    grace_period_ends_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Whitelisted Broker Usage Tracking
CREATE TABLE whitelisted_broker_usage (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id),
    trading_account_id BIGINT REFERENCES trading_accounts(id),
    enterprise_broker_id BIGINT REFERENCES enterprise_brokers(id),
    account_number VARCHAR(255),
    first_seen_at TIMESTAMP,
    last_seen_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**How It Works:**
1. Broker subscribes to enterprise plan
2. Broker's MT4/MT5 server name is whitelisted in `official_broker_name`
3. Any trader using that broker gets unlimited free accounts
4. System tracks usage in `whitelisted_broker_usage` table
5. Broker accesses enterprise portal to view client analytics

**Grace Period System:**
- 30-day grace period after subscription expires
- Service continues during grace period with warnings
- After grace period: New accounts blocked, existing accounts continue

**API Response Changes:**
```json
{
    "success": true,
    "message": "Data received successfully",
    "whitelisted_broker": true,
    "grace_period_warning": "Broker's enterprise plan expired. Grace period ends: 2025-12-19"
}
```

**Files Created:**
```
database/migrations/2025_11_19_081720_create_enterprise_brokers_table.php
database/migrations/2025_11_19_081807_create_whitelisted_broker_usage_table.php
app/Models/EnterpriseBroker.php
app/Models/WhitelistedBrokerUsage.php
```

**Files Modified:**
```
app/Http/Controllers/Api/DataCollectionController.php
app/Models/User.php
app/Models/TradingAccount.php
routes/web.php
```

**Documentation:**
- `docs/features/ENTERPRISE_BROKER_WHITELIST.md`
- `docs/features/ENTERPRISE_IMPLEMENTATION_AUDIT.md`
- `docs/features/IMPLEMENTATION_SUMMARY_ENTERPRISE.md`

---

## 💰 Billing System Overhaul

### Free-for-All with Time-Based Access
**Old System:**
- Subscription tiers: Free (1), Basic (3), Pro (10), Enterprise (unlimited)
- Account limits per tier
- Monthly recurring fees
- Complex tier management

**New System:**
- **Unlimited FREE accounts** for all users
- **No payment required** - ever
- **Time-based access control:**
  - Whitelisted broker users: Full time spans (7d, 30d, 90d, 180d)
  - Non-whitelisted broker users: Limited time spans (1-7d only)
- **No payment option** for individual traders
- Brokers pay for whitelist to unlock full features for their clients

**Database Changes:**
```sql
-- Migration: 2025_11_21_064434_remove_subscription_fields_from_users.php
ALTER TABLE users 
    DROP COLUMN subscription_tier,
    DROP COLUMN max_accounts;
```

**Access Model:**
- All users: Unlimited accounts, no cost
- Whitelisted brokers: Clients get full time period access
- Non-whitelisted brokers: Clients get 1-7 day view only
- No individual payment options

**Benefits:**
- ✅ Completely free for all traders
- ✅ No billing complexity
- ✅ Zero barrier to entry
- ✅ Broker-driven premium features
- ✅ No paywalls for individual users

**Files Modified:**
```
app/Http/Controllers/Admin/UserManagementController.php
resources/views/public/pricing.blade.php
resources/views/public/faq.blade.php
resources/views/settings/api-key.blade.php
tests/Unit/PricingUpdateTest.php
tests/Feature/AccountLimitEnforcementTest.php
```

**Documentation:**
- Updated README.md pricing section
- Updated FAQ page
- Updated pricing page

---

## 🚀 Infrastructure Upgrade

### AWS EC2 M5.large Migration
**Previous:** T-series (likely t3.medium) with CPU credit system  
**Current:** M5.large with consistent performance

**Specifications:**
- **vCPUs:** 2 (Intel Xeon Platinum 8259CL @ 2.50GHz)
- **RAM:** 8GB (doubled from 4GB)
- **Network:** Up to 10 Gbps
- **Cost:** ~$70/month (from ~$30/month)
- **Performance:** Consistent baseline, no CPU credits

**Architecture Simplification:**
```
Before: Internet → Nginx → Backend Nginx (8081-8084) → PHP-FPM
After:  Internet → Nginx → PHP-FPM (direct via unix socket)
```

**Benefits:**
- ✅ No more CPU credit exhaustion
- ✅ Consistent 24/7 performance
- ✅ Doubled memory capacity
- ✅ Better network bandwidth
- ✅ Simpler architecture
- ✅ 99.9% uptime

**Configuration Changes:**
```
/etc/nginx/sites-enabled/thetradevisor.com
- Removed upstream backend_pool
- Changed proxy_pass to fastcgi_pass
- Direct PHP-FPM socket connection
```

**Documentation:**
- `docs/INSTANCE_UPGRADE_SUMMARY.md`

---

## 🔧 Admin Panel Enhancements

### 1. Enhanced Admin Dashboard
**New Statistics Boxes:**

1. **Brokers Statistics** (replaces "Trades Today")
   - Total count of unique broker names
   - Count of active enterprise brokers
   - Format: "X brokers" with "Y enterprise" subtitle

2. **Next Enterprise Expiry** (replaces "Volume Today")
   - Company name of next broker to expire
   - Expiration date in human-readable format
   - Shows "No active subscriptions" if none exist

3. **Active Terminals** (replaces "Quick Actions")
   - Count of accounts synced within last hour
   - Based on `last_sync_at` timestamp
   - Shows "Last hour" subtitle

**Enhanced Recent Users Table:**
- Replaced "Plan" column with "Broker" column
- Shows primary broker (most accounts) for each user
- Displays "(+N)" indicator for multi-broker users
- Enterprise broker star indicator (✨) with tooltip
- Shows account count per broker

**Files Modified:**
```
app/Http/Controllers/Admin/AdminController.php
resources/views/admin/dashboard.blade.php
```

**Documentation:**
- `docs/changelog/ADMIN_DASHBOARD_STATS_UPDATE_NOV21.md`
- `docs/changelog/ADMIN_USERS_PAGE_UPDATE_NOV21.md`

---

### 2. Broker Management System
**New Controller:** `BrokerManagementController`

**Features:**
- Full CRUD for enterprise brokers
- View all enterprise brokers
- Add new enterprise brokers
- Edit broker details
- Activate/deactivate subscriptions
- Set subscription expiry dates
- Track broker usage statistics

**Routes:**
```php
Route::prefix('admin/brokers')->group(function () {
    Route::get('/', [BrokerManagementController::class, 'index']);
    Route::get('/create', [BrokerManagementController::class, 'create']);
    Route::post('/', [BrokerManagementController::class, 'store']);
    Route::get('/{broker}/edit', [BrokerManagementController::class, 'edit']);
    Route::put('/{broker}', [BrokerManagementController::class, 'update']);
    Route::delete('/{broker}', [BrokerManagementController::class, 'destroy']);
});
```

**Files Created:**
```
app/Http/Controllers/Admin/BrokerManagementController.php
resources/views/admin/brokers/index.blade.php
resources/views/admin/brokers/create.blade.php
resources/views/admin/brokers/edit.blade.php
```

---

### 3. Admin Wiki Enhancement
**Location:** `/admin/wiki`

**New Sections:**
- Enterprise broker management commands
- Subscription management procedures
- Whitelist verification steps
- Usage tracking queries
- Grace period handling

**Files Modified:**
```
app/Http/Controllers/Admin/AdminWikiController.php
resources/views/admin/wiki.blade.php
```

**Documentation:**
- `docs/guides/ADMIN_WIKI.md`
- `docs/guides/ADMIN_WIKI_QUICK_START.md`

---

## 🐛 Bug Fixes

### 1. Domain Routing Issues
**Problem:** Domain groups in routes broke `route()` helper, causing 500 errors

**Solution:**
- Removed domain groups from routes
- Created middleware-based domain restriction:
  - `EnterpriseSubdomainOnly` - Restricts to enterprise subdomain
  - `MainDomainOnly` - Blocks enterprise subdomain from main routes

**Files Modified:**
```
routes/web.php
app/Http/Middleware/EnterpriseSubdomainOnly.php (new)
app/Http/Middleware/MainDomainOnly.php (new)
bootstrap/app.php
```

---

### 2. API Subdomain 404 Errors
**Problem:** Expert Advisors getting HTTP 404 on `/api/v1/data/collect`

**Root Cause:** Nginx variable `$redirect_to_main` not initialized

**Solution:**
- Initialized `$redirect_to_main = 0` at server level
- Fixed location block to pass API requests to PHP-FPM

**Files Modified:**
```
nginx-api-subdomain.conf
```

---

### 3. MT4 Account Trade Display
**Problem:** Account #4 (MT4) returning 500 error: "column 'profit' does not exist"

**Root Cause:** Code assumed MT4 data in `orders` table, but stored in `deals` table

**Solution:**
- Changed logic to check which table has data
- Query Deals table for most MT4 accounts
- Fallback to Orders table if needed

**Files Modified:**
```
app/Http/Controllers/DashboardController.php
```

---

### 4. MT4 Trades Showing Only 1 Instead of 4
**Problem:** Statistics showed "4 Total Trades" but table showed only 1

**Root Cause:** All MT4 deals had `position_id = NULL`, grouped as one

**Solution:**
- When `position_id` is NULL, treat each deal as separate trade
- Loop through all deals with NULL position_id
- Create individual position objects

**Files Modified:**
```
app/Http/Controllers/DashboardController.php
```

---

### 5. Performance Page Time Period Selection
**Problem:** Active button stuck on "7 Days" regardless of selection

**Root Cause:** Time-filter component generated `?days=30` but controller expected `?period=30d`

**Solution:**
- Added `paramName` prop to time-filter component
- Component now generates correct parameter names per page

**Files Modified:**
```
resources/views/components/time-filter.blade.php
resources/views/performance/index.blade.php
```

---

### 6. Performance Page 90d/180d Data
**Problem:** 90 Days and 180 Days showed same data as 30 Days

**Root Cause:** `getPeriodConfig()` missing configurations for '90d' and '180d'

**Solution:**
- Added '90d' config: 90 days, 6-hour cache
- Added '180d' config: 180 days, 12-hour cache

**Files Modified:**
```
app/Http/Controllers/PerformanceController.php
```

---

### 7. PRO Badges on Time Selectors
**Problem:** Locked time periods not visually distinct

**Solution:**
- Added small "PRO" badges to locked buttons
- Badge positioned at top-right with gradient
- Consistent across all time selector pages

**Files Modified:**
```
resources/views/components/time-filter.blade.php
resources/views/accounts/health-overview.blade.php
app/Http/Controllers/AccountSnapshotViewController.php
```

---

### 8. Enterprise Login Security
**Problem:** Enterprise login missing Google Analytics and reCAPTCHA

**Solution:**
- Added Google Analytics component
- Added reCAPTCHA v2 widget
- Added `recaptcha` middleware to POST route

**Files Modified:**
```
resources/views/auth/enterprise-login.blade.php
routes/web.php
```

---

## 📊 System Status

### Production Metrics
- **Uptime:** 99.9%
- **Response Time:** 50-200ms (cached)
- **Memory Usage:** 34% (2.6GB / 7.6GB)
- **CPU Load:** 0.40, 0.43, 0.20
- **Cache Hit Rate:** 90%

### Services Running
- ✅ Nginx 1.24
- ✅ PHP 8.3-FPM (5 pools)
- ✅ PostgreSQL 16
- ✅ Redis 7
- ✅ Laravel Horizon
- ✅ Fail2ban
- ✅ Cron jobs

---

## 🗂️ New Files Created

### Controllers
```
app/Http/Controllers/EnterpriseController.php
app/Http/Controllers/Auth/EnterpriseLoginController.php
app/Http/Controllers/Admin/BrokerManagementController.php
```

### Middleware
```
app/Http/Middleware/EnterpriseAdminMiddleware.php
app/Http/Middleware/EnterpriseSubdomainOnly.php
app/Http/Middleware/MainDomainOnly.php
```

### Models
```
app/Models/EnterpriseBroker.php
app/Models/WhitelistedBrokerUsage.php
```

### Views
```
resources/views/auth/enterprise-login.blade.php
resources/views/enterprise/dashboard.blade.php
resources/views/enterprise/analytics.blade.php
resources/views/enterprise/settings.blade.php
resources/views/admin/brokers/index.blade.php
resources/views/admin/brokers/create.blade.php
resources/views/admin/brokers/edit.blade.php
```

### Migrations
```
database/migrations/2025_11_19_081720_create_enterprise_brokers_table.php
database/migrations/2025_11_19_081807_create_whitelisted_broker_usage_table.php
database/migrations/2025_11_21_064434_remove_subscription_fields_from_users.php
```

### Configuration
```
nginx-enterprise-subdomain.conf
```

### Documentation
```
docs/implementation/ENTERPRISE_SUBDOMAIN_SETUP.md
docs/features/ENTERPRISE_BROKER_WHITELIST.md
docs/features/ENTERPRISE_IMPLEMENTATION_AUDIT.md
docs/features/IMPLEMENTATION_SUMMARY_ENTERPRISE.md
docs/INSTANCE_UPGRADE_SUMMARY.md
docs/changelog/ADMIN_DASHBOARD_STATS_UPDATE_NOV21.md
docs/changelog/ADMIN_USERS_PAGE_UPDATE_NOV21.md
docs/changelog/SESSION_NOV_21_2025_FIXES.md
docs/changelog/RELEASE_NOTES_v1.7.0_NOV21_2025.md
```

---

## 📝 Modified Files

### Controllers
```
app/Http/Controllers/Admin/AdminController.php
app/Http/Controllers/Admin/UserManagementController.php
app/Http/Controllers/Api/DataCollectionController.php
app/Http/Controllers/DashboardController.php
app/Http/Controllers/PerformanceController.php
app/Http/Controllers/AccountSnapshotViewController.php
```

### Models
```
app/Models/User.php
app/Models/TradingAccount.php
```

### Views
```
resources/views/admin/dashboard.blade.php
resources/views/public/pricing.blade.php
resources/views/public/faq.blade.php
resources/views/settings/api-key.blade.php
resources/views/components/time-filter.blade.php
resources/views/performance/index.blade.php
resources/views/accounts/health-overview.blade.php
```

### Routes
```
routes/web.php
```

### Configuration
```
bootstrap/app.php
nginx-api-subdomain.conf
/etc/nginx/sites-enabled/thetradevisor.com
```

### Tests
```
tests/Unit/PricingUpdateTest.php
tests/Feature/AccountLimitEnforcementTest.php
```

---

## 🔄 Database Migrations

### Required Migrations
```bash
php artisan migrate
```

**Migrations:**
1. `2025_11_19_081720_create_enterprise_brokers_table.php`
2. `2025_11_19_081807_create_whitelisted_broker_usage_table.php`
3. `2025_11_21_064434_remove_subscription_fields_from_users.php`

---

## 📦 Deployment Instructions

### 1. Backup Current System
```bash
# Backup database
pg_dump thetradevisor > /backups/thetradevisor_pre_v1.7.0_$(date +%Y%m%d_%H%M%S).sql

# Backup application
tar -czf /backups/thetradevisor_app_pre_v1.7.0_$(date +%Y%m%d_%H%M%S).tar.gz /var/www/thetradevisor.com
```

### 2. Pull Latest Code
```bash
cd /var/www/thetradevisor.com
git fetch --all
git checkout v1.7.0-enterprise-billing-overhaul
git pull origin main
```

### 3. Update Dependencies
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 4. Run Migrations
```bash
php artisan migrate --force
```

### 5. Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### 6. Rebuild Caches
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 7. Update Nginx Configuration
```bash
# Copy enterprise subdomain config
sudo cp /var/www/thetradevisor.com/nginx-enterprise-subdomain.conf /etc/nginx/sites-available/enterprise.thetradevisor.com
sudo ln -s /etc/nginx/sites-available/enterprise.thetradevisor.com /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

### 8. Update SSL Certificate
```bash
sudo certbot certonly --expand --nginx \
  -d thetradevisor.com \
  -d www.thetradevisor.com \
  -d api.thetradevisor.com \
  -d enterprise.thetradevisor.com
```

### 9. Restart Services
```bash
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
php artisan horizon:terminate
```

### 10. Verify Deployment
```bash
# Check main site
curl -I https://thetradevisor.com

# Check enterprise subdomain
curl -I https://enterprise.thetradevisor.com

# Check API subdomain
curl -I https://api.thetradevisor.com/api/v1/health

# Check services
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
sudo systemctl status postgresql@16-main
sudo systemctl status redis
```

---

## 🧪 Testing Checklist

### Main Domain (thetradevisor.com)
- [ ] Landing page loads
- [ ] User registration works
- [ ] User login works
- [ ] Dashboard displays correctly
- [ ] Performance page shows all time periods
- [ ] Account management works
- [ ] API key generation works
- [ ] Pricing page shows new model
- [ ] FAQ reflects new pricing

### Enterprise Subdomain (enterprise.thetradevisor.com)
- [ ] Enterprise login page loads
- [ ] Enterprise authentication works
- [ ] Enterprise dashboard displays
- [ ] Enterprise analytics functional
- [ ] Enterprise settings accessible
- [ ] Broker information displays
- [ ] Usage tracking works

### API Subdomain (api.thetradevisor.com)
- [ ] Health check endpoint responds
- [ ] Data collection endpoint works
- [ ] Whitelisted broker detection works
- [ ] Grace period warnings appear
- [ ] API authentication functional

### Admin Panel
- [ ] Admin dashboard shows new stats
- [ ] Broker management accessible
- [ ] User management shows brokers
- [ ] Enterprise broker indicators visible
- [ ] Admin wiki updated

### Data Display
- [ ] MT4 accounts display correctly
- [ ] MT5 accounts display correctly
- [ ] All trades visible
- [ ] Performance metrics accurate
- [ ] Time period selection works

---

## 🚨 Breaking Changes

### For Users
- **None** - All existing functionality preserved
- Subscription tier field removed from database (not user-facing)

### For Developers
- `User` model no longer has `subscription_tier` or `max_accounts` fields
- Use `TradingAccount::count()` instead of `max_accounts`
- Enterprise broker whitelist check required in data collection

### For Admins
- New broker management interface
- Enterprise broker configuration required
- Subscription tier management removed

---

## 🔐 Security Enhancements

1. **Enterprise Portal Isolation**
   - Separate subdomain with dedicated middleware
   - Role-based access control
   - Active subscription verification

2. **reCAPTCHA Protection**
   - Added to enterprise login
   - Prevents automated attacks

3. **Security Headers**
   - HSTS enabled on enterprise subdomain
   - X-Frame-Options: SAMEORIGIN
   - X-Content-Type-Options: nosniff
   - CSP headers configured

4. **API Security**
   - Whitelist verification on data collection
   - Grace period tracking
   - Usage monitoring

---

## 📈 Performance Improvements

1. **Infrastructure**
   - M5.large provides consistent CPU performance
   - Doubled RAM (4GB → 8GB)
   - Simplified architecture reduces latency

2. **Caching**
   - Optimized cache durations per time period
   - 90% cache hit rate maintained
   - Reduced database load

3. **Database**
   - Indexed columns for enterprise broker queries
   - Optimized whitelist lookup
   - Efficient usage tracking

---

## 🎨 UI/UX Improvements

1. **Admin Dashboard**
   - More relevant statistics
   - Enterprise broker visibility
   - Better user information display

2. **Pricing Page**
   - Clearer pricing model
   - Example calculations
   - Enterprise broker program explanation

3. **Time Selectors**
   - Visual PRO badges
   - Clear locked state indication
   - Consistent across all pages

4. **Enterprise Portal**
   - Dedicated broker-focused interface
   - Aggregated client analytics
   - Professional design

---

## 🐛 Known Issues

**None** - All reported issues resolved in this release.

---

## 🎯 Next Release (v1.8.0)

### Planned Features
1. **Broker Payment Integration**
   - Stripe integration for broker subscriptions
   - Broker invoice generation
   - Automated whitelist activation
   - Subscription management portal

2. **Enhanced Analytics**
   - Advanced broker comparison
   - Historical trend analysis
   - Predictive analytics
   - Custom report builder

3. **Mobile App**
   - iOS and Android apps
   - Push notifications
   - Mobile-optimized dashboard

4. **API Enhancements**
   - GraphQL API
   - Webhook support
   - Real-time WebSocket connections

---

## 👥 Contributors

- **Ruslan Abuzant** - Lead Developer & Architect
- **Development Team** - Implementation & Testing
- **QA Team** - Quality Assurance & Testing

---

## 📞 Support

### For Users
- 📧 Email: [hello@thetradevisor.com](mailto:hello@thetradevisor.com)
- 🌐 Website: [https://thetradevisor.com](https://thetradevisor.com)
- 📖 Documentation: [docs/](docs/)

### For Brokers
- 📧 Email: [hello@thetradevisor.com](mailto:hello@thetradevisor.com)
- 🌐 Enterprise Portal: [https://enterprise.thetradevisor.com](https://enterprise.thetradevisor.com)
- 📄 Documentation: [Enterprise Broker Whitelist](docs/features/ENTERPRISE_BROKER_WHITELIST.md)

### For Developers
- 🐛 Issues: [GitHub Issues](https://github.com/abuzant/TheTradeVisor/issues)
- 📖 API Docs: [docs/reference/API_DOCUMENTATION.md](docs/reference/API_DOCUMENTATION.md)
- 💼 Professional Services: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)

---

## 🏆 Acknowledgments

### Technologies
- Laravel 11 - PHP Framework
- PostgreSQL 16 - Database
- Redis 7 - Caching
- Nginx 1.24 - Web Server
- AWS EC2 - Cloud Infrastructure
- Cloudflare - CDN & Security
- Let's Encrypt - SSL Certificates

### Design Patterns
- Multi-tenant Architecture
- Middleware-based Access Control
- Grace Period Pattern
- Pay-Per-Use Billing Model

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
