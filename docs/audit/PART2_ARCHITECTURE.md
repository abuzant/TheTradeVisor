# TheTradeVisor Codebase Audit - Part 2: Architecture & Data Flow

**Generated:** November 18, 2025

---

## System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         EXTERNAL SYSTEMS                         │
├─────────────────────────────────────────────────────────────────┤
│  MT4/MT5 EA  │  Web Browser  │  API Clients  │  Cloudflare CDN │
└───────┬──────┴───────┬───────┴───────┬───────┴────────┬─────────┘
        │              │               │                │
        │              │               │                │
┌───────▼──────────────▼───────────────▼────────────────▼─────────┐
│                    NGINX LOAD BALANCER (443)                     │
│                     + Cloudflare Proxy                           │
└───────┬──────────────┬───────────────┬────────────────┬─────────┘
        │              │               │                │
┌───────▼──────┐ ┌─────▼──────┐ ┌─────▼──────┐ ┌──────▼──────┐
│ Backend 8081 │ │ Backend    │ │ Backend    │ │ Backend     │
│              │ │ 8082       │ │ 8083       │ │ 8084        │
└───────┬──────┘ └─────┬──────┘ └─────┬──────┘ └──────┬──────┘
        │              │               │                │
        └──────────────┴───────────────┴────────────────┘
                              │
                    ┌─────────▼──────────┐
                    │   PHP 8.3-FPM      │
                    │   (5 pools)        │
                    └─────────┬──────────┘
                              │
                    ┌─────────▼──────────┐
                    │  LARAVEL 11 APP    │
                    └─────────┬──────────┘
                              │
                    ┌─────────▼──────────┐
                    │  PostgreSQL 16     │
                    │  (thetradevisor)   │
                    └────────────────────┘
```

---

## Data Flow Diagrams

### 1. EA Data Ingestion Flow

```
MT4/MT5 EA
    │
    │ POST /api/v1/data/collect
    │ Headers: X-API-Key
    │
    ▼
RedirectApiSubdomain Middleware
    │
    ▼
ValidateApiKey Middleware
    │ (validates api_key from users table)
    │
    ▼
ApiRateLimiter Middleware
    │ (checks rate limits per user)
    │
    ▼
TrackCountryMiddleware
    │ (GeoIP lookup, updates trading_accounts.country_code)
    │
    ▼
DataCollectionController::collect()
    │
    ├─► TradingDataValidationService::validate()
    │   (validates JSON structure, required fields)
    │
    ├─► PlatformDetectionService::detect()
    │   (detects MT4/MT5, netting/hedging)
    │
    ├─► Find/Create TradingAccount
    │   (by account_number + broker_server hash)
    │
    ├─► Dispatch ProcessTradingData Job
    │   (queued for async processing)
    │
    └─► Return success response
            │
            ▼
    ProcessTradingData Job (Queue)
            │
            ├─► Process Positions
            │   └─► PositionAggregationService (MT5 netting)
            │
            ├─► Process Orders
            │
            ├─► Process Deals
            │   ├─► Categorize (trade vs balance)
            │   └─► Link to positions via position_id
            │
            └─► Update TradingAccount
                (balance, equity, profit, last_sync_at)
```

### 2. User Dashboard Flow

```
User Browser
    │
    │ GET /dashboard
    │
    ▼
PreventPageCaching Middleware
    │ (Cache-Control: no-store)
    │
    ▼
ExtendedRememberMe Middleware
    │ (extends session to 30 days)
    │
    ▼
TrackWebCountryMiddleware
    │ (GeoIP tracking for web users)
    │
    ▼
QueryOptimizationMiddleware
    │ (logs slow queries >1s)
    │
    ▼
DashboardController::index()
    │
    ├─► Get user's trading accounts
    │   (TradingAccount::where('user_id', auth()->id()))
    │
    ├─► Get open positions per account
    │   (Position::where('is_open', true)->limit(10))
    │
    ├─► Get recent deals (closed trades)
    │   (Deal::closedTrades()->limit(20))
    │
    ├─► Calculate totals
    │   ├─► CurrencyService::convert() (to USD)
    │   └─► Sum balance, equity, profit
    │
    └─► Return dashboard view
        (with accounts, positions, deals, totals)
