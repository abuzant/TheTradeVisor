# TheTradeVisor Codebase Audit - Part 1: Component Inventory

**Generated:** November 18, 2025  
**Scope:** Complete system audit with architectural analysis

---

## 1. Models (12 Total)

### Core Trading Models
1. **User** - Authentication & subscription management
2. **TradingAccount** - MT4/MT5 account data
3. **Position** - Open/closed positions (MT4/MT5)
4. **Deal** - Transaction history (MT5 primary)
5. **Order** - Pending orders (MT4/MT5)

### Supporting Models
6. **SymbolMapping** - Symbol normalization (EURUSD vs EUR/USD)
7. **CurrencyRate** - Exchange rate cache
8. **AccountSnapshot** - Historical account balance tracking
9. **HistoryUploadProgress** - Track EA historical data uploads
10. **DigestSubscription** - User email digest preferences
11. **ApiRequestLog** - API usage tracking
12. **RateLimitSetting** - Dynamic rate limit configuration

---

## 2. Controllers (38 Total)

### Public Controllers (2)
- **PublicController** - Landing, features, pricing, docs, download
- **LegalController** - Terms, privacy policy

### Authentication (9 - Laravel Breeze)
- AuthenticatedSessionController
- RegisteredUserController
- PasswordResetLinkController
- NewPasswordController
- EmailVerificationPromptController
- EmailVerificationNotificationController
- VerifyEmailController
- ConfirmablePasswordController
- PasswordController

### User Dashboard (8)
- **DashboardController** - Main dashboard, account details
- **PerformanceController** - Performance metrics
- **AnalyticsController** - Global analytics (all users)
- **BrokerAnalyticsController** - User's broker comparison
- **BrokerDetailsController** - Public broker pages (SEO)
- **CountryAnalyticsController** - Geographic analytics
- **TradesController** - Trade history listing
- **AccountManagementController** - Pause/unpause/delete accounts

### User Settings (4)
- **ProfileController** - Profile management
- **ProfileDigestController** - Digest preferences
- **ApiKeyController** - API key management
- **CurrencyController** - Display currency settings
- **MyDigestController** - Preview digest email

### Export (1)
- **ExportController** - CSV/PDF exports

### Admin (10)
- **AdminController** - Admin dashboard
- **UserManagementController** - User CRUD
- **AccountManagementController** - Account management (admin)
- **TradesController** - View all trades
- **ServiceController** - System service management
- **LogViewerController** - Log file viewer
- **SymbolMappingController** - Symbol management
- **SymbolManagementController** - Symbol normalization
- **RateLimitController** - Rate limit configuration
- **CircuitBreakerController** - Circuit breaker management
- **DigestControlController** - Digest system control
- **AdminWikiController** - Documentation viewer

### API Controllers (4)
- **Api\DataCollectionController** - EA data ingestion
- **Api\AccountController** - Account API
- **Api\TradeController** - Trade API
- **Api\AnalyticsController** - Analytics API

### Orphaned/Empty (2)
- **AccountController** - EMPTY (should be deleted)
- **LandingController** - Duplicate of PublicController

### Unused (1)
- **AnalyticsControllerOptimized** - Not referenced in routes

---

## 3. Middleware (18 Total)

### Authentication & Authorization
1. **IsAdmin** - Admin-only access
2. **RedirectIfAuthenticated** - Guest middleware
3. **ValidateApiKey** - API key validation

### Security & Protection
4. **VerifyRecaptcha** - reCAPTCHA validation
5. **DisableCsrf** - CSRF bypass (specific routes)
6. **PreventPageCaching** - Prevent user data caching
7. **TrustProxies** - Cloudflare proxy support

### Rate Limiting
8. **ApiRateLimiter** - API rate limiting
9. **RateLimitAnalytics** - Analytics endpoint limiting
10. **RateLimitExports** - Export endpoint limiting
11. **RateLimitBrokerAnalytics** - Broker analytics limiting

### Performance & Monitoring
12. **CircuitBreakerMiddleware** - Auto-disable under load
13. **QueryOptimizationMiddleware** - Log slow queries

### Session & Tracking
14. **ExtendedRememberMe** - Extended session duration
15. **TrackCountryMiddleware** - Track API country (GeoIP)
16. **TrackWebCountryMiddleware** - Track web country (GeoIP)

### API
17. **ForceJsonResponse** - Force JSON for API routes
18. **RedirectApiSubdomain** - Redirect non-EA traffic from api subdomain

---

## 4. Services (15 Total)

### Analytics & Metrics
1. **PerformanceMetricsService** - User performance calculations
2. **TradeAnalyticsService** - Trade statistics
3. **BrokerAnalyticsService** - Broker comparison
4. **PositionAggregationService** - MT5 position aggregation

### Data Processing
5. **TradingDataValidationService** - Validate EA data
6. **PlatformDetectionService** - Detect MT4/MT5
7. **CurrencyService** - Currency conversion
8. **GeoIPService** - IP to country lookup

### System Protection
9. **RateLimiterService** - Dynamic rate limiting
10. **CircuitBreakerService** - Circuit breaker logic
11. **CircuitBreaker** - Circuit breaker implementation

### Export & Reporting
12. **ExportService** - CSV/PDF generation
13. **DigestService** - Email digest builder
14. **DigestInsightService** - LLM insights generation
15. **DigestRenderService** - HTML digest rendering

