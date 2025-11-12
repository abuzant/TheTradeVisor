# Changelog

All notable changes to TheTradeVisor will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.1] - 2025-11-10

### Added
- LIVE open positions table to dashboard for immediate exposure visibility
- Multi-account open positions overview with account-specific details
- Animated LIVE badge with gradient background for real-time feel
- Clickable account links in open positions table for easy navigation

### Enhanced
- Decimal formatting across all trading tables (removes unnecessary trailing zeros)
- Open positions table styling with compact padding (px-3 py-2)
- Currency display consistency (account pages show native currency, not hardcoded USD)
- Table sorting functionality maintained in new dashboard open positions view

### Fixed
- Free margin display showing incorrect USD currency instead of account currency
- Inconsistent decimal formatting between open positions, recent trades, and pending orders
- Visual hierarchy issues with table padding and spacing
- Missing account context in multi-account position display

### Technical
- Updated DashboardController to aggregate open positions from all user accounts
- Applied JavaScript regex and PHP rtrim for consistent decimal formatting
- Enhanced Alpine.js sorting for multi-account position data
- Improved caching strategy for dashboard open positions data

## [1.1.0] - 2025-11-08

### Added
- API authentication middleware with enhanced logging
- API rate limiting with configurable limits per IP and API key
- User deletion command (`php artisan user:delete`)
- API key validation command (`php artisan api:check`)
- API endpoint testing command (`php artisan api:test`)
- Comprehensive API documentation (API_DOCUMENTATION.md)
- GeoIP country detection for trading accounts
- Country analytics dashboard
- API request logging
- Duplicate EA instance detection support

### Fixed
- API rate limiter middleware not finding authenticated user
- ProcessTradingData job failing on NULL broker_name constraint
- Empty analytics pages due to stale cache
- Dashboard showing "N/A" for trade times
- Log file permission issues
- Multiple EA instances causing 401 errors with old API keys

### Changed
- Increased rate limits: IP (60→600 req/min), API key (120→600 req/min)
- Improved error logging for API key validation failures
- Enhanced deal time parsing to handle MT5 format (YYYY.MM.DD HH:MM:SS)
- Updated README with API documentation reference

### Security
- Added API key prefix logging (first 10 chars only) for security
- Improved API key validation with inactive user detection

## [1.0.0] - 2025-11-01

### Added
- Initial release of TheTradeVisor
- Trading account management system
- Performance analytics dashboard
- Trade history tracking and analysis
- Multi-broker integration support
- Symbol mapping and management
- Data export functionality (CSV, Excel, PDF)
- User management with role-based access control
- Real-time dashboard with live updates
- Multi-currency support with automatic conversion
- Advanced filtering and search capabilities
- Responsive design for mobile and tablet devices

### Features

#### Account Management
- Connect multiple trading accounts
- Support for various broker platforms
- Account synchronization and data import
- Account performance metrics

#### Analytics
- Comprehensive performance analytics
- Profit/loss tracking
- Win rate calculations
- Risk metrics and analysis
- Custom date range filtering
- Broker-specific analytics

#### Trade Management
- Complete trade history
- Trade filtering and search
- Trade details and metadata
- Position tracking
- Order management

#### Data Export
- Export to CSV format
- Export to Excel (XLSX)
- Export to PDF with customizable templates
- Filtered export options
- Scheduled exports

#### User Features
- User registration and authentication
- Profile management
- API key management
- Email notifications
- Activity logging

### Technical Stack
- Laravel 11.x
- PHP 8.2+
- Vue.js with Alpine.js
- TailwindCSS
- Vite build system
- Laravel Passport for API authentication
- Redis for caching and queues
- SQLite/MySQL/PostgreSQL support

### Security
- Laravel Passport OAuth2 authentication
- CSRF protection
- XSS protection
- SQL injection prevention
- Encrypted sensitive data
- Rate limiting
- Secure password hashing

## [1.0.0] - 2025-11-07

### Initial Release
- First production-ready release
- Core trading analytics platform
- Multi-account management
- Comprehensive reporting system

---

## Version History Guidelines

### Types of Changes
- **Added** - New features
- **Changed** - Changes in existing functionality
- **Deprecated** - Soon-to-be removed features
- **Removed** - Removed features
- **Fixed** - Bug fixes
- **Security** - Security improvements

### Version Format
- **Major** (X.0.0) - Incompatible API changes
- **Minor** (0.X.0) - New functionality (backward compatible)
- **Patch** (0.0.X) - Bug fixes (backward compatible)

---

[Unreleased]: https://github.com/yourusername/TheTradeVisor/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/yourusername/TheTradeVisor/releases/tag/v1.0.0

---

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)

For project support and inquiries:  
📧 [hello@thetradevisor.com](mailto:hello@thetradevisor.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)
