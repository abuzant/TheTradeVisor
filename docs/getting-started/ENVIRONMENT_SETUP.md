# Environment Setup Guide

## Overview
This guide covers the environment configuration for TheTradeVisor application.

## Prerequisites
- PHP 8.3+
- PostgreSQL 16
- Redis 7+
- Nginx
- Composer
- Node.js 18+
- npm or yarn

## Environment Configuration

### 1. Copy Environment File
```bash
cp .env.example .env
```

### 2. Configure Database
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=thetradevisor
DB_USERNAME=tradevisor_user
DB_PASSWORD=your_password
```

### 3. Configure Cache
```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 4. Configure Queue
```env
QUEUE_CONNECTION=redis
```

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Run Migrations
```bash
php artisan migrate
```

### 7. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm install && npm run build
```

### 8. Set Permissions
```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## Development Environment
For development, you may use:
- Laravel Sail (Docker)
- Laravel Valet (macOS)
- XAMPP/WAMP (Windows)

## Production Environment
See [Deployment Guide](../operations/DEPLOYMENT.md) for production setup.
