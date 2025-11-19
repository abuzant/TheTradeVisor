# Helpers and Utilities Reference

This document provides a comprehensive reference for all helper classes and utility functions available in TheTradeVisor.

---

## NumberHelper

**Location:** `app/Helpers/NumberHelper.php`

### Purpose
Formats large numbers with K/M/B/T suffixes for compact display in UI components.

### Methods

#### `formatShort($number, $decimals = 1)`

Formats a number with appropriate suffix based on magnitude.

**Parameters:**
- `$number` (float) - The number to format
- `$decimals` (int) - Number of decimal places (default: 1)

**Returns:** `string` - Formatted number with suffix

**Examples:**
```php
use App\Helpers\NumberHelper;

NumberHelper::formatShort(500);           // "500.00"
NumberHelper::formatShort(1234);          // "1.2K"
NumberHelper::formatShort(196134.78);     // "196.1K"
NumberHelper::formatShort(1500000);       // "1.5M"
NumberHelper::formatShort(2500000000);    // "2.5B"
NumberHelper::formatShort(3700000000000); // "3.7T"

// Custom decimals
NumberHelper::formatShort(1234, 2);       // "1.23K"
NumberHelper::formatShort(1234, 0);       // "1K"
```

**Thresholds:**
- < 1,000: No suffix, 2 decimal places
- ≥ 1,000: K (Thousand)
- ≥ 1,000,000: M (Million)
- ≥ 1,000,000,000: B (Billion)
- ≥ 1,000,000,000,000: T (Trillion)

**Usage in Blade:**
```blade
{{ \App\Helpers\NumberHelper::formatShort($account->balance) }}
```

**Use Cases:**
- Metric cards with limited space
- Dashboard summaries
- Mobile responsive displays
- Charts and graphs labels

---

## Artisan Commands Reference

### Account Snapshot Management

#### Generate Snapshots
```bash
php artisan snapshots:generate
```
Manually triggers snapshot generation for all active accounts.

**Options:**
- `--account=ID` - Generate for specific account only
- `--force` - Force generation even if recent snapshot exists

**Schedule:** Runs automatically every 15 minutes via Laravel Scheduler

---

### Cache Management

#### Clear All Caches
```bash
php artisan cache:clear
```
Clears the application cache (Redis).

#### Clear View Cache
```bash
php artisan view:clear
```
Clears compiled Blade templates.

#### Clear Config Cache
```bash
php artisan config:clear
```
Clears cached configuration files.

#### Clear Route Cache
```bash
php artisan route:clear
```
Clears cached routes.

#### Clear All (Recommended for Deployment)
```bash
php artisan cache:clear && php artisan view:clear && php artisan config:clear && php artisan route:clear
```

---

### Queue Management

#### Start Queue Worker
```bash
php artisan queue:work --queue=default,trading-data,snapshots
```
Starts processing queued jobs.

**Options:**
- `--tries=3` - Number of retry attempts
- `--timeout=300` - Maximum execution time per job
- `--sleep=3` - Seconds to sleep when no jobs available

#### Monitor Queue with Horizon
```bash
php artisan horizon
```
Starts Laravel Horizon dashboard for queue monitoring.

**Access:** `https://thetradevisor.com/horizon`

---

### Database Management

#### Run Migrations
```bash
php artisan migrate
```
Runs pending database migrations.

#### Rollback Last Migration
```bash
php artisan migrate:rollback
```

#### Fresh Migration (⚠️ Destructive)
```bash
php artisan migrate:fresh --seed
```
Drops all tables and re-runs migrations with seeders.

---

### Development Tools

#### Tinker (Interactive Shell)
```bash
php artisan tinker
```
Opens Laravel's interactive REPL for testing code.

**Examples:**
```php
// Check user accounts
$user = User::find(22);
$user->tradingAccounts;

// Test deal queries
Deal::where('trading_account_id', 4)->count();

// Clear specific cache
Cache::forget('account_snapshots_4_days_7');
```

#### Telescope (Debugging)
```bash
php artisan telescope:install
```
Installs Laravel Telescope for debugging.

**Access:** `https://thetradevisor.com/telescope`

---

### Custom Commands

#### Process Trading Data
```bash
php artisan trading:process-data
```
Manually triggers trading data processing job.

#### Update Broker Statistics
```bash
php artisan brokers:update-stats
```
Recalculates broker performance statistics.

---

## Blade Components Reference

### Snapshot Widgets

