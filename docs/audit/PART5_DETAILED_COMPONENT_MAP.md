# TheTradeVisor Codebase Audit - Part 5: Detailed Component Mapping

**Generated:** November 18, 2025

---

## 🗺️ COMPLETE COMPONENT RELATIONSHIP MAP

### User Authentication Flow

```
┌─────────────────────────────────────────────────────────────┐
│                    AUTHENTICATION CHAIN                      │
└─────────────────────────────────────────────────────────────┘

Browser Request
    │
    ├─► GET /login
    │   └─► Auth\AuthenticatedSessionController::create()
    │       └─► View: auth/login.blade.php
    │
    ├─► POST /login
    │   └─► Auth\AuthenticatedSessionController::store()
    │       ├─► Validate credentials
    │       ├─► Auth::attempt()
    │       ├─► Update User.last_login_at
    │       ├─► Regenerate session
    │       └─► Redirect to /dashboard
    │
    ├─► GET /register
    │   └─► Auth\RegisteredUserController::create()
    │       └─► View: auth/register.blade.php
    │
    ├─► POST /register
    │   └─► Auth\RegisteredUserController::store()
    │       ├─► Validate input
    │       ├─► Create User (auto-generates api_key)
    │       ├─► Send verification email
    │       └─► Redirect to /dashboard
    │
    └─► POST /logout
        └─► Auth\AuthenticatedSessionController::destroy()
            ├─► Auth::logout()
            ├─► Invalidate session
            └─► Redirect to /
```

---

### EA Data Ingestion Flow (Complete Trace)