```

### 3. Analytics Flow (Global)

```
User Browser
    │
    │ GET /analytics/{days}
    │
    ▼
RateLimitAnalytics Middleware
    │ (10 requests/minute per user)
    │
    ▼
CircuitBreakerMiddleware
    │ (checks if analytics service is open)
    │
    ▼
AnalyticsController::index()
    │
    ├─► Check cache (key: analytics.{days}.{user_id})
    │   │ TTL: 15 minutes
    │   │
    │   └─► Cache HIT? Return cached data
    │
    ├─► Get user's accounts
    │   (TradingAccount::where('user_id', auth()->id()))
    │
    ├─► Get deals for date range
    │   (Deal::closedTrades()->dateRange()->limit(10000))
    │
    ├─► TradeAnalyticsService::calculate()
    │   ├─► Win rate
    │   ├─► Profit factor
    │   ├─► Average win/loss
    │   ├─► Best/worst trades
    │   ├─► Symbol performance
    │   └─► Daily profit trend
    │
    ├─► CurrencyService::convert() (all to USD)
    │
    ├─► Cache results (15 min)
    │
    └─► Return analytics view
```

### 4. Broker Details (Public SEO Page)

```
Google Bot / User Browser
    │
    │ GET /broker/{broker}
    │ (NO authentication required)
    │
    ▼
BrokerDetailsController::show()
    │
    ├─► Check cache (key: broker.public.{broker}.180d)
    │   │ TTL: 4 hours
    │   │
    │   └─► Cache HIT? Return cached data
    │
    ├─► Get ALL users' data for this broker
    │   (TradingAccount::where('broker_name', $broker))
    │
    ├─► Get deals from last 180 days
    │   (Deal::closedTrades()->dateRange()->limit(50000))
    │
    ├─► BrokerAnalyticsService::aggregatePublicData()
    │   ├─► Total trades
    │   ├─► Win rate
    │   ├─► Top symbols
    │   ├─► Top countries
    │   ├─► Daily profit trend
    │   └─► Active traders count
    │
    ├─► Cache results (4 hours)
    │
    └─► Return broker-details view
        (with SEO meta tags, JSON-LD, OpenGraph)
```

### 5. Export Flow

```
User Browser
    │
    │ GET /export/trades/csv
    │
    ▼
RateLimitExports Middleware
    │ (5 requests/minute per user)
    │
    ▼
CircuitBreakerMiddleware
    │ (checks if exports service is open)
    │
    ▼
ExportController::exportTradesCsv()
    │
    ├─► Get user's accounts
    │
    ├─► Get deals (max 10,000 records)
    │   (Deal::closedTrades()->limit(10000))
    │
    ├─► ExportService::generateCsv()
    │   ├─► Format data
    │   ├─► Apply currency conversion
    │   └─► Generate CSV
    │
    └─► Return CSV download response
```

---

## Model Relationships

```
User (1)
  │
  ├─► hasMany TradingAccount (N)
  │       │
  │       ├─► hasMany Position (N)
  │       │       │
  │       │       └─► hasMany Deal (N) [via position_id]
  │       │
  │       ├─► hasMany Order (N)
  │       │
  │       ├─► hasMany Deal (N)
  │       │
  │       └─► hasOne HistoryUploadProgress (1)
  │
  ├─► hasMany DigestSubscription (N)
  │
  └─► hasMany ApiRequestLog (N)


Deal (Core Trading Entity)
  │
  ├─► belongsTo TradingAccount
  │
  ├─► Linked by position_id (MT5 netting)
  │   └─► Multiple deals per position
  │       ├─► IN deals (entry)
  │       └─► OUT deals (exit) ← Used for closed trades
  │
  └─► Linked by ticket (MT4/MT5 hedging)


