# Currency Display System

**Last Updated:** November 9, 2025

---

## Overview

TheTradeVisor uses a smart currency display system that shows values in the most appropriate currency based on context.

---

## The Rule

### 🔹 Single Account Context
When viewing **one specific account**, values are shown in that **account's native currency**.

**Examples:**
- Individual account detail page
- Account-specific charts
- Account row in tables

**Display:**
```
Account: Equiti Securities (AED Account)
Balance: AED 198,151.43
Equity:  AED 158,725.83
```

---

### 🔹 Multi-Account Context
When viewing **combined data from multiple accounts**, all values are **converted to USD**.

**Examples:**
- Dashboard totals
- Global analytics
- Performance across all accounts
- Broker comparisons

**Display:**
```
Dashboard Totals (All Accounts Combined)
Total Balance: USD 53,897.19
Total Equity:  USD 43,173.51
```

---

## Why This Approach?

### ✅ Advantages

1. **Clarity**: Single account views show native currency (no confusion)
2. **Comparability**: Multi-account views use USD (fair comparison)
3. **Simplicity**: No user settings needed
4. **Accuracy**: Real exchange rates from external API

### 📊 Example Scenario

**User has 2 accounts:**
- Account A: AED 198,151.43
- Account B: EUR 10,000.00

**Dashboard shows:**
```
Total Balance: USD 64,897.19
(AED 198,151.43 → USD 53,897.19 + EUR 10,000 → USD 11,000)
```

**Individual account pages show:**
```
Account A: AED 198,151.43
Account B: EUR 10,000.00
```

---

## Technical Implementation

### Currency Conversion Service

**File:** `app/Services/CurrencyService.php`

```php
// Convert amount from one currency to another
$usdAmount = $currencyService->convert(
    198151.43,  // Amount
    'AED',      // From currency
    'USD'       // To currency
);
// Returns: 53897.19
```

### Exchange Rates

**Source:** `https://api.exchangerate-api.com/v4/latest/`

**Caching:**
- Rates cached for 1 hour
- Stored in `currency_rates` database table
- Fallback: Returns 1.0 if API fails

**Supported Currencies:**
- USD, EUR, GBP, AED, JPY, CHF, AUD, CAD, NZD, SGD

### Example Rates (as of Nov 2025)
```
1 AED = 0.272 USD
1 EUR = 1.10 USD
1 GBP = 1.27 USD
```

---

## Implementation Locations

### Dashboard (`/dashboard`)
**File:** `app/Http/Controllers/DashboardController.php`

```php
// Multi-account view = Convert to USD
foreach ($accounts as $account) {
    $totalBalance += $currencyService->convert(
        $account->balance,
        $account->account_currency,
        'USD'
    );
}
```

### Individual Account (`/account/{id}`)
**File:** `app/Http/Controllers/DashboardController.php`

```php
// Single account view = Show native currency
$balance = $account->balance;
$currency = $account->account_currency; // e.g., 'AED'
```

### Broker Analytics (`/broker-analytics`)
**File:** `app/Services/BrokerAnalyticsService.php`

```php
// Multi-broker comparison = Show native currency per broker
// (Each broker typically has one primary currency)
$avgBalance = $accounts->avg('balance');
$currency = $primaryCurrency; // e.g., 'AED' for Equiti
```

---

## Views Using This System

### ✅ Multi-Account (USD)
- `/dashboard` - Dashboard totals
- `/analytics` - Global analytics
- `/performance` - Performance across all accounts

### ✅ Single Account (Native Currency)
- `/account/{id}` - Individual account detail
- `/accounts` - Account list (each row shows its own currency)
- `/broker-analytics` - Per-broker stats (native currency)

---

## Database Schema

### Trading Accounts Table
```sql
CREATE TABLE trading_accounts (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    account_currency VARCHAR(3) DEFAULT 'USD',
    balance DECIMAL(15,2),
    equity DECIMAL(15,2),
    ...
);
```

### Currency Rates Table
```sql
CREATE TABLE currency_rates (
    id SERIAL PRIMARY KEY,
    from_currency VARCHAR(3) NOT NULL,
    to_currency VARCHAR(3) NOT NULL,
    rate DECIMAL(10,6) NOT NULL,
    updated_at TIMESTAMP
);
```

---

## Testing Currency Conversion

### Via Tinker
```bash
php artisan tinker --execute="
\$service = app(\App\Services\CurrencyService::class);
echo 'AED to USD: ' . \$service->convert(198151.43, 'AED', 'USD') . PHP_EOL;
echo 'EUR to USD: ' . \$service->convert(10000, 'EUR', 'USD') . PHP_EOL;
"
```

### Expected Output
```
AED to USD: 53897.19
EUR to USD: 11000.00
```

---

## Future Enhancements

### Automatic Rate Updates
Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Update currency rates every hour
    $schedule->call(function () {
        app(\App\Services\CurrencyService::class)->updateAllRates();
    })->hourly();
}
```

### Additional Currencies
Add more currencies in `CurrencyService::getSupportedCurrencies()`:

```php
public function getSupportedCurrencies(): array
{
    return [
        'USD' => 'US Dollar',
        'EUR' => 'Euro',
        'GBP' => 'British Pound',
        'AED' => 'UAE Dirham',
        // Add more as needed
    ];
}
```

---

## Troubleshooting

### Issue: Conversion Not Working
**Symptom:** Dashboard shows AED values with USD label

**Solution:**
```bash
# 1. Clear cache
php artisan cache:clear

# 2. Test conversion
php artisan tinker --execute="
\$service = app(\App\Services\CurrencyService::class);
echo \$service->convert(100, 'AED', 'USD');
"

# 3. If returns 100 (no conversion), manually insert rate:
php artisan tinker --execute="
DB::table('currency_rates')->updateOrInsert(
    ['from_currency' => 'AED', 'to_currency' => 'USD'],
    ['rate' => 0.272, 'updated_at' => now()]
);
"
```

### Issue: Wrong Exchange Rate
**Solution:** Rates update automatically from API. To force update:

```bash
php artisan tinker --execute="
app(\App\Services\CurrencyService::class)->updateAllRates();
"
```

---

## Summary

✅ **Single account** = Native currency (AED, EUR, etc.)  
✅ **Multiple accounts** = Convert to USD  
✅ **Real exchange rates** from external API  
✅ **Automatic caching** for performance  
✅ **No user configuration** needed  

This system provides clarity, accuracy, and simplicity for all users.
