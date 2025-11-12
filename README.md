# TheTradeVisor 📊

> **Enterprise-grade trading analytics and account management platform with advanced monitoring, caching, and fault tolerance**

A comprehensive, production-ready trading analytics platform built with Laravel 11, featuring enterprise-level infrastructure including intelligent caching, circuit breakers, real-time queue monitoring, and advanced performance optimization.

[![Laravel](https://img.shields.io/badge/Laravel-11-red.svg?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-blue.svg?logo=php)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED.svg?logo=docker)](https://www.docker.com)
[![Docker Compose](https://img.shields.io/badge/Docker%20Compose-Ready-2496ED.svg?logo=docker)](https://docs.docker.com/compose)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15-336791.svg?logo=postgresql)](https://www.postgresql.org)
[![Redis](https://img.shields.io/badge/Redis-7-DC382D.svg?logo=redis)](https://redis.io)
[![Nginx](https://img.shields.io/badge/Nginx-1.24-009639.svg?logo=nginx)](https://nginx.org)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Production Ready](https://img.shields.io/badge/Status-Production%20Ready-brightgreen.svg)](https://thetradevisor.com)
[![Version](https://img.shields.io/badge/Version-1.2.1-blue.svg)](CHANGELOG.md)
[![One-Click Deploy](https://img.shields.io/badge/One%20Click%20Deploy-00A8E1.svg)](#-docker-deployment)
[![Analytics](https://img.shields.io/badge/Analytics-Real--time-FF6B6B.svg)](https://thetradevisor.com/analytics)
[![MT4/MT5](https://img.shields.io/badge/MT4%2FMT5-Supported-4CAF50.svg)](docs/MT4_MT5_POSITION_SYSTEM.md)
[![Platform Detection](https://img.shields.io/badge/Platform-Auto--Detect-9C27B0.svg)](docs/MT4_MT5_POSITION_SYSTEM.md)
[![Client-Side Filtering](https://img.shields.io/badge/Filtering-50x%20Faster-FF9800.svg)](docs/CLIENT_SIDE_FILTERING_AND_PLATFORM_DETECTION.md)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0.svg?logo=alpine.js)](https://alpinejs.dev)

## 📚 Documentation

**[📖 Complete Documentation Index →](docs/INDEX.md)** | **[Documentation Overview →](docs/README.md)**

### 🆕 Latest Improvements (November 2025)

#### Version 1.3.0 (Nov 12, 2025) - Professional Marketing Website & REST API
- **🌐 Professional Marketing Website** - Complete multi-page guest website with landing, features, pricing, FAQ, about, and contact pages
- **📚 Comprehensive Documentation** - Full documentation viewer with installation guides, features overview, and troubleshooting
- **🔌 REST API Endpoints** - Complete API implementation with accounts, trades, and analytics endpoints
- **📊 API Documentation** - Professional API reference with Postman-style examples and rate limiting
- **⭐ Testimonials & Social Proof** - Customer testimonials, trust badges, and social proof elements
- **🔐 Tier-Based Rate Limiting** - Subscription-based API rate limits (Free: 100/hr, Pro: 1000/hr, Enterprise: Unlimited)

#### Version 1.2.1 (Nov 12, 2025) - Advanced Analytics & Market Sentiment
- **[Country-Based Market Sentiment Analysis](docs/features/COUNTRY_SENTIMENT_ANALYSIS.md)** - 🌍 Global market sentiment by geographical region with buy/sell ratios
- **[Platform Performance Matrix](docs/features/PLATFORM_PERFORMANCE_MATRIX.md)** - 🖥️ MT4 vs MT5 and Hedging vs Netting performance comparison
- **Interactive Radar Charts** - Multi-dimensional platform performance visualization
- **Advanced Sentiment Scoring** - Quantified market sentiment (0-50% scale)
- **Real-Time Country Analytics** - Live trading patterns by geographic region

#### Version 1.2.0 (Nov 11, 2025) - MT4/MT5 Position System
- **[MT4/MT5 Position System](docs/MT4_MT5_POSITION_SYSTEM.md)** - 🎯 Platform detection and smart position aggregation
- **[Platform Badges & Filters](docs/PLATFORM_BADGES_AND_FILTERS.md)** - Visual platform indicators with instant filtering
- **[Client-Side Filtering](docs/CLIENT_SIDE_FILTERING_AND_PLATFORM_DETECTION.md)** - Lightning-fast filters (50x faster)
- **[Implementation Details](docs/IMPLEMENTATION_DETAILS.md)** - Technical documentation

#### Previous Updates
- **[Dashboard LIVE Positions](docs/features/DASHBOARD_LIVE_POSITIONS.md)** - Real-time exposure visibility across all accounts
- **[Analytics Improvements](docs/ANALYTICS_IMPROVEMENTS_NOV_9_2025.md)** - Bug fixes and UI enhancements
- **[Features Implemented](docs/FEATURES_IMPLEMENTED_NOV_9_2025.md)** - Complete feature overview
- **[Flag Icons Implementation](docs/FLAG_ICONS_IMPLEMENTATION.md)** - Professional country flags

### 🚀 Quick Start
- [Docker Deployment](docs/DOCKER_DEPLOYMENT.md) - 🐳 One-click deployment with Docker
- [Installation Guide](docs/installation.md) - Complete setup instructions
- [Quick Start Guide](docs/quick-start.md) - Get running in 5 minutes
- [Nginx Setup Note](docs/operations/NGINX_SETUP_NOTE.md) - ⚠️ **Important:** Load balancing is optional!

### 📖 Core Documentation
- [API Documentation](docs/reference/API_DOCUMENTATION.md) - MT4/MT5 integration
- [REST API Reference](/api-docs) - Complete API endpoints with examples
- [User Documentation](/docs) - Installation, features, and troubleshooting
- [Currency Display System](docs/features/CURRENCY_DISPLAY.md) - How currency conversion works
- [Artisan Commands](docs/reference/ARTISAN_COMMANDS.md) - Custom commands reference
- [Deployment Guide](docs/operations/DEPLOYMENT.md) - Production deployment
- [Contributing Guide](docs/contributing/CONTRIBUTING.md) - How to contribute

## ✨ Core Features

### 📈 Trading & Analytics
- **🎯 MT4/MT5 Platform Detection** - Automatic detection of platform type and account mode (Netting/Hedging)
- **📊 Smart Position Aggregation** - Intelligent grouping of deals into positions for MT5 Netting accounts
- **🏷️ Platform Badges** - Visual indicators showing MT4/MT5 and Netting/Hedging modes
- **⚡ Client-Side Filtering** - Lightning-fast position filtering (50x faster, no page reload)
- **🔍 Advanced Filters** - Filter by status (open/closed) and profitability (profitable/losses)
- **📂 Expandable Position Rows** - View individual deals within aggregated positions
- **Multi-Platform Support** - Full support for both MT4 and MT5 trading platforms
- **Multi-Account Management** - Connect and manage unlimited MT4/MT5 trading accounts
- **LIVE Open Positions Dashboard** - Real-time exposure visibility across all accounts with animated indicators
- **Advanced Performance Analytics** - Real-time performance metrics with 80-90% cache hit rate
- **Global Analytics Dashboard** - Real-time insights from thousands of traders worldwide
- **Interactive Charts & Visualizations** - Chart.js powered graphs with responsive design
- **Market Sentiment Analysis** - Buy/sell percentages with sentiment indicators
- **🌍 Country-Based Market Sentiment** - **NEW:** Global market sentiment analysis by geographical region
- **🖥️ Platform Performance Matrix** - **NEW:** MT4 vs MT5 and Hedging vs Netting performance comparison
- **GeoIP Country Analytics** - Location-based trading insights with professional flag icons
- **Time Period Filtering** - Flexible date ranges (Today, 7 Days, 30 Days) with validation
- **Trade History Tracking** - Complete historical data with intelligent caching
- **Broker Analytics** - Compare broker performance, spreads, and execution quality
- **Symbol Mapping & Normalization** - Advanced symbol mapping with auto-normalization
- **Smart Currency Display** - Single account = native currency, Multi-account = USD conversion
- **Data Export** - Export to CSV, PDF with customizable filters

### 🚀 Enterprise Infrastructure

#### ⚡ Performance & Caching
- **Nginx FastCGI Cache** - 80-90% reduction in PHP requests, 20x faster page loads
- **Redis Caching** - Multi-layer caching strategy for dashboard, performance metrics, and broker analytics
- **Smart Cache Invalidation** - Automatic cache clearing on data updates
- **ETags & HSTS** - Optimized static asset delivery with security headers
- **X-Cache-Status Headers** - Real-time cache debugging and monitoring

#### 🔄 Fault Tolerance & Resilience
- **Circuit Breaker Pattern** - Automatic failure detection and graceful degradation
- **Service Health Monitoring** - Real-time monitoring of Redis, Database, APIs, Email
- **Automatic Recovery** - Self-healing circuits with configurable retry timeouts
- **Fallback Strategies** - Graceful degradation when external services fail
- **Admin Dashboard** - Visual circuit breaker status with manual reset capability

#### 📊 Queue Management & Monitoring
- **Laravel Horizon** - Advanced queue monitoring with auto-scaling (2-10 workers)
- **Job Prioritization** - Separate queues for real-time and historical data
- **Failed Job Tracking** - Automatic retry with exponential backoff
- **Real-time Metrics** - Throughput, wait times, and job status monitoring
- **Auto-scaling Workers** - Dynamic worker allocation based on queue load

#### 🔭 Development & Debugging
- **Laravel Telescope** - Deep request/response inspection (dev/staging only)
- **Query Monitoring** - N+1 query detection and performance profiling
- **Exception Tracking** - Detailed error logging with context
- **Job Monitoring** - Real-time job execution tracking
- **Cache Analytics** - Hit/miss rate tracking and optimization insights

### 🔐 Security & Access Control
- **Role-Based Access Control (RBAC)** - Admin and user role separation
- **API Authentication** - Laravel Passport OAuth2 implementation
- **Rate Limiting** - Configurable rate limits for API and login endpoints
- **CSRF Protection** - Full CSRF protection on all forms
- **SQL Injection Prevention** - Eloquent ORM with prepared statements
- **XSS Protection** - Blade templating with automatic escaping
- **HSTS Enabled** - Force HTTPS with strict transport security

### 📡 REST API
- **Complete REST API** - Full API implementation for accounts, trades, and analytics
- **MT4/MT5 Integration** - Complete API for trading platform integration
- **Real-time Data Collection** - Async job processing for trading data
- **Historical Data Import** - Bulk import with queue processing
- **API Key Management** - Secure API key generation and validation
- **Tier-Based Rate Limiting** - Subscription-based limits (Free: 100/hr, Pro: 1000/hr, Enterprise: Unlimited)
- **API Documentation** - Professional Postman-style documentation with examples

### 🌐 Marketing Website
- **Professional Landing Page** - Corporate-style homepage with live global analytics
- **Feature Showcase** - Comprehensive features page with detailed descriptions
- **Pricing Plans** - Clear pricing structure with Free, Pay-Per-Account, Pro, and Enterprise tiers
- **FAQ Section** - Detailed frequently asked questions organized by category
- **Documentation Portal** - Complete documentation viewer with guides and troubleshooting
- **API Reference** - Professional API documentation with request/response examples
- **Testimonials** - Customer testimonials with 5-star ratings and social proof
- **Trust Badges** - SSL, GDPR, and 24/7 support indicators
- **SEO Optimized** - Full SEO meta tags, Open Graph, and Twitter Cards on all pages
- **Google Analytics** - Complete tracking across all public pages

## 📋 Requirements

### Minimum Requirements
- **PHP** >= 8.3 (with extensions: redis, gd, mbstring, xml, curl)
- **Composer** >= 2.0
- **Node.js** >= 18.x
- **NPM** >= 9.x
- **Database**: MySQL 8.0+ or PostgreSQL 13+
- **Redis** >= 6.0 (required for caching and queues)
- **Nginx** >= 1.18 (with FastCGI cache support)
- **Supervisor** (for queue workers)

### Recommended Production Setup
- **PHP 8.3** with OPcache enabled
- **Redis Cluster** for high availability
- **MySQL 8.0+** with read replicas
- **Nginx** with FastCGI cache (100MB+ cache zone)
- **2+ CPU cores**, 4GB+ RAM
- **SSL Certificate** (Let's Encrypt recommended)

> **Note:** Our production uses multiple nginx instances with load balancing. This is **optional** and not required for standard deployments. See [Nginx Setup Note](docs/operations/NGINX_SETUP_NOTE.md) for details.

## 🛠️ Installation

### 1. Clone the Repository

```bash
git clone git@github.com:yourusername/TheTradeVisor.git
cd TheTradeVisor
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file and configure your database and other settings:

```env
APP_NAME=TheTradeVisor
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=sqlite
# Or configure MySQL/PostgreSQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=tradevisor
# DB_USERNAME=root
# DB_PASSWORD=

# AWS Configuration (for file storage)
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket_name
```

### 5. Database Setup

```bash
# Create SQLite database (if using SQLite)
touch database/database.sqlite

# Run migrations
php artisan migrate

# (Optional) Seed database with sample data
php artisan db:seed
```

### 6. Laravel Passport Setup

```bash
php artisan passport:install
```

### 7. Storage Link

```bash
php artisan storage:link
```

### 8. Build Frontend Assets

```bash
# For production
npm run build

# For development
npm run dev
```

## 🐳 Docker Deployment

### 🚀 One-Click Deployment with Docker

TheTradeVisor now supports Docker for easy, one-click deployment! No need to configure PHP, Nginx, PostgreSQL, or Redis manually - Docker handles everything.

#### Prerequisites
- Docker & Docker Compose installed
- Git (to clone the repository)

#### Quick Start (5 minutes)

```bash
# 1. Clone the repository
git clone https://github.com/abuzant/TheTradeVisor.git
cd TheTradeVisor

# 2. One-command installation
make install

# 3. Access your application!
# Open http://localhost in your browser
```

That's it! 🎉 The application is now running with:
- ✅ PHP 8.3 with all extensions
- ✅ Nginx web server
- ✅ PostgreSQL database
- ✅ Redis cache & queues
- ✅ Laravel Horizon (queue monitoring)
- ✅ SSL-ready configuration
- ✅ Optimized for production

#### Docker Commands

```bash
# Start all containers
make up

# Stop all containers
make down

# View logs
make logs

# Open shell in container
make shell

# Run Laravel commands
make artisan COMMAND="list"

# Clear cache
make cache-clear

# Complete restart
make restart

# Clean everything
make clean
```

#### Docker Compose Options

```bash
# Build and start (production)
docker-compose up -d --build

# View running containers
docker-compose ps

# Access application logs
docker-compose logs -f app

# Access database logs
docker-compose logs -f postgres

# Backup database
make backup

# Restore database
make restore FILE=backup.sql
```

#### Container Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Nginx         │    │   PHP-FPM       │    │   Laravel       │
│   (Port 80)     │────│   (Port 9000)   │────│   Application   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                                        │
                       ┌─────────────────┐            │
                       │   Redis         │────────────┤
                       │   (Port 6379)   │            │
                       └─────────────────┘            │
                                                        │
                       ┌─────────────────┐            │
                       │   PostgreSQL    │────────────┘
                       │   (Port 5432)   │
                       └─────────────────┘
```

#### Environment Configuration

The Docker setup includes a pre-configured environment:
- **Database:** PostgreSQL 15 with optimized settings
- **Cache:** Redis 7 with persistence
- **PHP:** 8.3 with OPcache and performance tuning
- **Nginx:** Optimized configuration with gzip and caching
- **Security:** All security headers enabled

#### Production Deployment

For production deployment:

```bash
# 1. Clone and configure
git clone https://github.com/abuzant/TheTradeVisor.git
cd TheTradeVisor

# 2. Update environment
cp docker/.env.docker .env
# Edit .env with your production settings

# 3. Deploy
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build

# 4. Install Passport
docker-compose exec app php artisan passport:install
```

#### Development with Docker

```bash
# Development mode with hot reload
make dev

# Or manually
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up
```

---

## 🚀 Traditional Installation (Non-Docker)

### Development

```bash
# Start all services (server, queue, logs, vite)
composer dev
```

Or run services individually:

```bash
# Terminal 1: Laravel development server
php artisan serve

# Terminal 2: Queue worker
php artisan queue:work

# Terminal 3: Vite dev server
npm run dev
```

### Production

```bash
# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start queue worker (use supervisor in production)
php artisan queue:work --daemon

# Serve with your web server (Nginx/Apache)
```

## 📦 Tech Stack

### Backend Framework
- **Laravel 11** - Modern PHP framework with advanced features
- **Laravel Passport** - OAuth2 API authentication
- **Laravel Horizon** - Advanced queue monitoring and auto-scaling
- **Laravel Telescope** - Debugging and performance profiling (dev/staging)
- **Laravel Breeze** - Authentication scaffolding

### Infrastructure & Performance
- **Nginx** - Web server with FastCGI cache (60 min TTL)
- **Redis** - Caching, sessions, and queue backend
- **Supervisor** - Process management for queue workers
- **Circuit Breaker Pattern** - Custom fault tolerance implementation

### Data & Storage
- **MySQL 8.0+** - Primary relational database
- **Redis** - Cache store and queue driver
- **AWS S3** - Cloud storage for trading data files
- **MaxMind GeoLite2** - GeoIP database for location analytics

### Frontend & Assets
- **Vite** - Lightning-fast build tool
- **TailwindCSS 3** - Utility-first CSS framework
- **Alpine.js** - Lightweight reactive framework
- **Axios** - Promise-based HTTP client
- **Chart.js** - Data visualization (if applicable)

### Development & Monitoring
- **Laravel Pail** - Real-time log tailing
- **DomPDF** - Server-side PDF generation
- **Predis** - PHP Redis client
- **PHPUnit** - Testing framework

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/     # Application controllers
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic services
│   └── ...
├── config/                   # Configuration files
├── database/
│   ├── migrations/           # Database migrations
│   ├── seeders/              # Database seeders
│   └── factories/            # Model factories
├── public/                   # Public assets
├── resources/
│   ├── views/                # Blade templates
│   ├── js/                   # JavaScript files
│   └── css/                  # CSS files
├── routes/
│   ├── web.php               # Web routes
│   ├── api.php               # API routes
│   └── ...
├── storage/                  # Application storage
└── tests/                    # Test files
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

## 📚 Documentation

### User Guides
- [API Documentation](docs/reference/API_DOCUMENTATION.md) - Complete API reference for MT4/MT5 integration
- [Artisan Commands](docs/reference/ARTISAN_COMMANDS.md) - Custom artisan commands reference
- [Export and Filter Features](docs/guides/EXPORT_AND_FILTER_FEATURES.md) - Data export capabilities
- [User Guide - Exports](docs/guides/USER_GUIDE_EXPORTS.md) - Step-by-step export guide

### Operations & Deployment
- [Deployment Guide](docs/operations/DEPLOYMENT.md) - Production deployment instructions
- [Infrastructure Recommendations](docs/operations/INFRASTRUCTURE_RECOMMENDATIONS.md) - Nginx, Circuit Breakers, Monitoring analysis
- [Monitoring Implementation](docs/operations/MONITORING_IMPLEMENTATION.md) - Complete monitoring setup guide
- [Scaling Analysis](docs/operations/SCALING_ANALYSIS.md) - Queue and caching optimization

### Project Information
- [Project Structure](docs/project/PROJECT_STRUCTURE.md) - Codebase organization
- [Documentation Index](docs/project/DOCUMENTATION_INDEX.md) - Complete documentation overview
- [Contributing Guide](docs/contributing/CONTRIBUTING.md) - How to contribute to the project
- [Changelog](docs/changelog/CHANGELOG.md) - Version history and changes

### Admin Features
- **Circuit Breaker Dashboard** - `/admin/circuit-breakers` - Monitor service health
- **Queue Monitor (Horizon)** - `/horizon` - Real-time queue analytics
- **Telescope (Dev/Staging)** - `/telescope` - Deep debugging and profiling
- **System Logs** - `/admin/logs` - Centralized log viewer
- **Rate Limits** - `/admin/rate-limits` - API rate limit management

## 🔒 Security

- All sensitive data is encrypted
- API authentication via Laravel Passport
- CSRF protection enabled
- SQL injection protection via Eloquent ORM
- XSS protection via Blade templating

If you discover any security vulnerabilities, please email hello@thetradevisor.com.

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📈 Performance Metrics

### Before Optimization
- Dashboard load time: ~2000ms
- Database queries per request: 10-15
- Concurrent user capacity: ~50
- Cache hit rate: 0%

### After Optimization
- Dashboard load time: **<100ms** (20x faster)
- Database queries per request: **1-2** (10x reduction)
- Concurrent user capacity: **500-1000** (20x increase)
- Cache hit rate: **80-90%** (new capability)
- Bandwidth reduction: **50-70%** (gzip + ETags + caching)

## 🏗️ Architecture Highlights

### Caching Strategy
```
┌─────────────────────────────────────────────────────────┐
│  Nginx FastCGI Cache (L1)                              │
│  ├─ Public pages: 60 min TTL                           │
│  ├─ Static assets: 365 days                            │
│  └─ Bypass: Authenticated users, Admin, API            │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│  Redis Cache (L2)                                       │
│  ├─ Dashboard data: 2 min TTL                          │
│  ├─ Performance metrics: 5 min TTL                     │
│  ├─ Broker analytics: 30 min TTL                       │
│  └─ Auto-invalidation on data updates                  │
└─────────────────────────────────────────────────────────┘
```

### Circuit Breaker Flow
```
┌──────────┐    Success     ┌──────────┐
│  CLOSED  │ ──────────────→│  CLOSED  │
│ (Healthy)│                │ (Healthy)│
└──────────┘                └──────────┘
     │                           ↑
     │ 5 Failures                │ Success
     ↓                           │
┌──────────┐   60s Timeout  ┌──────────┐
│   OPEN   │ ──────────────→│HALF-OPEN │
│(Blocking)│                │ (Testing)│
└──────────┘                └──────────┘
     │                           │
     └─── Fallback ──────────────┘
```

### Queue Architecture
```
MT5 Data → API Endpoint → Redis Queue
                              ↓
                    ┌─────────────────┐
                    │ Laravel Horizon │
                    │  Auto-scaling   │
                    │   2-10 workers  │
                    └─────────────────┘
                              ↓
              ┌───────────────┴───────────────┐
              ↓                               ↓
    ProcessTradingData              ProcessHistoricalData
    (default queue)                 (historical queue)
              ↓                               ↓
         MySQL Database ← Cache Invalidation
```

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)  

For project support and inquiries:  
📧 [hello@thetradevisor.com](mailto:hello@thetradevisor.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)

## 🙏 Acknowledgments

- Built with ❤️ using [Laravel 11](https://laravel.com)
- UI styled with [TailwindCSS](https://tailwindcss.com)
- Queue monitoring by [Laravel Horizon](https://laravel.com/docs/horizon)
- Development debugging with [Laravel Telescope](https://laravel.com/docs/telescope)
- GeoIP data by [MaxMind GeoLite2](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data)
- Infrastructure inspired by enterprise-grade microservices patterns