SymbolMapping (Normalization)
  │
  └─► normalize('EURUSD.a') → 'EURUSD'
      normalize('EUR/USD') → 'EURUSD'
      normalize('EURUSDm') → 'EURUSD'
```

---

## Critical Data Paths

### Path 1: MT5 Closed Trade Identification
```
Deal::closedTrades()
  → where('entry', 'out')
  → whereIn('type', ['0', '1', 'buy', 'sell'])
  → This is the CORRECT way to get closed trades
```

**Why?**
- MT5 uses position-based system
- Multiple deals per position (IN, OUT, INOUT)
- OUT deals = position closed with final profit
- Works for both netting and hedging modes

### Path 2: Currency Conversion
```
Single Account Context:
  → Use account's native currency (no conversion)
  → Example: Account page shows AED

Multi-Account Context:
  → Convert all to USD
  → CurrencyService::convert()
  → Uses cached exchange rates (currency_rates table)
  → Example: Dashboard totals in USD
```

### Path 3: Symbol Normalization
```
Raw Symbol (from EA):
  → 'EURUSD.a', 'EUR/USD', 'EURUSDm', etc.
  
SymbolMapping::normalize():
  → Checks symbol_mappings table
  → Returns normalized symbol: 'EURUSD'
  → Falls back to raw symbol if no mapping
  
Used in:
  → Analytics (group by symbol)
  → Trade listings
  → Symbol-specific pages
```

### Path 4: Rate Limiting
```
Request
  │
  ▼
RateLimiterService::shouldLimit()
  │
  ├─► Check rate_limit_settings table
  │   (dynamic limits per endpoint/user)
  │
  ├─► Check cache (key: rate_limit.{user_id}.{endpoint})
  │   (tracks request count)
  │
  ├─► Increment counter
  │
  └─► Return true if exceeded
          │
          ▼
      HTTP 429 Response
      (Retry-After header)
```

### Path 5: Circuit Breaker
```
Request
  │
  ▼
CircuitBreakerMiddleware
  │
  ├─► CircuitBreakerService::isOpen('analytics')
  │   │
  │   ├─► Check cache (circuit_breaker_state)
  │   │
  │   ├─► Check metrics (error rate, response time)
  │   │
  │   └─► State: CLOSED | OPEN | HALF_OPEN
  │
  ├─► If OPEN:
  │   └─► Return cached/fallback data
  │
  └─► If CLOSED:
      └─► Process request normally
          │
          ├─► Success: Record success metric
          │
          └─► Failure: Record failure metric
              (auto-opens circuit if threshold exceeded)
```

---

## Service Dependencies

```
PerformanceMetricsService
  └─► CurrencyService

BrokerAnalyticsService
  └─► CurrencyService

TradeAnalyticsService
  └─► CurrencyService

PositionAggregationService
  └─► PlatformDetectionService

DataCollectionController
  ├─► TradingDataValidationService
  ├─► PlatformDetectionService
  └─► GeoIPService (via TrackCountryMiddleware)

DigestService
  ├─► DigestInsightService (LLM)
  └─► DigestRenderService (HTML)

ExportController
  └─► ExportService
```

---

## Authentication & Authorization Flow

```
Login Request
  │
  ▼
AuthenticatedSessionController::store()
  │
  ├─► Validate credentials
  │
  ├─► Auth::attempt()
  │
  ├─► Update last_login_at
  │
  ├─► Regenerate session
  │
  └─► Redirect to /dashboard


API Request
  │
  ▼
ValidateApiKey Middleware
  │
  ├─► Extract X-API-Key header
  │
  ├─► User::where('api_key', $key)->first()
  │
  ├─► Check is_active
  │
  └─► Set auth()->user()


Admin Request
  │
  ▼
IsAdmin Middleware
  │
  ├─► Check auth()->check()
  │
  ├─► Check auth()->user()->is_admin
  │
  └─► Abort 403 if not admin
```

---

*Continued in Part 3: Issues & Improvements*