```
┌─────────────────────────────────────────────────────────────┐
│                  EA DATA INGESTION CHAIN                     │
└─────────────────────────────────────────────────────────────┘

MT4/MT5 Expert Advisor
    │
    │ HTTP POST Request
    │ URL: https://api.thetradevisor.com/api/v1/data/collect
    │ Headers: X-API-Key: tvsr_xxxxx
    │ Body: JSON {account, positions, orders, deals}
    │
    ▼
┌─────────────────────────────────────────────────────────────┐
│ MIDDLEWARE STACK (Executed in Order)                        │
├─────────────────────────────────────────────────────────────┤
│ 1. RedirectApiSubdomain                                     │
│    └─► Checks if request is from EA (has X-API-Key)        │
│        └─► If not EA: redirect to main site                 │
│                                                              │
│ 2. ForceJsonResponse                                        │
│    └─► Sets Accept: application/json                        │
│                                                              │
│ 3. ValidateApiKey                                           │
│    ├─► Extract X-API-Key header                            │
│    ├─► Query: User::where('api_key', $key)->first()        │
│    ├─► Check: is_active = true                             │
│    ├─► Set: auth()->setUser($user)                         │
│    └─► Abort 401 if invalid                                │
│                                                              │
│ 4. ApiRateLimiter                                           │
│    ├─► RateLimiterService::shouldLimit($user, 'api')       │
│    ├─► Check cache: rate_limit.{user_id}.api               │
│    ├─► Increment counter                                    │
│    └─► Abort 429 if exceeded                               │
│                                                              │
│ 5. TrackCountryMiddleware                                   │
│    ├─► GeoIPService::lookup($request->ip())                │
│    ├─► Get country_code, country_name, city, timezone      │
│    └─► Store in request attributes                         │
└─────────────────────────────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────────────────────┐
│ CONTROLLER: Api\DataCollectionController::collect()         │
├─────────────────────────────────────────────────────────────┤
│ Step 1: Validate Request                                    │
│    └─► TradingDataValidationService::validate($data)       │
│        ├─► Check required fields                            │
│        ├─► Validate data types                              │
│        ├─► Sanitize inputs                                  │
│        └─► Return validated data                            │
│                                                              │
│ Step 2: Detect Platform                                     │
│    └─► PlatformDetectionService::detect($data)             │
│        ├─► Check for position_identifier (MT5 netting)     │
│        ├─► Check for multiple positions per symbol (MT4)   │
│        ├─► Analyze deal structure                           │
│        └─► Return: platform_type, account_mode              │
│                                                              │
│ Step 3: Find or Create Trading Account                      │
│    ├─► Generate hash: sha256(account_number + server)      │
│    ├─► Query: TradingAccount::where('account_hash', $hash) │
│    ├─► If not found: Create new account                    │
│    └─► Update account data:                                │
│        ├─► broker_name, broker_server                       │
│        ├─► balance, equity, margin, profit                  │
│        ├─► country_code, country_name (from middleware)     │
│        ├─► platform_type, account_mode                      │
│        └─► last_sync_at = now()                            │
│                                                              │
│ Step 4: Store Raw JSON (Backup)                            │
│    └─► Storage::put("raw_data/{account_id}/{timestamp}.json")│
│                                                              │
│ Step 5: Dispatch Job                                        │
│    └─► ProcessTradingData::dispatch($account, $data)       │
│        └─► Queue: default                                   │
│                                                              │
│ Step 6: Return Response                                     │
│    └─► JSON: {success: true, message: "Data received"}     │
└─────────────────────────────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────────────────────┐
│ JOB: ProcessTradingData (Queued, Async)                     │
├─────────────────────────────────────────────────────────────┤
│ Step 1: Process Positions                                   │
│    ├─► Loop through $data['positions']                     │
│    ├─► For each position:                                   │
│    │   ├─► Check if MT5 netting                            │
│    │   │   └─► PositionAggregationService::aggregate()     │
│    │   │       ├─► Find all deals for position_identifier  │
│    │   │       ├─► Sum volumes (IN - OUT)                  │
│    │   │       ├─► Calculate average price                 │
│    │   │       └─► Update position record                  │
│    │   │                                                    │
│    │   ├─► Find or create Position record                  │
│    │   │   └─► Position::updateOrCreate([                  │
│    │   │       'trading_account_id' => $account->id,       │
│    │   │       'ticket' => $position['ticket'],            │
│    │   │   ], [                                            │
│    │   │       'symbol' => $position['symbol'],            │
│    │   │       'type' => $position['type'],                │
│    │   │       'volume' => $position['volume'],            │
│    │   │       'open_price' => $position['open_price'],    │
│    │   │       'current_price' => $position['price'],      │
│    │   │       'profit' => $position['profit'],            │
│    │   │       'is_open' => true,                          │
│    │   │   ])                                              │
│    │   │                                                    │
│    │   └─► Normalize symbol                                │
│    │       └─► SymbolMapping::normalize($symbol)           │
│    │                                                        │
│    └─► Mark missing positions as closed                    │
│        └─► Position::where('is_open', true)                │
│            ->whereNotIn('ticket', $currentTickets)         │
│            ->update(['is_open' => false])                  │
│                                                              │
│ Step 2: Process Orders                                      │
│    ├─► Loop through $data['orders']                        │
│    └─► Order::updateOrCreate([...])                        │
│                                                              │
│ Step 3: Process Deals                                       │
│    ├─► Loop through $data['deals']                         │
│    ├─► For each deal:                                       │
│    │   ├─► Categorize deal                                 │
│    │   │   ├─► If symbol empty: deal_category = 'balance' │
│    │   │   └─► Else: deal_category = 'trade'              │
│    │   │                                                    │
│    │   ├─► Detect activity type                            │
│    │   │   ├─► If type = 'balance': activity_type = 'deposit'│
│    │   │   ├─► If type = 'credit': activity_type = 'credit'│
│    │   │   └─► Else: activity_type = 'trade'              │
│    │   │                                                    │
│    │   └─► Deal::updateOrCreate([                          │
│    │       'trading_account_id' => $account->id,           │
│    │       'ticket' => $deal['ticket'],                    │
│    │   ], [                                                │
│    │       'position_id' => $deal['position_id'],          │
│    │       'symbol' => $deal['symbol'],                    │
│    │       'type' => $deal['type'],                        │
│    │       'entry' => $deal['entry'],                      │
│    │       'volume' => $deal['volume'],                    │
│    │       'price' => $deal['price'],                      │
│    │       'profit' => $deal['profit'],                    │
│    │       'time' => $deal['time'],                        │
│    │       'deal_category' => $category,                   │
│    │       'activity_type' => $activityType,               │
│    │   ])                                                  │
│    │                                                        │
│    └─► Link deals to positions                             │
│        └─► Update Position.deal_count                      │
│                                                              │
│ Step 4: Update Account Snapshot                            │
│    └─► AccountSnapshot::create([                           │
│        'trading_account_id' => $account->id,               │
│        'balance' => $account->balance,                     │
│        'equity' => $account->equity,                       │
│        'profit' => $account->profit,                       │
│        'snapshot_at' => now(),                             │
│    ])                                                      │
│                                                              │
│ Step 5: Log Success                                         │
│    └─► Log::info("Processed data for account {$id}")      │
└─────────────────────────────────────────────────────────────┘
```

---

### Dashboard Loading Flow (Complete Trace)

