# Platform Screenshots

This directory contains screenshots of TheTradeVisor platform features used in the marketing website.

## Required Screenshots

Please add the following screenshot files to this directory:

1. **account-performance.png** - Account Performance chart showing balance and equity trends
2. **open-positions.png** - Live Open Positions table with current trades
3. **closed-positions.png** - Recent Closed Positions history
4. **equity-curve.png** - Equity Curve chart (Last 30 Days)
5. **trading-by-symbol.png** - Trading by Symbol donut chart
6. **trading-by-hour.png** - Trading Activity by Hour bar chart
7. **trading-session-analysis.png** - Trading Session Analysis radar chart
8. **risk-analytics.png** - Risk Analytics Dashboard with bubble chart
9. **performance-leaderboards.png** - Performance Leaderboards section
10. **realtime-activity.png** - Real-Time Activity Monitor
11. **profit-loss-distribution.png** - Profit/Loss Distribution chart
12. **recent-activity.png** - Recent Activity feed
13. **trading-patterns.png** - Trading Patterns Analysis
14. **best-trading-days.png** - Best Trading Days section
15. **market-volatility.png** - Market Volatility Analysis

## Image Specifications

- **Format:** PNG (preferred) or JPG
- **Resolution:** Minimum 1200px width for best quality
- **File Size:** Optimize images to keep under 500KB each
- **Naming:** Use lowercase with hyphens (kebab-case)

## Usage

These screenshots are displayed on the `/screenshots` page of the marketing website.

Each screenshot is wrapped in a styled container with:
- Title and description
- Gray background with border
- Rounded corners and shadow
- Feature highlights below the image

## Adding New Screenshots

1. Save the screenshot file to this directory
2. Update `/resources/views/public/screenshots.blade.php` if adding new sections
3. Clear Laravel view cache: `php artisan view:clear`
4. Test the page at `/screenshots`
