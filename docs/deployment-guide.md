# TheTradeVisor Deployment Guide

## Server Architecture

### Current Setup
- **OS**: Ubuntu Linux
- **Web Server**: Nginx
- **PHP**: 8.3 with PHP-FPM
- **Database**: PostgreSQL (Docker container)
- **Cache**: Redis (Docker container)
- **Queue**: Redis with Laravel Horizon
- **Process Manager**: Supervisor

### Directory Structure
```
/vhosts/thetradevisor.com/
├── app/                    # Application code
├── bootstrap/              # Bootstrap files
├── config/                 # Configuration files
├── database/               # Database migrations and seeds
├── public/                 # Web root
├── resources/              # Views and assets
├── routes/                 # Route definitions
├── storage/                # Application storage
│   ├── app/
│   │   └── raw_data/       # MT4/MT5 JSON data (auto-cleaned)
│   └── logs/               # Application logs
├── scripts/                # Maintenance scripts
└── vendor/                 # Composer dependencies
```

## Pre-Deployment Checklist

### 1. Backup Current System
```bash
# Create application backup
cd /vhosts/thetradevisor.com
./scripts/backup.sh

# Create database backup
docker exec postgres pg_dump -U pgsql_user -d thetradevisor_app > backup_$(date +%Y%m%d_%H%M%S).sql
```

### 2. Verify Environment
```bash
# Check PHP version
php -v

# Check Composer
composer --version

# Check Node.js (if needed)
node -v
npm -v

# Verify Docker containers
docker ps
```

### 3. Review Configuration
```bash
# Check .env file
cat .env | grep -E "APP_ENV|APP_DEBUG|DB_|REDIS_|QUEUE_"

# Verify database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

## Deployment Steps

### 1. Pull Latest Code
```bash
cd /vhosts/thetradevisor.com
git pull origin main
```

### 2. Install Dependencies
```bash
# PHP dependencies
composer install --no-dev --optimize-autoloader

# Node.js dependencies (if applicable)
npm ci --production
npm run build
```

### 3. Update Database
```bash
# Run migrations
php artisan migrate --force

# Seed data if needed
php artisan db:seed --force
```

### 4. Clear and Cache
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Restart Services
```bash
# Restart PHP-FPM
sudo systemctl restart php8.3-fpm

# Restart Nginx
sudo systemctl restart nginx

# Restart queue workers
sudo supervisorctl restart horizon
sudo supervisorctl restart thetradevisor-worker:*

# Optional: Full refresh
./scripts/refurbish.sh
```

### 6. Verify Deployment
```bash
# Check health
./scripts/healthcheck.sh

# Check Horizon status
php artisan horizon:status

# Test application
curl -I https://thetradevisor.com
```

## Post-Deployment Tasks

### 1. Monitor Logs
```bash
# Application logs
tail -f storage/logs/laravel.log

# Queue logs
tail -f storage/logs/horizon.log

# Nginx logs
tail -f /var/log/nginx/error.log
```

### 2. Check Queue Processing
```bash
# Check queue status
php artisan queue:monitor

# View Horizon dashboard
# Visit: https://thetradevisor.com/horizon
```

### 3. Verify Database
```bash
# Check PostgreSQL
docker exec postgres psql -U pgsql_user -d thetradevisor_app -c "SELECT version();"

# Check connections
docker exec postgres psql -U pgsql_user -d thetradevisor_app -c "SELECT count(*) FROM pg_stat_activity;"
```

## Environment Configuration

### Required .env Variables
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://thetradevisor.com

# Database
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=thetradevisor_app
DB_USERNAME=thetradevisor_db
DB_PASSWORD=your_password

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=redis

# Horizon
HORIZON_ENV=production

# Email
MAIL_MAILER=ses
MAIL_HOST=email-smtp.eu-west-1.amazonaws.com
MAIL_PORT=587
MAIL_USERNAME=your_ses_username
MAIL_PASSWORD=your_ses_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@thetradevisor.com
```

## Service Management