#### Health Metrics
```blade
<x-snapshots.health-metrics 
    :current="$currentSnapshot" 
    :changes="$changes" 
    :currency="$account->account_currency" 
/>
```

**Props:**
- `current` - Current snapshot object
- `changes` - Array of 24h percentage changes
- `currency` - Account currency code

#### Balance & Equity Chart
```blade
<x-snapshots.balance-equity-chart 
    :chartData="$chartData" 
    :currency="$account->account_currency"
    :days="$days"
/>
```

**Props:**
- `chartData` - Array with labels, balance, equity data
- `currency` - Account currency code
- `days` - Time range (7, 30, 90, 180)

#### Max Drawdown Gauge
```blade
<x-snapshots.max-drawdown-gauge 
    :maxDrawdown="$statistics['max_drawdown']"
    :equity="$statistics['equity']"
    :currency="$account->account_currency"
/>
```

**Props:**
- `maxDrawdown` - Maximum drawdown percentage
- `equity` - Current equity value
- `currency` - Account currency code

#### Margin Stats
```blade
<x-snapshots.margin-stats
    :chartData="$chartData"
    :margin="$statistics['margin']"
    :currency="$account->account_currency"
/>
```

**Props:**
- `chartData` - Array with margin timeline data
- `margin` - Array with max, avg, current margin
- `currency` - Account currency code

---

## Model Scopes Reference

### Deal Model

#### `tradesOnly()`
Filters deals to only include actual trades (excludes balance operations).

```php
Deal::tradesOnly()->get();
```

#### `forAccount($accountId)`
Filters deals for a specific trading account.

```php
Deal::forAccount(4)->get();
```

#### `forPosition($positionId)`
Retrieves all deals for a specific position.

```php
Deal::forPosition('12345')->get();
```

#### `closedOnly()`
Filters to only closed positions (entry = 'out').

```php
Deal::closedOnly()->get();
```

---

## Service Classes Reference

### PerformanceMetricsService

**Location:** `app/Services/PerformanceMetricsService.php`

#### `getPlatformPerformanceMatrix($userId, $days = 30)`
Retrieves platform performance comparison data.

**Parameters:**
- `userId` (int) - User ID
- `days` (int) - Time range in days

**Returns:** Array of platform statistics

**Example:**
```php
$service = app(PerformanceMetricsService::class);
$matrix = $service->getPlatformPerformanceMatrix(22, 30);
```

---

## Configuration Files

### Important Config Files

#### `config/cache.php`
Redis cache configuration.

#### `config/queue.php`
Queue driver and connection settings.

#### `config/horizon.php`
Laravel Horizon configuration.

#### `config/services.php`
Third-party service credentials (GeoIP, AWS, etc.).

---

## Environment Variables

### Required Variables

```env
# Application
APP_NAME="TheTradeVisor"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://thetradevisor.com

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=thetradevisor
DB_USERNAME=thetradevisor_user
DB_PASSWORD=your_secure_password

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=redis

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis

# GeoIP
GEOIP_DATABASE_PATH=/path/to/GeoLite2-Country.mmdb
```

---

## Best Practices

### 1. Number Formatting
Always use `NumberHelper::formatShort()` for large numbers in UI:
```blade
❌ {{ number_format($balance, 2) }}
✅ {{ \App\Helpers\NumberHelper::formatShort($balance) }}
```

### 2. Cache Keys
Use descriptive, versioned cache keys:
```php
❌ Cache::put('data', $value);
✅ Cache::put('account_snapshots_' . $accountId . '_days_' . $days, $value);
```

### 3. Query Optimization
Always use scopes for common filters:
```php
❌ Deal::where('deal_category', 'trade')->where('symbol', '<>', '')->get();
✅ Deal::tradesOnly()->get();
```

### 4. Chart IDs
Always use unique IDs for charts:
```php
❌ <canvas id="myChart"></canvas>
✅ <canvas id="myChart_{{ uniqid() }}"></canvas>
```

---

## Troubleshooting

### Common Issues

#### Cache Not Clearing
```bash
# Clear all caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Restart queue workers
php artisan queue:restart
```

#### Charts Not Rendering
- Check for duplicate canvas IDs
- Verify Chart.js is loaded
- Check browser console for errors

#### Slow Queries
- Check Redis connection
- Verify indexes on database tables
- Review query logs in Telescope

---

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Chart.js Documentation](https://www.chartjs.org/docs/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Alpine.js Documentation](https://alpinejs.dev/start-here)

---

**Last Updated:** November 19, 2025  
**Version:** 1.6.0
