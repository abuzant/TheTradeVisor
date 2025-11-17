# TheTradeVisor

[![Laravel](https://img.shields.io/badge/Laravel-11-red.svg?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-blue.svg?logo=php)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED.svg?logo=docker)](https://www.docker.com)
[![Docker Compose](https://img.shields.io/badge/Docker%20Compose-Ready-2496ED.svg?logo=docker)](https://docs.docker.com/compose)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-336791.svg?logo=postgresql)](https://www.postgresql.org)
[![Redis](https://img.shields.io/badge/Redis-7-DC382D.svg?logo=redis)](https://redis.io)
[![Nginx](https://img.shields.io/badge/Nginx-1.24-009639.svg?logo=nginx)](https://nginx.org)
[![License](https://img.shields.io/badge/License-Proprietary-green.svg)](LICENSE)
[![Production Ready](https://img.shields.io/badge/Status-Production%20Ready-brightgreen.svg)](https://thetradevisor.com)
[![One-Click Deploy](https://img.shields.io/badge/One%20Click%20Deploy-00A8E1.svg)](#-docker-deployment)
[![Analytics](https://img.shields.io/badge/Analytics-Real--time-FF6B6B.svg)](https://thetradevisor.com/analytics)
[![MT4/MT5](https://img.shields.io/badge/MT4%2FMT5-Integration-4CAF50.svg)](docs/reference/API_DOCUMENTATION.md)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-3.x-38B2AC.svg?logo=tailwind-css)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0.svg?logo=alpine.js)](https://alpinejs.dev)
[![Chart.js](https://img.shields.io/badge/Chart.js-4.x-FF6384.svg?logo=chart.js)](https://chartjs.org)
[![Horizon](https://img.shields.io/badge/Laravel-Horizon-FF2D20.svg?logo=laravel)](https://laravel.com/docs/horizon)
[![Telescope](https://img.shields.io/badge/Laravel-Telescope-FF2D20.svg?logo=laravel)](https://laravel.com/docs/telescope)
[![Circuit Breaker](https://img.shields.io/badge/Circuit%20Breaker-Active-success.svg)]()
[![Rate Limiting](https://img.shields.io/badge/Rate%20Limiting-Active-success.svg)]()
[![Caching](https://img.shields.io/badge/Redis%20Cache-90%25%20Hit%20Rate-DC382D.svg)]()
[![Monitoring](https://img.shields.io/badge/Health%20Monitoring-Active-success.svg)]()
[![GeoIP](https://img.shields.io/badge/GeoIP-Analytics-blue.svg)]()
[![Cloudflare](https://img.shields.io/badge/Cloudflare-CDN-F38020.svg?logo=cloudflare)](https://cloudflare.com)
[![AWS](https://img.shields.io/badge/AWS-EC2-FF9900.svg?logo=amazon-aws)](https://aws.amazon.com)

**Professional Trading Analytics Platform** - Comprehensive MT4/MT5 trading data analysis, performance tracking, and broker comparison system.

---

## 🚀 Overview

TheTradeVisor is an enterprise-grade trading analytics platform that aggregates, analyzes, and visualizes trading data from MetaTrader 4 and MetaTrader 5 platforms. Built with Laravel 11 and modern web technologies, it provides traders with deep insights into their trading performance, broker comparisons, and global market analytics.

### Key Features

- 📊 **Real-time Analytics** - Live trading performance metrics and statistics
- 🌍 **Global Market Data** - Aggregated analytics from traders worldwide
- 🏦 **Broker Comparison** - Detailed broker performance analysis
- 📈 **Performance Tracking** - Individual and portfolio-level tracking with equity curves, drawdown analysis, and symbol performance
- 🔒 **Enterprise Security** - Multi-layer protection and monitoring
- ⚡ **High Performance** - Redis caching, query optimization, circuit breakers
- 🌐 **GeoIP Analytics** - Country-based trading insights
- 📱 **Responsive Design** - Beautiful UI across all devices
- 💰 **Flexible Pricing** - Free tier + pay-per-account model ($9.99 one-time)
- 🔑 **API Key Management** - Secure MT4/MT5 integration with account limits

---

## 📚 Documentation

### Getting Started

- [Installation Guide](docs/getting-started/INSTALLATION.md) - Complete setup instructions
- [Configuration Guide](docs/README.md#-quick-start) - Quick start guide
- [MT4/MT5 EA Installation](docs/guides/MT4_EA_INSTALLATION.md) - Expert Advisor setup guide

### Core Features

- [GeoIP Analytics](docs/features/geoip-analytics.md) - Geographic insights
- [Trading Analytics](docs/README.md#-core-features) - Analytics system overview
- [Performance Analytics](docs/features/performance.md) - Trade analysis, equity curves, and drawdown
- [Dashboard Live Positions](docs/features/DASHBOARD_LIVE_POSITIONS.md) - Real-time open positions and recent closed positions
- [Broker Comparison](docs/README.md#-core-features) - Broker analytics features

### System Architecture

- [Architecture Overview](docs/development/architecture.md) - System design and components
- [API Documentation](docs/reference/API_DOCUMENTATION.md) - REST API reference
- [Caching Strategy](docs/REDIS_CACHING_OPTIMIZATION.md) - Redis implementation

### Operations & Monitoring

- [System Monitoring](docs/operations/MONITORING_IMPLEMENTATION.md) - Health checks and alerts
- [Performance Optimization](docs/REDIS_CACHING_OPTIMIZATION.md) - Caching and optimization
- [Deployment Guide](docs/operations/DEPLOYMENT.md) - Production deployment
- [Scaling Guide](docs/operations/SCALING_ANALYSIS.md) - Horizontal and vertical scaling

### Security & Protection

- [Rate Limiting](docs/RATE_LIMITING_COMPLETE.md) - Request rate limiting
- [Circuit Breakers](docs/CIRCUIT_BREAKER_IMPLEMENTATION.md) - System overload protection
- [Query Optimization](docs/SLOW_QUERY_LOGGING.md) - Database performance
- [Alert System](docs/ALERT_SYSTEM_SETUP.md) - Slack/Email notifications

### Troubleshooting

- [Troubleshooting](docs/CLOUDFLARE_521_TROUBLESHOOTING.md) - Common issues and solutions
- [Cloudflare 521 Errors](docs/CLOUDFLARE_521_TROUBLESHOOTING.md) - Connection issues
- [System Crash Postmortem](docs/SYSTEM_CRASH_POSTMORTEM.md) - Incident analysis

### Development

- [Contributing Guide](docs/contributing/CONTRIBUTING.md) - How to contribute
- [Testing Guide](docs/development/testing.md) - Testing procedures
- [Artisan Commands](docs/reference/ARTISAN_COMMANDS.md) - Custom commands reference

---

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 11.x
- **Language**: PHP 8.3
- **Database**: PostgreSQL 16
- **Cache**: Redis 7.x
- **Queue**: Laravel Horizon

### Frontend
- **Framework**: Blade Templates
- **Styling**: Tailwind CSS 3.x
- **JavaScript**: Alpine.js, Chart.js
- **Icons**: Lucide Icons

### Infrastructure
- **Web Server**: Nginx (Load Balanced)
- **PHP**: PHP-FPM (5 pools)
- **Platform**: AWS EC2
- **CDN**: Cloudflare
- **Monitoring**: Custom health checks

---

## ⚡ Quick Start

### Prerequisites

- PHP 8.3+
- PostgreSQL 16+
- Redis 7+
- Composer 2.x
- Node.js 18+ & NPM

### Installation

```bash
# Clone repository
git clone https://github.com/abuzant/TheTradeVisor.git
cd TheTradeVisor

# Install dependencies
composer install
npm install && npm run build

# Configure environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed

# Start services
php artisan serve
php artisan horizon
```

For detailed installation instructions, see the [Installation Guide](docs/getting-started/INSTALLATION.md).

---

## 🔒 Security Features

### Multi-Layer Protection

1. **Rate Limiting** - 10-20 requests/minute on expensive endpoints
2. **Circuit Breakers** - Automatic protection during high load
3. **Query Limits** - Maximum 10,000 records per query
4. **Query Timeout** - 30-second maximum execution time
5. **Slow Query Logging** - Performance monitoring
6. **System Monitoring** - Health checks every 2 minutes
7. **Alert System** - Slack/Email notifications

### Authentication & Authorization

- Laravel Breeze authentication
- Role-based access control (Admin/User)
- API key authentication
- Session management
- CSRF protection

---

## 📊 Performance

### Optimizations

- **Redis Caching**: 90% reduction in database load
- **Query Optimization**: All queries paginated and limited
- **Database Aggregation**: Statistics calculated in PostgreSQL
- **CDN**: Cloudflare for static assets
- **Load Balancing**: 4 Nginx backend instances

### Benchmarks

- **Page Load**: 50-200ms (cached)
- **Analytics**: 5-minute cache, instant response
- **Exports**: Rate limited to 5/minute
- **API**: Sub-100ms response times

---

## 💰 Subscription Tiers

TheTradeVisor offers flexible pricing to suit traders of all levels:

| Tier | Price | Accounts | Features |
|------|-------|----------|----------|
| **Free** | $0 | 1 account | Full analytics, real-time data, global insights |
| **Pay-Per-Account** | $9.99 one-time | Unlimited | Add accounts as needed, lifetime access per account |
| **Enterprise** | Custom | Unlimited | Custom solutions, priority support, dedicated infrastructure |

**Key Points:**
- ✅ First account is **FREE** forever
- ✅ Additional accounts: **$9.99 one-time payment** (no monthly fees!)
- ✅ Account limits enforced to prevent abuse
- ✅ All tiers include full platform features
- ✅ Enterprise tier for trading firms and institutions

---

## 🌍 Global Analytics

TheTradeVisor aggregates trading data from users worldwide, providing:

- **Country-based Analytics** - Trading patterns by region
- **Broker Comparison** - Performance across brokers
- **Symbol Popularity** - Most traded instruments
- **Market Sentiment** - Win rates and profitability
- **Trading Hours** - Peak trading times globally

All data is anonymized and aggregated for privacy.

---

## 📈 System Status

### Current Version: 1.4.0

**Production Status**: ✅ Stable

**Recent Updates** (November 2025):
- ✅ Account limit enforcement (prevent abuse)
- ✅ Redirect authenticated users from guest pages
- ✅ Updated pricing model ($9.99 one-time per account)
- ✅ Removed PRO tier from subscription system
- ✅ Circuit breaker implementation
- ✅ Comprehensive rate limiting
- ✅ Slow query logging
- ✅ Alert system (Slack/Email)
- ✅ Storage permissions optimization
- ✅ Pagination everywhere
- ✅ Redis caching optimization

See [CHANGELOG.md](docs/CHANGELOG.md) for complete history.

---

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](docs/contributing/CONTRIBUTING.md) for details.

### Development Workflow

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Write/update tests
5. Submit a pull request

---

## 📝 License

This project is proprietary software. All rights reserved.

For licensing inquiries, contact: [hello@thetradevisor.com](mailto:hello@thetradevisor.com)

---

## 🆘 Support

### Getting Help

- 📧 Email: [hello@thetradevisor.com](mailto:hello@thetradevisor.com)
- 🌐 Website: [https://thetradevisor.com](https://thetradevisor.com)
- 📖 Documentation: [docs/](docs/)
- 🐛 Issues: [GitHub Issues](https://github.com/abuzant/TheTradeVisor/issues)

### Professional Services

For custom development, consulting, or enterprise support:
📧 [ruslan@abuzant.com](mailto:ruslan@abuzant.com)

---

## 🏆 Acknowledgments

### Technologies

- [Laravel](https://laravel.com) - The PHP Framework
- [PostgreSQL](https://postgresql.org) - Advanced Database
- [Redis](https://redis.io) - In-Memory Data Store
- [Tailwind CSS](https://tailwindcss.com) - Utility-First CSS
- [Chart.js](https://chartjs.org) - Beautiful Charts

### Design Patterns

- Circuit Breaker Pattern (Martin Fowler)
- Repository Pattern
- Service Layer Architecture
- Event-Driven Design

---

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)  
❤️ From Palestine to the world with Love

For project support and inquiries:  
📧 [hello@thetradevisor.com](mailto:hello@thetradevisor.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)