### Supervisor Configuration
```ini
# /etc/supervisor/conf.d/horizon.conf
[program:horizon]
process_name=%(program_name)s
command=php /vhosts/thetradevisor.com/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/vhosts/thetradevisor.com/storage/logs/horizon.log
stopwaitsecs=3600
```

### PHP-FPM Configuration
- Pool file: `/etc/php/8.3/fpm/pool.d/thetradevisor.conf`
- User: `www-data`
- Memory limit: Configured per pool

### Docker Containers
```yaml
# docker-compose.yml (relevant services)
services:
  postgres:
    image: postgres:15
    environment:
      POSTGRES_DB: thetradevisor_app
      POSTGRES_USER: pgsql_user
      POSTGRES_PASSWORD: your_password
    volumes:
      - ./postgres/data:/var/lib/postgresql/data

  redis:
    image: redis:7
    command: redis-server --appendonly yes
    volumes:
      - ./redis/data:/data
```

## Troubleshooting

### Common Issues

#### 1. Queue Workers Not Processing
```bash
# Check Supervisor status
sudo supervisorctl status

# Restart workers
sudo supervisorctl restart thetradevisor-worker:*

# Check Redis connection
redis-cli ping
```

#### 2. Database Connection Failed
```bash
# Check PostgreSQL container
docker ps | grep postgres

# Check container logs
docker logs postgres

# Test connection
docker exec postgres psql -U pgsql_user -d thetradevisor_app
```

#### 3. 502 Bad Gateway
```bash
# Check PHP-FPM status
sudo systemctl status php8.3-fpm

# Check PHP-FPM log
tail -f /var/log/php8.3-fpm.log

# Check Nginx configuration
sudo nginx -t
```

#### 4. High Memory Usage
```bash
# Check memory usage
free -h

# Check PHP processes
ps aux | grep php

# Restart services
./scripts/refurbish.sh
```

## Performance Optimization

### 1. PHP-FPM Tuning
```ini
; /etc/php/8.3/fpm/pool.d/thetradevisor.conf
pm = dynamic
pm.max_children = 20
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
```

### 2. PostgreSQL Optimization
```sql
-- postgresql.conf
shared_buffers = 256MB
effective_cache_size = 1GB
work_mem = 4MB
maintenance_work_mem = 64MB
```

### 3. Redis Configuration
```conf
# redis.conf
maxmemory 512mb
maxmemory-policy allkeys-lru
```

## Security Considerations

1. **File Permissions**
   - Web files: `www-data:www-data`
   - Storage: `775` for directories, `664` for files
   - Sensitive files: `600`

2. **Environment Variables**
   - Keep `.env` secure
   - Use strong passwords
   - Rotate keys regularly

3. **SSL/TLS**
   - Use HTTPS only
   - Keep certificates updated
   - Implement HSTS

## Monitoring

### Key Metrics to Monitor
- CPU usage
- Memory usage
- Disk space (especially `/vhosts/thetradevisor.com/storage/app/raw_data/`)
- Queue backlog
- Database connections
- Response times

### Monitoring Tools
- Laravel Horizon (queues)
- System health monitor script
- Nginx access logs
- PostgreSQL slow query log

## Rollback Procedure

If deployment fails:
```bash
# 1. Restore code
git checkout HEAD~1

# 2. Restore database
docker exec -i postgres psql -U pgsql_user -d thetradevisor_app < backup.sql

# 3. Clear caches
php artisan cache:clear

# 4. Restart services
sudo systemctl restart php8.3-fpm
sudo supervisorctl restart all
```

## Scheduled Tasks

### Recommended Cron Jobs
```bash
# Edit cron
sudo crontab -e

# Add jobs
*/2 * * * * /vhosts/thetradevisor.com/scripts/monitor_system_health.sh
0 3 * * 0 /vhosts/thetradevisor.com/scripts/cleanup_old_json_files.sh false
0 1 * * * /vhosts/thetradevisor.com/scripts/cleanup_backups.sh
```

## Additional Resources

- [Server Maintenance Guide](server-maintenance.md)
- [API Documentation](api-documentation.md)
- [Troubleshooting Guide](troubleshooting.md)
