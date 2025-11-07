# 🌍 GeoIP Country Analytics

Track and analyze trading activity by country using MaxMind GeoLite2 database.

## 📊 Overview

The GeoIP Country Analytics feature provides insights into:
- Which countries your trading accounts are from
- Trading patterns by country
- Popular symbols per country
- Broker preferences by region
- Win rates and profitability by location

## ✨ Features

### Automatic Country Detection

- **IP-based Geolocation**: Automatically detects country from API requests
- **Real-time Tracking**: Updates country data with each API call
- **Privacy-Friendly**: Only stores country-level data (not city or precise location)
- **GDPR Compliant**: Minimal data collection with legitimate interest basis

### Analytics Dashboards

#### Top Trading Countries

View on `/analytics` page:
- Country rankings by trade volume
- Number of accounts per country
- Total balance by country
- Flag emoji display

#### Detailed Country Analytics

Visit `/analytics/countries` for:
- Complete country rankings
- Trade counts and win rates
- Profit/loss statistics
- Average profit per trade
- Account distribution

### Country-Specific Insights

- **Trading Patterns**: Day of week preferences
- **Popular Symbols**: Most traded pairs by country
- **Broker Preferences**: Which brokers are popular in each region
- **Performance Metrics**: Win rates and profitability by country

## 🚀 Setup

### 1. Get MaxMind License Key

1. Sign up at https://www.maxmind.com/en/geolite2/signup
2. Create a license key
3. It's **completely free**!

### 2. Configure Environment

Add to your `.env` file:
```env
MAXMIND_LICENSE_KEY=your_license_key_here
```

### 3. Download GeoIP Database

```bash
php artisan geoip:update
```

Expected output:
```
Downloading GeoIP database...
Fetching from MaxMind...
Download complete. Extracting...
✓ GeoIP database updated successfully!
```

### 4. Verify Installation

```bash
php artisan tinker
```

```php
$service = app(\App\Services\GeoIPService::class);
$service->getCountryFromIP('8.8.8.8');
// Should return: ['country_code' => 'US', 'country_name' => 'United States']
```

## 📈 How It Works

### Data Collection Flow

```
1. Trading account makes API request
   ↓
2. Middleware captures IP address
   ↓
3. GeoIPService looks up country (cached 24h)
   ↓
4. Country data saved to database
   ↓
5. Analytics updated in real-time
```

### Database Tables

#### trading_accounts
```sql
country_code    VARCHAR(2)   -- ISO country code (e.g., 'US')
country_name    VARCHAR(100) -- Full name (e.g., 'United States')
last_ip         VARCHAR(45)  -- Last seen IP address
last_seen_at    TIMESTAMP    -- Last activity time
```

#### api_request_logs
```sql
id                  BIGINT
trading_account_id  BIGINT
user_id             BIGINT
ip_address          VARCHAR(45)
country_code        VARCHAR(2)
country_name        VARCHAR(100)
endpoint            VARCHAR(255)
method              VARCHAR(10)
created_at          TIMESTAMP
```

## 🎯 Using the Analytics

### View Top Countries

**URL**: `/analytics`

**Section**: "Top Trading Countries"

Shows:
- 🏳️ Country flag
- Country name
- Number of accounts
- Total balance

**Click**: "View Detailed Analytics →" for more

### Detailed Country View

**URL**: `/analytics/countries`

**Displays**:
- Complete rankings
- Trade statistics
- Win rate indicators
- Profit/loss metrics
- Flag emojis

**Filters**:
- Time period (7, 30, 90 days)
- Sort by various metrics

### API Endpoints

#### Get Top Countries

```http
GET /api/analytics/countries?days=30
```

**Response**:
```json
[
  {
    "country_code": "US",
    "country_name": "United States",
    "account_count": 15,
    "total_trades": 1234,
    "win_rate": 65.5,
    "total_profit": 5678.90,
    "avg_profit": 4.60
  }
]
```

#### Country by Symbol

```http
GET /api/analytics/country/symbol/EURUSD?days=30
```

#### Country by Broker

```http
GET /api/analytics/country/broker/ICMarkets?days=30
```

## 🔄 Automatic Updates

### Scheduled Database Updates

The GeoIP database automatically updates **every 2 weeks**:

```php
// routes/console.php
Schedule::command('geoip:update')
    ->cron('0 2 */14 * *')  // Every 14 days at 2:00 AM
    ->name('update-geoip-database');
```

### Manual Update

```bash
php artisan geoip:update
```

### Check Schedule

```bash
php artisan schedule:list
```

## 🔒 Privacy & Security

### Data Collected

✅ **What we collect**:
- Country code (e.g., "US")
- Country name (e.g., "United States")
- IP address (for lookup only, not stored permanently)

❌ **What we DON'T collect**:
- City or precise location
- GPS coordinates
- Personal information
- Browsing history

### GDPR Compliance

- **Legal Basis**: Legitimate interest (Article 6(1)(f))
- **Data Minimization**: Only country-level data
- **Purpose**: Analytics and service improvement
- **User Rights**: Data can be deleted on request

### Security Measures

- IP addresses used for lookup only
- Country data anonymized and aggregated
- No personally identifiable information stored
- Secure database with encryption

## 🎨 Customization

### Disable Country Tracking

Remove middleware from `bootstrap/app.php`:

```php
// Comment out this line:
// \App\Http\Middleware\TrackCountryMiddleware::class,
```

### Change Update Frequency

Edit `routes/console.php`:

```php
Schedule::command('geoip:update')
    ->cron('0 2 */7 * *')  // Every 7 days instead of 14
```

### Custom Analytics

Create custom queries:

```php
use App\Models\TradingAccount;

// Get accounts by country
$usAccounts = TradingAccount::where('country_code', 'US')->get();

// Get top countries
$topCountries = TradingAccount::select('country_code', 'country_name')
    ->selectRaw('COUNT(*) as count')
    ->whereNotNull('country_code')
    ->groupBy('country_code', 'country_name')
    ->orderByDesc('count')
    ->limit(10)
    ->get();
```

## 🐛 Troubleshooting

### No Country Data Showing

**Problem**: Analytics page shows "No country data available"

**Solutions**:
1. Wait for API requests from trading accounts
2. Check if GeoIP database exists:
   ```bash
   ls -la storage/app/geoip/GeoLite2-Country.mmdb
   ```
3. Verify middleware is active
4. Check logs: `tail -f storage/logs/laravel.log`

### Database Update Failed

**Problem**: `geoip:update` command fails

**Solutions**:
1. Check MaxMind license key in `.env`
2. Verify internet connection
3. Check disk space: `df -h`
4. Check permissions:
   ```bash
   chmod 755 storage/app/geoip
   chown -R www-data:www-data storage/app/geoip
   ```

### Incorrect Country Detection

**Problem**: Wrong country showing for account

**Causes**:
- VPN or proxy usage
- Shared hosting IP
- CDN or load balancer

**Note**: This is expected behavior when users use VPNs

## 📊 Performance

### Caching

- **GeoIP Lookups**: Cached for 24 hours per IP
- **Analytics Queries**: Cached for 1 hour per user
- **Database**: Indexed on country_code for fast queries

### Expected Performance

- GeoIP lookup: <1ms (cached), ~10ms (uncached)
- Analytics query: 50-200ms
- Page load impact: <10ms

## 🔮 Future Enhancements

Potential additions:
- 🗺️ World map visualization
- 🕐 Time zone analysis
- 📊 Country comparison tool
- 📄 Export country analytics to CSV/PDF
- 📱 Mobile app integration
- 🔔 Real-time country tracking

## 📚 Related Documentation

- [Installation Guide](../installation.md)
- [Analytics Features](analytics.md)
- [API Documentation](../api/overview.md)
- [GeoIP Implementation Details](../GEOIP_IMPLEMENTATION.md)

## 🆘 Support

### Getting Help

- **Documentation**: See [GEOIP_SETUP_GUIDE.md](../GEOIP_SETUP_GUIDE.md)
- **Email**: hello@thetradevisor.com
- **GitHub Issues**: https://github.com/abuzant/TheTradeVisor/issues

### Reporting Issues

Include:
1. Error message from logs
2. Steps to reproduce
3. Expected vs actual behavior
4. System information (OS, PHP version)

---

**Last Updated**: November 7, 2025  
**Feature Version**: 1.0.0  
**Status**: Production Ready ✅