```
┌─────────────────────────────────────────────────────────────┐
│                    DASHBOARD LOADING CHAIN                   │
└─────────────────────────────────────────────────────────────┘

Browser Request: GET /dashboard
    │
    ▼
┌─────────────────────────────────────────────────────────────┐
│ MIDDLEWARE STACK                                             │
├─────────────────────────────────────────────────────────────┤
│ 1. auth (Laravel default)                                   │
│    └─► Check if user is authenticated                       │
│                                                              │
│ 2. verified (Laravel default)                               │
│    └─► Check if email is verified                           │
│                                                              │
│ 3. PreventPageCaching                                       │
│    └─► Set headers:                                         │
│        ├─► Cache-Control: no-store, no-cache               │
│        ├─► Pragma: no-cache                                 │
│        └─► Expires: 0                                       │
│                                                              │
│ 4. ExtendedRememberMe                                       │
│    └─► Extend session lifetime to 30 days                  │
│                                                              │
│ 5. TrackWebCountryMiddleware                                │
│    └─► GeoIPService::lookup($request->ip())                │
│                                                              │
│ 6. QueryOptimizationMiddleware                              │
│    └─► Start query timer                                    │
│        └─► Log queries > 1 second                           │
└─────────────────────────────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────────────────────┐
│ CONTROLLER: DashboardController::index()                    │
├─────────────────────────────────────────────────────────────┤
│ Step 1: Get User's Trading Accounts                         │
│    └─► $accounts = TradingAccount::where('user_id', auth()->id())│
│        ->where('is_active', true)                           │
│        ->where('is_paused', false)                          │
│        ->with(['positions' => function($q) {                │
│            $q->where('is_open', true)->limit(10);           │
│        }])                                                  │
│        ->get();                                             │
│                                                              │
│ Step 2: Calculate Account Totals (Multi-Currency)          │
│    ├─► Loop through accounts                                │
│    ├─► For each account:                                    │
│    │   ├─► CurrencyService::convert(                       │
│    │   │       $account->balance,                           │
│    │   │       $account->account_currency,                  │
│    │   │       'USD'                                        │
│    │   │   )                                                │
│    │   └─► Sum: totalBalance, totalEquity, totalProfit     │
│    │                                                        │
│    └─► Result: All totals in USD                           │
│                                                              │
│ Step 3: Get Recent Closed Trades                           │
│    └─► $recentTrades = Deal::whereIn(                      │
│            'trading_account_id',                            │
│            $accounts->pluck('id')                           │
│        )                                                    │
│        ->closedTrades()                                     │
│        ->with('tradingAccount')                             │
│        ->orderBy('time', 'desc')                            │
│        ->limit(20)                                          │
│        ->get();                                             │
│                                                              │
│ Step 4: Calculate Performance Metrics                       │
│    ├─► Total trades: $recentTrades->count()                │
│    ├─► Winning trades: $recentTrades->where('profit', '>', 0)│
│    ├─► Win rate: (winning / total) * 100                   │
│    ├─► Total profit: $recentTrades->sum('profit')          │
│    └─► Convert to USD using CurrencyService                │
│                                                              │
│ Step 5: Get Open Positions Summary                         │
│    ├─► Count open positions per account                    │
│    ├─► Calculate total unrealized profit                   │
│    └─► Convert to USD                                      │
│                                                              │
│ Step 6: Prepare Chart Data                                 │
│    └─► Get daily profit for last 30 days                   │
│        └─► Deal::closedTrades()                            │
│            ->dateRange(now()->subDays(30), now())          │
│            ->selectRaw('DATE(time) as date, SUM(profit) as profit')│
│            ->groupBy('date')                                │
│            ->get();                                         │
│                                                              │
│ Step 7: Return View                                         │
│    └─► return view('dashboard', [                          │
│        'accounts' => $accounts,                             │
│        'totalBalance' => $totalBalance,                     │
│        'totalEquity' => $totalEquity,                       │
│        'totalProfit' => $totalProfit,                       │
│        'recentTrades' => $recentTrades,                     │
│        'winRate' => $winRate,                               │
│        'chartData' => $chartData,                           │
│    ]);                                                      │
└─────────────────────────────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────────────────────┐
│ VIEW: resources/views/dashboard.blade.php                   │
├─────────────────────────────────────────────────────────────┤
│ @extends('layouts.app')                                     │
│                                                              │
│ Components Used:                                             │
│ ├─► @include('components.google-analytics')                │
│ ├─► <x-broker-name :broker="$account->broker_name" />      │
│ ├─► <x-platform-badge :platform="$account->platform_type" />│
│ └─► @include('components.footer')                          │
│                                                              │
│ JavaScript:                                                  │
│ └─► Chart.js for profit trend visualization                │
└─────────────────────────────────────────────────────────────┘
```

