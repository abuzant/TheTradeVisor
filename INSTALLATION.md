# 🚀 TheTradeVisor - Installation Guide

Complete installation instructions for setting up TheTradeVisor on your own server.

---

## 📋 Table of Contents

- [System Requirements](#-system-requirements)
- [Quick Start (Docker)](#-quick-start-docker)
- [Manual Installation](#-manual-installation)
- [Configuration](#-configuration)
- [Post-Installation](#-post-installation)
- [Troubleshooting](#-troubleshooting)

---

## 💻 System Requirements

### Minimum Requirements

- **OS**: Linux (Ubuntu 22.04+ recommended), macOS, or Windows with WSL2
- **PHP**: 8.2 or higher
- **Database**: PostgreSQL 15+ or 16+
- **Cache**: Redis 7+
- **Web Server**: Nginx 1.24+ or Apache 2.4+
- **Memory**: 2GB RAM minimum (4GB+ recommended)
- **Storage**: 10GB minimum

### Software Dependencies

#### PHP Extensions Required:
```bash
php8.3-cli
php8.3-fpm
php8.3-pgsql
php8.3-redis
php8.3-mbstring
php8.3-xml
php8.3-curl
php8.3-zip
php8.3-gd
php8.3-intl
php8.3-bcmath
```

#### Node.js & NPM:
- **Node.js**: 18.x or higher
- **NPM**: 9.x or higher

#### Composer:
- **Composer**: 2.x

---

## 🐳 Quick Start (Docker)

The fastest way to get TheTradeVisor running is with Docker Compose.

### Prerequisites

- Docker 20.10+
- Docker Compose 2.0+

### Installation Steps

```bash
# 1. Clone the repository
git clone https://github.com/abuzant/TheTradeVisor.git
cd TheTradeVisor

# 2. Copy environment file
cp .env.example .env

# 3. Update .env with your settings
nano .env  # or use your preferred editor

# 4. Build and start containers
docker-compose up -d

# 5. Install dependencies
docker-compose exec app composer install
docker-compose exec app npm install && npm run build

# 6. Generate application key
docker-compose exec app php artisan key:generate

# 7. Run migrations
docker-compose exec app php artisan migrate --seed

# 8. Set permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache

# 9. Access the application
# Open http://localhost in your browser
```

### Docker Services

The docker-compose setup includes:

- **app**: Laravel application (Nginx + PHP-FPM)
- **postgres**: PostgreSQL 15 database
- **redis**: Redis 7 cache & queue
- **horizon**: Laravel Horizon queue worker

### Docker Commands

```bash
# View logs
docker-compose logs -f app

# Stop services
docker-compose down

# Restart services
docker-compose restart

# Access app container
docker-compose exec app bash

# Run artisan commands
docker-compose exec app php artisan [command]
```

---

## 🔧 Manual Installation

For production deployments or custom setups.

### Step 1: Install System Dependencies

#### Ubuntu/Debian:

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.3 and extensions
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-pgsql php8.3-redis \
    php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd \
    php8.3-intl php8.3-bcmath

# Install PostgreSQL 16
sudo apt install -y postgresql-16 postgresql-client-16

# Install Redis
sudo apt install -y redis-server

# Install Nginx
sudo apt install -y nginx

# Install Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Step 2: Clone Repository

```bash
# Clone to web directory
cd /var/www
sudo git clone https://github.com/abuzant/TheTradeVisor.git thetradevisor.com
cd thetradevisor.com

# Set ownership
sudo chown -R www-data:www-data /var/www/thetradevisor.com
```

### Step 3: Install Application Dependencies

```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies and build assets
npm install
npm run build
```

### Step 4: Configure Database

```bash
# Create PostgreSQL database and user
sudo -u postgres psql << EOF
CREATE DATABASE thetradevisor;
CREATE USER thetradevisor WITH ENCRYPTED PASSWORD 'your_secure_password';
GRANT ALL PRIVILEGES ON DATABASE thetradevisor TO thetradevisor;
ALTER DATABASE thetradevisor OWNER TO thetradevisor;
\c thetradevisor
GRANT ALL ON SCHEMA public TO thetradevisor;
EOF
```

### Step 5: Configure Application

```bash
# Copy environment file
cp .env.example .env

# Edit configuration
nano .env
```

**Required .env settings:**

```env
APP_NAME="TheTradeVisor"
APP_ENV=production
APP_KEY=  # Will be generated
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=thetradevisor
DB_USERNAME=thetradevisor
DB_PASSWORD=your_secure_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 6: Initialize Application

```bash
# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed database (optional - creates admin user)
php artisan db:seed

# Link storage
php artisan storage:link

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Step 7: Configure Nginx

Create `/etc/nginx/sites-available/thetradevisor.com`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com;
    root /var/www/thetradevisor.com/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/thetradevisor.com /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Step 8: Setup Queue Worker (Horizon)

Create systemd service `/etc/systemd/system/horizon.service`:

```ini
[Unit]
Description=Laravel Horizon
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/thetradevisor.com/artisan horizon
WorkingDirectory=/var/www/thetradevisor.com

[Install]
WantedBy=multi-user.target
```

Enable and start:

```bash
sudo systemctl enable horizon
sudo systemctl start horizon
sudo systemctl status horizon
```

### Step 9: Setup SSL (Optional but Recommended)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d your-domain.com

# Auto-renewal is configured automatically
```

---

## ⚙️ Configuration

### Environment Variables

Key configuration options in `.env`:

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_ENV` | Environment (production/local) | production |
| `APP_DEBUG` | Debug mode (true/false) | false |
| `APP_URL` | Application URL | http://localhost |
| `DB_CONNECTION` | Database driver | pgsql |
| `CACHE_DRIVER` | Cache driver | redis |
| `QUEUE_CONNECTION` | Queue driver | redis |
| `SESSION_DRIVER` | Session driver | redis |

### Subscription Tiers

Configure in database or via admin panel:

- **Free**: 1 account, max_accounts = 1
- **Basic**: Pay-per-account ($9.99), max_accounts = purchased count
- **Enterprise**: Unlimited, max_accounts = 999999

### API Rate Limiting

Configured in `app/Http/Middleware/`:

- Analytics: 10 requests/minute
- Exports: 5 requests/minute
- Broker Analytics: 20 requests/minute

---

## 🎯 Post-Installation

### Create Admin User

```bash
php artisan tinker
```

```php
$user = App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('secure_password'),
    'is_admin' => true,
    'is_active' => true,
    'subscription_tier' => 'enterprise',
    'max_accounts' => 999999,
    'api_key' => Str::random(64),
]);
```

### Verify Installation

1. **Access Application**: Visit your domain
2. **Login**: Use admin credentials
3. **Check Dashboard**: Verify no errors
4. **Test API**: Connect MT4/MT5 EA
5. **Check Horizon**: Visit `/horizon` (admin only)
6. **Check Telescope**: Visit `/telescope` (admin only)

### Setup Monitoring

```bash
# Add health check cron (every 2 minutes)
sudo crontab -e -u www-data
```

Add:
```cron
*/2 * * * * cd /var/www/thetradevisor.com && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔍 Troubleshooting

### Common Issues

#### 1. Permission Errors

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### 2. Database Connection Failed

- Verify PostgreSQL is running: `sudo systemctl status postgresql`
- Check credentials in `.env`
- Test connection: `psql -U thetradevisor -d thetradevisor -h 127.0.0.1`

#### 3. Redis Connection Failed

- Verify Redis is running: `sudo systemctl status redis`
- Test connection: `redis-cli ping`

#### 4. Queue Not Processing

- Check Horizon status: `sudo systemctl status horizon`
- View logs: `sudo journalctl -u horizon -f`
- Restart: `sudo systemctl restart horizon`

#### 5. 500 Internal Server Error

- Check Laravel logs: `tail -f storage/logs/laravel.log`
- Check Nginx logs: `sudo tail -f /var/log/nginx/error.log`
- Clear cache: `php artisan cache:clear && php artisan config:clear`

### Logs Location

- **Laravel**: `storage/logs/laravel.log`
- **Nginx**: `/var/log/nginx/error.log`
- **PHP-FPM**: `/var/log/php8.3-fpm.log`
- **PostgreSQL**: `/var/log/postgresql/postgresql-16-main.log`
- **Horizon**: `sudo journalctl -u horizon`

---

## 📚 Next Steps

After installation:

1. **Configure MT4/MT5 EA** - See [MT4/MT5 EA Installation Guide](docs/guides/MT4_EA_INSTALLATION.md)
2. **Setup Monitoring** - See [Monitoring Guide](docs/operations/MONITORING_IMPLEMENTATION.md)
3. **Configure Backups** - Setup automated database backups
4. **Review Security** - See [Security Guide](docs/security/)
5. **Customize Settings** - Configure email, alerts, etc.

---

## 🆘 Support

Need help?

- 📧 Email: [hello@thetradevisor.com](mailto:hello@thetradevisor.com)
- 📖 Documentation: [docs/](docs/)
- 🐛 Issues: [GitHub Issues](https://github.com/abuzant/TheTradeVisor/issues)

---

**Happy Trading! 📈**
