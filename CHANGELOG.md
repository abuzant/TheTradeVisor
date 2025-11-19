# Changelog

All notable changes to TheTradeVisor will be documented in this file.

## [2.0.1] - 2025-11-19

### 🎉 Major Features

#### Enterprise Dashboard Enhancements
- **Timeframe Selector**: Added 7, 30, 90, and 180-day timeframe options with intelligent caching (24-hour cache duration)
- **Advanced Metrics**: New profit factor, best trade, and worst trade cards with beautiful styling
- **Account Identification**: Best/worst trade cards now show account number, currency, and platform badges
- **Symbol Normalization**: Hover over symbols to reveal raw broker-specific names while displaying clean normalized versions
- **Performance Optimization**: Implemented 24-hour view caching for all timeframe queries
- **Data Accuracy**: Fixed currency conversion to properly aggregate multi-currency accounts to USD

#### Broker Landing Page (`/for-brokers`)
- **New Marketing Page**: Professional landing page designed to attract enterprise broker clients
- **Value Proposition**: Clear messaging highlighting free trader access and competitive advantages
- **Feature Showcase**: Interactive screenshot gallery with 6 platform screenshots
- **Click-to-Navigate Gallery**: Beautiful carousel with thumbnail navigation, arrow buttons, and keyboard support
- **Pricing Section**: Transparent $999/month pricing with feature breakdown
- **Contact Integration**: Multiple CTAs with gradient buttons and email protection
- **SEO Optimized**: Open Graph tags and proper meta descriptions

#### Footer Unification
- **Unified Component**: Created single `unified-footer.blade.php` component
- **Consistent Branding**: All pages now use the same footer structure
- **Broker Link**: Added prominent "For Brokers" link in footer navigation
- **Clean Migration**: Replaced both `footer.blade.php` and `public-footer.blade.php`

### 🔒 Security Enhancements
- **Email Protection**: Implemented Cloudflare-style email obfuscation on `/for-brokers` and `/contact` pages
- **Bot Prevention**: Added `data-cfemail` attributes to protect against email scrapers

### 🎨 UI/UX Improvements
- **Button Enhancements**: Beautiful gradient buttons with hover animations, lift effects, and animated icons
- **Screenshot Gallery**: Interactive carousel with fade transitions, thumbnail grid, and progress counter
- **Responsive Design**: All new components fully responsive across mobile, tablet, and desktop
- **Visual Hierarchy**: Improved spacing, shadows, and color gradients throughout

### 🐛 Bug Fixes
- **Currency Display**: Fixed enterprise dashboard to show aggregated values in USD with proper conversion
- **Chart Accuracy**: Corrected balance/equity chart to use only latest snapshot per account per day
- **Symbol Table Layout**: Fixed "dancing table" issue with fixed column widths for symbol hover effects
- **CSS Loading**: Fixed Tailwind CSS loading on broker landing page using CDN
- **HTML Escaping**: Resolved DOCTYPE and comment escaping issues

### 🚀 Performance
- **Query Optimization**: All enterprise dashboard queries now cached for 24 hours
- **Smart Defaults**: Invalid timeframe selections automatically default to 30 days
- **Efficient Aggregation**: Improved data aggregation logic for multi-account scenarios

### 📝 Technical Changes
- Added route: `GET /for-brokers` → `PublicController@forBrokers`
- New controller method: `PublicController::forBrokers()`
- Enhanced `EnterpriseController` with timeframe validation and extended caching
- Created reusable footer component system
- Implemented JavaScript-based screenshot carousel with smooth transitions

### 🎯 What's Next
- Homepage updates to promote broker features
- Features page enhancements covering both user and broker capabilities
- Additional screenshot placeholders for future content

---

## [2.0.0] - Previous Release
- Initial enterprise features
- Trading analytics dashboard
- Multi-account support
- Performance tracking

---

**Full Changelog**: https://github.com/yourusername/thetradevisor/compare/v2.0.0...v2.0.1