---

### Analytics Calculation Flow

```
┌─────────────────────────────────────────────────────────────┐
│                  ANALYTICS CALCULATION CHAIN                 │
└─────────────────────────────────────────────────────────────┘

Browser Request: GET /analytics/30
    │
    ▼
RateLimitAnalytics Middleware
    └─► Check: 10 requests/minute per user
    │
    ▼
CircuitBreakerMiddleware
    └─► Check: Is analytics service open?
    │
    ▼
AnalyticsController::index($days = 30)
    │
    ├─► Check Cache
    │   ├─► Key: "analytics.{$days}.{$userId}"
    │   ├─► TTL: 15 minutes
    │   └─► If HIT: return cached data
    │
    ├─► Get User's Accounts
    │   └─► $accountIds = auth()->user()
    │       ->tradingAccounts()
    │       ->pluck('id');
    │
    ├─► Get Closed Trades
    │   └─► $deals = Deal::whereIn('trading_account_id', $accountIds)
    │       ->closedTrades()
    │       ->dateRange(now()->subDays($days), now())
    │       ->with('tradingAccount')
    │       ->limit(10000)
    │       ->get();
    │
    ├─► TradeAnalyticsService::calculate($deals)
    │   │
    │   ├─► Basic Metrics
    │   │   ├─► Total trades: $deals->count()
    │   │   ├─► Winning trades: $deals->where('profit', '>', 0)->count()
    │   │   ├─► Losing trades: $deals->where('profit', '<', 0)->count()
    │   │   ├─► Win rate: (winning / total) * 100
    │   │   └─► Break-even trades: $deals->where('profit', '=', 0)->count()
    │   │
    │   ├─► Profit Metrics
    │   │   ├─► Total profit: $deals->sum('profit')
    │   │   ├─► Total commission: $deals->sum('commission')
    │   │   ├─► Total swap: $deals->sum('swap')
    │   │   ├─► Net profit: profit + commission + swap
    │   │   ├─► Average profit: total / count
    │   │   ├─► Average win: winning deals avg
    │   │   ├─► Average loss: losing deals avg
    │   │   └─► Profit factor: gross profit / gross loss
    │   │
    │   ├─► Symbol Analysis
    │   │   ├─► Group by normalized_symbol
    │   │   ├─► For each symbol:
    │   │   │   ├─► Total trades
    │   │   │   ├─► Win rate
    │   │   │   ├─► Total profit
    │   │   │   ├─► Average profit
    │   │   │   └─► Total volume
    │   │   └─► Sort by profit DESC
    │   │
    │   ├─► Daily Profit Trend
    │   │   ├─► Group by DATE(time)
    │   │   ├─► Sum profit per day
    │   │   └─► Calculate cumulative profit
    │   │
    │   ├─► Best/Worst Trades
    │   │   ├─► Best: $deals->sortByDesc('profit')->take(10)
    │   │   └─► Worst: $deals->sortBy('profit')->take(10)
    │   │
    │   ├─► Hold Time Analysis
    │   │   ├─► For each closed deal:
    │   │   │   ├─► Find IN deal (entry)
    │   │   │   ├─► Find OUT deal (exit)
    │   │   │   └─► Calculate: OUT.time - IN.time
    │   │   ├─► Average hold time
    │   │   ├─► Min hold time
    │   │   └─► Max hold time
    │   │
    │   └─► Return analytics array
    │
    ├─► Currency Conversion (Multi-Account)
    │   └─► CurrencyService::convertAll($analytics, 'USD')
    │       ├─► Loop through all profit values
    │       ├─► Convert from account currency to USD
    │       └─► Return converted values
    │
    ├─► Cache Results
    │   └─► Cache::put($key, $analytics, 900) // 15 min
    │
    └─► Return View
        └─► view('analytics.index', compact('analytics'))
```

---

### Export Generation Flow

