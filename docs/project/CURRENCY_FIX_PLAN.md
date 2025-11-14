# Currency Display Fix Plan

## Rule:
- **Multi-Account Context** → Always convert to USD
- **Single Account Context** → Use native currency

## Files to Fix:

### CRITICAL - Multi-Account Contexts (MUST convert to USD):

1. **PerformanceController** ❌
   - Shows performance across ALL user accounts
   - Currently uses $displayCurrency
   - FIX: Remove $displayCurrency, always use USD, convert all values

2. **Dashboard** ❌  
   - Shows totals across ALL accounts
   - Currently uses $displayCurrency
   - FIX: Remove $displayCurrency, always use USD, convert all values

3. **BrokerAnalyticsController** ❌
   - Compares multiple brokers
   - Currently uses $displayCurrency
   - FIX: Remove $displayCurrency, always use USD, convert all values

4. **AnalyticsController** ❌
   - Global analytics across all users
   - Already hardcoded to USD (GOOD\!)
   - VERIFY: Make sure all conversions happen

5. **TradesController->index()** ❌
   - Shows trades from ALL user accounts
   - Currently uses $display_currency
   - FIX: Convert to USD if multiple accounts

6. **ExportController** ❌
   - Exports from multiple accounts
   - Currently uses $displayCurrency
   - FIX: Always export in USD for multi-account

### OK - Single Account or Settings:

1. **Settings/CurrencyController** ✅
   - User preference setting
   - Keep as is

2. **Individual account pages** ✅
   - Show native currency
   - Keep as is

## Implementation Strategy:

1. Remove $displayCurrency parameter from multi-account contexts
2. Always pass 'USD' to services
3. Ensure CurrencyService converts properly
4. Update views to show "USD" label
5. Test each controller after fix
