# 🚀 Installation Guide

Complete guide to installing TheTradeVisor on your server.

## 📋 Prerequisites

### System Requirements
- **OS**: Ubuntu 20.04+ / Debian 11+ / CentOS 8+
- **PHP**: 8.2 or higher
- **Database**: PostgreSQL 13+ or MySQL 8.0+
- **Web Server**: Nginx or Apache
- **Memory**: Minimum 2GB RAM
- **Storage**: Minimum 10GB free space

### Required PHP Extensions
```bash
php-cli
php-fpm
php-pgsql (or php-mysql)
php-mbstring
php-xml
php-curl
php-zip
php-gd
php-bcmath
php-intl
```

### Additional Requirements
- Composer 2.0+
- Node.js 18+ and NPM
- Git
- Redis (optional, for caching)

## 🔧 Installation Steps

### 1. Clone the Repository

```bash
cd /var/www
git clone https://github.com/abuzant/TheTradeVisor.git
cd TheTradeVisor
```

### 2. Install PHP Dependencies

```bash
composer install --optimize-autoloader --no-dev
```

### 3. Install Node Dependencies

```bash
npm install
npm run build
```

### 4. Environment Configuration

```bash
cp .env.example .env
nano .env
```

Configure the following:
```env
APP_NAME="TheTradeVisor"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=thetradevisor
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

MAXMIND_LICENSE_KEY=your_maxmind_key
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Run Database Migrations

```bash
php artisan migrate --force
```

### 7. Install Laravel Passport

```bash
php artisan passport:install
```

### 8. Download GeoIP Database

```bash
php artisan geoip:update
```

### 9. Set Permissions

```bash
chown -R www-data:www-data /var/www/TheTradeVisor
chmod -R 755 /var/www/TheTradeVisor
chmod -R 775 /var/www/TheTradeVisor/storage
chmod -R 775 /var/www/TheTradeVisor/bootstrap/cache
```

### 10. Configure Web Server

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/TheTradeVisor/public;

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
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 11. Setup SSL (Recommended)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

### 12. Configure Scheduler

Add to crontab:
```bash
crontab -e
```

Add this line:
```
* * * * * cd /var/www/TheTradeVisor && php artisan schedule:run >> /dev/null 2>&1
```

### 13. Setup Queue Worker (Optional)

Create systemd service:
```bash
sudo nano /etc/systemd/system/thetradevisor-worker.service
```

```ini
[Unit]
Description=TheTradeVisor Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/TheTradeVisor
ExecStart=/usr/bin/php /var/www/TheTradeVisor/artisan queue:work --sleep=3 --tries=3
Restart=always

[Install]
WantedBy=multi-user.target
```

Enable and start:
```bash
sudo systemctl enable thetradevisor-worker
sudo systemctl start thetradevisor-worker
```

## ✅ Verification

### Test the Installation

1. Visit your domain: `https://yourdomain.com`
2. Register a new account
3. Check logs: `tail -f storage/logs/laravel.log`

### Run Tests

```bash
php artisan test
```

All tests should pass!

## 🔄 Post-Installation

### Create Admin User

```bash
php artisan tinker
```

```php
$user = User::where('email', 'your@email.com')->first();
$user->is_admin = true;
$user->save();
```

### Optimize for Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🆘 Troubleshooting

### Permission Issues
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Database Connection Failed
- Check database credentials in `.env`
- Ensure database exists
- Test connection: `php artisan tinker` then `DB::connection()->getPdo();`

### 500 Error
- Check `storage/logs/laravel.log`
- Ensure `.env` file exists
- Run `php artisan config:clear`

## 📚 Next Steps

- [Quick Start Guide](quick-start.md)
- [Configuration](configuration.md)
- [API Setup](api/overview.md)

---

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)

For project support and inquiries:  
📧 [your-email@example.com](mailto:your-email@example.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)