---

## 5. Jobs (2 Active + 3 Backup)

### Active
1. **ProcessTradingData** - Process real-time EA data
2. **ProcessHistoricalData** - Process historical EA data

### Backup Files (Should be deleted)
- ProcessTradingData.php.backup
- ProcessHistoricalData.php.backup
- ProcessHistoricalData.php.old

---

## 6. Commands (16 Total)

### Data Management
1. **InjectFromRawData** - Inject from JSON backups
2. **ReprocessJsonData** - Reprocess stored JSON
3. **RestoreFromJson** - Restore from backups
4. **FixDealTimes** - Fix timestamp formats
5. **SyncSymbols** - Sync symbol mappings
6. **BackfillAccountSnapshots** - Create historical snapshots

### Platform Detection
7. **DetectAccountPlatforms** - Detect MT4/MT5

### Maintenance
8. **CleanupInactiveAccounts** - Remove inactive accounts
9. **UpdateGeoIPDatabase** - Update GeoIP database

### Admin Tools
10. **DeleteUser** - Delete user and data
11. **CheckApiKey** - Verify API key

### Digest System
12. **SendDigests** - Send email digests
13. **GenerateDigestHtml** - Generate digest HTML
14. **TestDigest** - Test digest generation

### Testing
15. **TestApiEndpoint** - Test API endpoints
16. **EmergencyRecovery** - Emergency system recovery

---

## 7. Traits (1 Total)

1. **Sortable** - Reusable sorting logic for tables
   - Used by: AccountManagementController, BrokerAnalyticsController, Admin\AdminController, Admin\UserManagementController, Admin\TradesController

---

## 8. Views (55+ Total)

### Layouts
- layouts/app.blade.php
- layouts/guest.blade.php
- layouts/navigation.blade.php

### Public Pages (10)
- public/landing, features, pricing, about, faq, contact, docs, api-docs, download, screenshots

### Authentication (6)
- auth/login, register, forgot-password, reset-password, verify-email, confirm-password

### User Dashboard (15)
- dashboard.blade.php
- performance.blade.php
- analytics/index.blade.php
- analytics/countries.blade.php
- analytics/locked.blade.php
- broker-analytics/index.blade.php
- broker-details/show.blade.php (public)
- trades/index.blade.php
- trades/symbol.blade.php
- accounts/index.blade.php
- account/show.blade.php
- settings/currency.blade.php
- profile/edit.blade.php
- digest/show.blade.php

### Admin (16)
- admin/dashboard
- admin/users/index, show, edit
- admin/accounts/index
- admin/trades/index
- admin/services
- admin/logs
- admin/symbols/index
- admin/rate-limits/index
- admin/circuit-breakers/index
- admin/digest-control
- admin/wiki/index

### Components (20+)
- application-logo, auth-session-status, broker-name, danger-button, date-range-filter, dropdown, dropdown-link, expandable-position-row, footer, google-analytics, input-error, input-label, modal, nav-link, platform-badge, primary-button, public-footer, public-layout, public-nav, responsive-nav-link, secondary-button, text-input

### Backup Files (Should be deleted)
- analytics/index-backup.blade.php
- analytics/index.blade.php.backup
- broker-analytics/index.blade.php.backup
- broker-details/show.blade.php.backup
- legal/terms.blade.php.old
- legal/privacy.blade.php.old

---

## 9. Migrations (41 Total)

### Core Tables
- users, cache, jobs, password_reset_tokens
- trading_accounts, positions, orders, deals
- symbol_mappings, currency_rates
- api_request_logs, rate_limit_settings
- digest_subscriptions, account_snapshots
- history_upload_progress

### OAuth (Passport)
- oauth_auth_codes, oauth_access_tokens, oauth_refresh_tokens, oauth_clients, oauth_device_codes

### Telescope
- telescope_entries

### Duplicate Migrations (Issue)
- 2025_10_30_170030_add_display_currency_to_users_table.php
- 2025_10_30_180726_add_display_currency_to_users_table.php
- 2025_10_30_180753_add_display_currency_to_users_table.php
**⚠️ THREE migrations for same column - should consolidate**

---

## 10. Routes Summary

### Web Routes (50+ endpoints)
- Public: 14 routes (landing, features, pricing, etc.)
- Auth: 10 routes (login, register, password reset)
- User Dashboard: 20+ routes
- Admin: 30+ routes

### API Routes (7 endpoints)
- POST /api/v1/data/collect (EA data ingestion)
- GET /api/v1/accounts
- GET /api/v1/accounts/{id}
- GET /api/v1/trades
- GET /api/v1/analytics/performance
- GET /api/health

---

## Summary Statistics

| Component | Count | Notes |
|-----------|-------|-------|
| Models | 12 | All active |
| Controllers | 38 | 3 orphaned/unused |
| Middleware | 18 | All active |
| Services | 15 | All active |
| Jobs | 2 | 3 backup files |
| Commands | 16 | All active |
| Traits | 1 | Widely used |
| Views | 55+ | 6 backup files |
| Migrations | 41 | 3 duplicates |
| Routes (Web) | 50+ | All active |
| Routes (API) | 7 | All active |

---

*Continued in Part 2: Architecture & Data Flow*