```
┌─────────────────────────────────────────────────────────────┐
│                    EXPORT GENERATION CHAIN                   │
└─────────────────────────────────────────────────────────────┘

Browser Request: GET /export/trades/csv
    │
    ▼
RateLimitExports Middleware
    └─► Check: 5 requests/minute per user
    │
    ▼
CircuitBreakerMiddleware
    └─► Check: Is exports service open?
    │
    ▼
ExportController::exportTradesCsv(Request $request)
    │
    ├─► Get Filters
    │   ├─► $dateFrom = $request->get('date_from')
    │   ├─► $dateTo = $request->get('date_to')
    │   ├─► $symbol = $request->get('symbol')
    │   └─► $accountId = $request->get('account_id')
    │
    ├─► Build Query
    │   └─► $deals = Deal::whereIn('trading_account_id', $accountIds)
    │       ->closedTrades()
    │       ->when($dateFrom, fn($q) => $q->where('time', '>=', $dateFrom))
    │       ->when($dateTo, fn($q) => $q->where('time', '<=', $dateTo))
    │       ->when($symbol, fn($q) => $q->where('symbol', $symbol))
    │       ->when($accountId, fn($q) => $q->where('trading_account_id', $accountId))
    │       ->with(['tradingAccount'])
    │       ->orderBy('time', 'desc')
    │       ->limit(10000) // Hard limit
    │       ->get();
    │
    ├─► ExportService::generateCsv($deals)
    │   │
    │   ├─► Prepare Headers
    │   │   └─► ['Date', 'Symbol', 'Type', 'Volume', 'Price', 'Profit', 'Account']
    │   │
    │   ├─► Format Data
    │   │   └─► Loop through deals:
    │   │       ├─► Format date: Y-m-d H:i:s
    │   │       ├─► Normalize symbol
    │   │       ├─► Format type: BUY/SELL
    │   │       ├─► Format volume: 2 decimals
    │   │       ├─► Format price: 5 decimals
    │   │       ├─► Format profit: 2 decimals
    │   │       └─► Add account number
    │   │
    │   ├─► Generate CSV
    │   │   ├─► Create temporary file
    │   │   ├─► Write headers
    │   │   ├─► Write rows
    │   │   └─► Return file path
    │   │
    │   └─► Return CSV content
    │
    └─► Return Download Response
        └─► response()->download($filePath, 'trades.csv')
            ->deleteFileAfterSend(true);
```

---

## 🔗 SERVICE INTERACTION MAP

```
Controllers
    │
    ├─► PerformanceMetricsService
    │   └─► CurrencyService
    │       └─► CurrencyRate Model
    │
    ├─► TradeAnalyticsService
    │   └─► CurrencyService
    │       └─► CurrencyRate Model
    │
    ├─► BrokerAnalyticsService
    │   └─► CurrencyService
    │       └─► CurrencyRate Model
    │
    ├─► PositionAggregationService
    │   └─► PlatformDetectionService
    │       └─► TradingAccount Model
    │
    ├─► TradingDataValidationService
    │   └─► (No dependencies)
    │
    ├─► GeoIPService
    │   └─► MaxMind GeoIP2 Database
    │
    ├─► RateLimiterService
    │   ├─► RateLimitSetting Model
    │   └─► Cache
    │
    ├─► CircuitBreakerService
    │   └─► Cache
    │
    ├─► ExportService
    │   └─► DomPDF (for PDF exports)
    │
    └─► DigestService
        ├─► DigestInsightService
        │   └─► External LLM API
        └─► DigestRenderService
            └─► Blade Templates
```

---

## 📊 DATABASE QUERY PATTERNS

### Most Common Queries

**1. Get User's Accounts**
```php
TradingAccount::where('user_id', auth()->id())
    ->where('is_active', true)
    ->where('is_paused', false)
    ->get();
```
**Used in:** Dashboard, Analytics, Exports, Trades
**Frequency:** Every page load
**Optimization:** Index on (user_id, is_active, is_paused)

---

**2. Get Closed Trades**
```php
Deal::whereIn('trading_account_id', $accountIds)
    ->where('entry', 'out')
    ->whereIn('type', ['0', '1', 'buy', 'sell'])
    ->orderBy('time', 'desc')
    ->limit(100)
    ->get();
```
**Used in:** Dashboard, Analytics, Trades, Exports
**Frequency:** Very high
**Optimization:** Index on (trading_account_id, entry, type, time)

---

**3. Symbol Normalization**
```php
SymbolMapping::where('raw_symbol', $symbol)->first();
```
**Used in:** Every deal/position display
**Frequency:** Extremely high
**Optimization:** Cache all mappings in memory

---

**4. Currency Conversion**
```php
CurrencyRate::where('from_currency', $from)
    ->where('to_currency', $to)
    ->first();
```
**Used in:** Dashboard, Analytics, Exports
**Frequency:** High
**Optimization:** Cache rates for 1 hour

---

**5. Position Aggregation (MT5)**
```php
Deal::where('position_id', $positionId)
    ->where('trading_account_id', $accountId)
    ->orderBy('time', 'asc')
    ->get();
```
**Used in:** EA data processing
**Frequency:** Every EA sync
**Optimization:** Index on (position_id, trading_account_id, time)

---

*End of Component Mapping*
