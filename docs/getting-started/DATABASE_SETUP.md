# Database Setup Guide

## Overview
This guide covers database setup and configuration for TheTradeVisor.

## PostgreSQL Setup

### 1. Install PostgreSQL 16
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install postgresql-16 postgresql-contrib

# Start PostgreSQL
sudo systemctl start postgresql
sudo systemctl enable postgresql
```

### 2. Create Database and User
```bash
# Switch to postgres user
sudo -u postgres psql

# Create database
CREATE DATABASE thetradevisor;

# Create user
CREATE USER tradevisor_user WITH PASSWORD 'your_secure_password';

# Grant privileges
GRANT ALL PRIVILEGES ON DATABASE thetradevisor TO tradevisor_user;

# Exit
\q
```

### 3. Configure .env
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=thetradevisor
DB_USERNAME=tradevisor_user
DB_PASSWORD=your_secure_password
```

## Database Migrations

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Database (Optional)
```bash
php artisan db:seed
```

## Database Schema

The database includes the following main tables:
- `users` - User accounts
- `trading_accounts` - MT4/MT5 account connections
- `deals` - Trade execution records
- `positions` - Current open positions
- `orders` - Trade requests
- `symbols` - Trading instruments
- `brokers` - Broker information

## Database Maintenance

### 1. Backup Database
```bash
pg_dump -h localhost -U tradevisor_user thetradevisor > backup.sql
```

### 2. Restore Database
```bash
psql -h localhost -U tradevisor_user thetradevisor < backup.sql
```

### 3. Optimize Database
```bash
php artisan db:optimize
```

## Troubleshooting

### Connection Issues
- Verify PostgreSQL is running
- Check firewall settings
- Verify user permissions

### Performance Issues
- Run `ANALYZE` on tables
- Check query logs
- Consider indexing

See [Database Maintenance](../operations/DATABASE_MAINTENANCE.md) for more details.
