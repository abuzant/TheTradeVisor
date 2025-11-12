# Deployment Guide

This guide covers deploying TheTradeVisor to production environments.

## 📋 Pre-Deployment Checklist

- [ ] All tests passing (`php artisan test`)
- [ ] Environment variables configured
- [ ] Database migrations tested
- [ ] Frontend assets built (`npm run build`)
- [ ] SSL certificate configured
- [ ] Backup strategy in place
- [ ] Monitoring tools configured

## 🚀 Deployment Options

### Option 1: Traditional Server (VPS/Dedicated)

#### Requirements
- Ubuntu 20.04+ or similar Linux distribution
- Nginx or Apache
- PHP 8.2+ with required extensions
- MySQL/PostgreSQL or SQLite
- Redis (optional but recommended)
- Supervisor (for queue workers)

#### Step-by-Step Deployment

1. **Server Setup**

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2 and extensions
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-xml php8.2-curl php8.2-mbstring \
    php8.2-zip php8.2-bcmath php8.2-redis php8.2-sqlite3

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Nginx
sudo apt install -y nginx

# Install Redis (optional)
sudo apt install -y redis-server
```

2. **Clone and Setup Application**

```bash
# Clone repository
cd /var/www
sudo git clone git@github.com:yourusername/TheTradeVisor.git
cd TheTradeVisor

# Set permissions (IMPORTANT: Prevents 500 errors)
sudo chown -R www-data:www-data /var/www/TheTradeVisor
sudo chmod -R 755 /var/www/TheTradeVisor
sudo chmod -R 775 /var/www/TheTradeVisor/storage
sudo chmod -R 775 /var/www/TheTradeVisor/bootstrap/cache

# Set setgid bit so new files inherit www-data group
sudo find /var/www/TheTradeVisor/storage -type d -exec chmod 2775 {} \;
sudo find /var/www/TheTradeVisor/bootstrap/cache -type d -exec chmod 2775 {} \;

# Add deployment user to www-data group (if using different user)
sudo usermod -a -G www-data $USER

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Environment setup
cp .env.example .env
php artisan key:generate
```

3. **Configure Environment**

Edit `.env` file:

```env
APP_NAME=TheTradeVisor
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tradevisor
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

4. **Database Setup**

```bash
# Create database
mysql -u root -p
CREATE DATABASE tradevisor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tradevisor_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON tradevisor.* TO 'tradevisor_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
php artisan migrate --force
php artisan passport:install --force
php artisan storage:link
```

5. **Optimize Application**

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

6. **Configure Nginx**

Create `/etc/nginx/sites-available/tradevisor`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/TheTradeVisor/public;

    # SSL Configuration
    ssl_certificate /etc/ssl/certs/your_cert.crt;
    ssl_certificate_key /etc/ssl/private/your_key.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Increase upload size limits
    client_max_body_size 100M;
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/tradevisor /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

7. **Configure Supervisor for Queue Workers**

Create `/etc/supervisor/conf.d/tradevisor-worker.conf`:

```ini
[program:tradevisor-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/TheTradeVisor/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/TheTradeVisor/storage/logs/worker.log
stopwaitsecs=3600
```

Start supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start tradevisor-worker:*
```

8. **Setup Cron Jobs**

```bash
sudo crontab -e -u www-data
```

Add:

```cron
* * * * * cd /var/www/TheTradeVisor && php artisan schedule:run >> /dev/null 2>&1
```

### Option 2: Docker Deployment

1. **Create Dockerfile**

```dockerfile
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www

CMD ["php-fpm"]
```

2. **Create docker-compose.yml**

```yaml
version: '3.8'

services:
  app:
    build: .
    container_name: tradevisor-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    networks:
      - tradevisor

  nginx:
    image: nginx:alpine
    container_name: tradevisor-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx:/etc/nginx/conf.d
    networks:
      - tradevisor

  mysql:
    image: mysql:8.0
    container_name: tradevisor-mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: tradevisor
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_USER: tradevisor_user
      MYSQL_PASSWORD: secure_password
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - tradevisor

  redis:
    image: redis:alpine
    container_name: tradevisor-redis
    restart: unless-stopped
    networks:
      - tradevisor

networks:
  tradevisor:
    driver: bridge

volumes:
  mysql_data:
```

3. **Deploy with Docker**

```bash
docker-compose up -d
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan passport:install --force
```

### Option 3: Cloud Platforms

#### Laravel Forge
1. Connect your server to Forge
2. Create a new site
3. Deploy from GitHub repository
4. Configure environment variables
5. Enable Quick Deploy

#### AWS Elastic Beanstalk
1. Install EB CLI
2. Initialize EB application
3. Configure `.ebextensions`
4. Deploy: `eb deploy`

#### DigitalOcean App Platform
1. Connect GitHub repository
2. Configure build and run commands
3. Set environment variables
4. Deploy

## 🔒 Security Hardening

1. **Firewall Configuration**

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

2. **Disable Directory Listing**

Add to Nginx config:
```nginx
autoindex off;
```

3. **Hide PHP Version**

Edit `/etc/php/8.2/fpm/php.ini`:
```ini
expose_php = Off
```

4. **Setup Fail2Ban**

```bash
sudo apt install fail2ban
sudo systemctl enable fail2ban
```

## 📊 Monitoring

### Application Monitoring
- Laravel Telescope (development)
- Laravel Horizon (queue monitoring)
- New Relic / Datadog (production)

### Server Monitoring
- Uptime monitoring (UptimeRobot, Pingdom)
- Server metrics (Netdata, Prometheus)
- Log aggregation (ELK Stack, Papertrail)

## 🔄 Updates and Maintenance

### Deploying Updates

```bash
cd /var/www/TheTradeVisor
git pull origin main
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo supervisorctl restart tradevisor-worker:*
```

### Database Backups

```bash
# Create backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u tradevisor_user -p tradevisor > /backups/tradevisor_$DATE.sql
# Upload to S3 or backup storage
```

### Log Rotation

Configure in `/etc/logrotate.d/tradevisor`:

```
/var/www/TheTradeVisor/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

## 🆘 Troubleshooting

### Common Issues

1. **500 Internal Server Error**
   - Check storage permissions: `chmod -R 775 storage bootstrap/cache`
   - Check error logs: `tail -f storage/logs/laravel.log`

2. **Queue Not Processing**
   - Check supervisor status: `sudo supervisorctl status`
   - Restart workers: `sudo supervisorctl restart tradevisor-worker:*`

3. **Database Connection Issues**
   - Verify credentials in `.env`
   - Check database service: `sudo systemctl status mysql`

4. **Asset Not Loading**
   - Rebuild assets: `npm run build`
   - Clear cache: `php artisan cache:clear`

## 📞 Support

For deployment issues, contact your DevOps team or refer to the main README.md for support contacts.

---

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)

For project support and inquiries:  
📧 [hello@thetradevisor.com](mailto:hello@thetradevisor.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)
