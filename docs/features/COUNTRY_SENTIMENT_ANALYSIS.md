# Country-Based Market Sentiment Analysis

## Overview

The Country-Based Market Sentiment Analysis provides real-time insights into trading behavior and market sentiment across different geographical regions. This feature analyzes buy/sell patterns, win rates, and profitability by country to help traders understand global market dynamics.

## Features

### 🌍 Global Sentiment Overview
- **Bullish Countries**: Regions with dominant buying pressure (>10% difference)
- **Bearish Countries**: Regions with dominant selling pressure (>10% difference)
- **Neutral Countries**: Balanced markets with minimal directional bias
- **Sentiment Scoring**: Quantified sentiment strength (0-50% scale)

### 📊 Country-Level Metrics
- **Trade Volume Analysis**: Total trades per country
- **Buy/Sell Ratios**: Directional trading preferences
- **Win Rate Comparison**: Success rates by region
- **Profitability Metrics**: Total and average profit per country
- **Market Participation**: Active trading accounts by country

### 🎯 Interactive Visualizations
- **Sentiment Heatmap**: Color-coded country performance
- **Buy/Sell Stacked Chart**: Visual representation of directional flow
- **Country Comparison Table**: Detailed metrics side-by-side
- **Real-Time Updates**: Data refreshes every 5 minutes

## Data Points Analyzed

### Primary Metrics
```php
[
    'country' => 'US',                    // Country code
    'country_code' => 'US',               // Standardized code
    'total_trades' => 1250,               // Total trade count
    'buy_trades' => 750,                  // Buy trade count
    'sell_trades' => 500,                 // Sell trade count
    'buy_percentage' => 60.0,             // Buy trade percentage
    'sell_percentage' => 40.0,            // Sell trade percentage
    'winning_trades' => 750,              // Profitable trades
    'losing_trades' => 500,               // Loss-making trades
    'win_rate' => 60.0,                   // Win rate percentage
    'total_profit' => 12500.50,           // Total profit in display currency
    'total_volume' => 2500000.00,         // Total trading volume
    'avg_profit' => 10.00,                // Average profit per trade
    'sentiment' => 'bullish',             // Market sentiment
    'sentiment_score' => 10.0,            // Sentiment strength (0-50%)
    'profit_per_trade' => 10.00           // Profit per trade ratio
]
```

### Sentiment Calculation Logic
```php
// Bullish: Buy percentage > Sell percentage + 10%
if ($buyPercentage > $sellPercentage + 10) {
    $sentiment = 'bullish';
    $sentimentScore = min(($buyPercentage - $sellPercentage) / 2, 50);
}
// Bearish: Sell percentage > Buy percentage + 10%
elseif ($sellPercentage > $buyPercentage + 10) {
    $sentiment = 'bearish';
    $sentimentScore = min(($sellPercentage - $buyPercentage) / 2, 50);
}
// Neutral: Balanced market
else {
    $sentiment = 'neutral';
    $sentimentScore = 0;
}
```

## Technical Implementation

### Backend Service
- **Service Class**: `PerformanceMetricsService::getCountryBasedMarketSentiment()`
- **Cache Strategy**: 5-minute Redis cache for performance
- **Data Source**: Deals table with country detection from trading accounts
- **Currency Conversion**: Real-time conversion to display currency

### Database Query
```sql
SELECT 
    ta.detected_country,
    COUNT(d.id) as total_trades,
    SUM(CASE WHEN d.type = 'buy' THEN 1 ELSE 0 END) as buy_trades,
    SUM(CASE WHEN d.type = 'sell' THEN 1 ELSE 0 END) as sell_trades,
    SUM(CASE WHEN d.profit > 0 THEN 1 ELSE 0 END) as winning_trades,
    SUM(d.profit) as total_profit,
    SUM(d.volume) as total_volume
FROM deals d
JOIN trading_accounts ta ON d.trading_account_id = ta.id
WHERE d.time >= NOW() - INTERVAL '30 days'
  AND ta.detected_country IS NOT NULL
  AND d.type IN ('buy', 'sell')
GROUP BY ta.detected_country
HAVING COUNT(d.id) >= 5
ORDER BY total_trades DESC
```

### Frontend Components
- **Chart.js Integration**: Stacked bar chart for buy/sell visualization
- **Responsive Design**: Mobile-friendly table and chart layouts
- **Real-Time Updates**: AJAX refresh every 5 minutes
- **Interactive Tooltips**: Detailed country metrics on hover

## API Endpoints

### Performance Metrics (includes country sentiment)
```
GET /api/performance?days=30
Authorization: Bearer {api_key}
```

### Response Structure
```json
{
    "country_sentiment": [
        {
            "country": "US",
            "country_code": "US",
            "total_trades": 1250,
            "buy_percentage": 60.0,
            "sell_percentage": 40.0,
            "win_rate": 60.0,
            "total_profit": 12500.50,
            "sentiment": "bullish",
            "sentiment_score": 10.0
        }
    ]
}
```

## Use Cases

### For Traders
1. **Market Timing**: Identify global market sentiment shifts
2. **Regional Analysis**: Understand trading patterns by geography
3. **Sentiment Following**: Align with dominant regional trends
4. **Contrarian Trading**: Spot opportunities in overly biased regions

### For Analysts
1. **Global Market Health**: Assess worldwide trading activity
2. **Regional Performance**: Compare market efficiency by country
3. **Sentiment Indicators**: Track market psychology changes
4. **Risk Management**: Diversify across regional sentiments

### For Brokers
1. **Client Insights**: Understand regional client behavior
2. **Market Expansion**: Identify high-activity regions
3. **Risk Assessment**: Monitor concentrated regional exposure
4. **Product Development**: Tailor offerings to regional preferences

## Performance Considerations

### Caching Strategy
- **Cache Duration**: 5 minutes for real-time freshness
- **Cache Key**: `performance.{account_hash}.{days}.{currency}`
- **Invalidation**: Automatic on new trade data
- **Memory Usage**: ~1MB per cached dataset

### Query Optimization
- **Indexed Columns**: `detected_country`, `time`, `type`
- **Partitioning**: Monthly partitions for large datasets
- **Query Time**: <100ms for 30-day analysis
- **Concurrent Users**: Supports 500+ simultaneous requests

## Future Enhancements

### Planned Features
1. **Real-Time Sentiment Map**: Interactive world map visualization
2. **Sentiment Alerts**: Notifications for significant sentiment changes
3. **Historical Trends**: Sentiment evolution over time
4. **Economic Event Correlation**: Link sentiment to news events
5. **Predictive Analytics**: Forecast sentiment shifts

### Data Expansion
1. **City-Level Analysis**: Granular geographic insights
2. **Timezone Performance**: Trading patterns by time zone
3. **Currency Pair Preferences**: Regional symbol preferences
4. **Broker Distribution**: Platform usage by country

## Implementation Credits

**Lead Developer**: [Ruslan Abuzant](https://abuzant.com)  
**Architecture**: Laravel 11 + PostgreSQL + Redis  
**Frontend**: Chart.js + TailwindCSS + Alpine.js  
**Analytics**: Advanced statistical analysis with currency conversion  

---

*Last Updated: November 12, 2025*  
*Version: 1.0.0*


---

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)  
❤️ From Palestine to the world with Love

For project support and inquiries:  
�� [hello@thetradevisor.com](mailto:hello@thetradevisor.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)

