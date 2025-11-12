# Docker Deployment Guide

**Last Updated:** November 9, 2025  
**Version:** 2.1.0

---

## 🐳 Docker Support for TheTradeVisor

TheTradeVisor now includes complete Docker support for easy, one-click deployment. No need to manually configure PHP, Nginx, PostgreSQL, or Redis - Docker handles everything!

---

## 🚀 Quick Start (5 Minutes)

### Prerequisites
- Docker Desktop (Windows/Mac) or Docker Engine (Linux)
- Docker Compose
- Git

### One-Command Installation

```bash
# 1. Clone the repository
git clone https://github.com/abuzant/TheTradeVisor.git
cd TheTradeVisor

# 2. Run the installation command
make install

# 3. Access your application!
# Open http://localhost in your browser
```

That's it! 🎉 Your trading analytics platform is running with:
- ✅ PHP 8.3 with all required extensions
- ✅ Nginx web server with SSL-ready configuration
- ✅ PostgreSQL 15 database
- ✅ Redis 7 for caching and queues
- ✅ Laravel Horizon for queue monitoring
- ✅ Optimized for production performance

---

## 📁 Docker Files Overview

### Core Files
- **`Dockerfile`** - Main application container definition
- **`docker-compose.yml`** - Multi-container orchestration
- **`Makefile`** - Convenient commands for Docker operations
- **`.dockerignore`** - Files excluded from Docker build

### Configuration Files
- **`docker/nginx.conf`** - Nginx configuration
- **`docker/supervisord.conf`** - Process management
- **`docker/php.ini`** - PHP performance tuning
- **`docker/.env.docker`** - Environment variables template

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Docker Network                          │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐ │
│  │   Nginx     │  │   PHP-FPM   │  │     Laravel App     │ │
│  │  (Port 80)  │──│ (Port 9000) │──│   (Analytics, API)  │ │
│  └─────────────┘  └─────────────┘  └─────────────────────┘ │
│                                                    │        │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐ │
│  │   Redis     │◄─│   Horizon   │  │   PostgreSQL        │ │
│  │ (Cache/Queue)│  │ (Queue Mgr) │  │    (Database)       │ │
│  │ (Port 6379) │  │             │◄─│    (Port 5432)      │ │
│  └─────────────┘  └─────────────┘  └─────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### Container Details

#### App Container (`thetradevisor-app`)
- **Base Image:** PHP 8.3-FPM Alpine
- **Includes:** Nginx, PHP-FPM, Supervisor
- **Purpose:** Main web application
- **Ports:** 80 (HTTP)

#### PostgreSQL Container (`thetradevisor-postgres`)
- **Base Image:** PostgreSQL 15 Alpine
- **Database:** `thetradevisor`
- **User:** `thetradevisor`
- **Persistence:** Docker volume
- **Ports:** 5432 (accessible from host)

#### Redis Container (`thetradevisor-redis`)
- **Base Image:** Redis 7 Alpine
- **Purpose:** Caching and queue backend
- **Persistence:** Docker volume with AOF
- **Ports:** 6379 (accessible from host)

#### Horizon Container (`thetradevisor-horizon`)
- **Base Image:** Same as app container
- **Purpose:** Laravel Horizon queue monitoring
- **Command:** `php artisan horizon`

---

## 🛠️ Make Commands

The `Makefile` provides convenient commands for Docker operations:

### Basic Operations
```bash
make help          # Show all available commands
make build         # Build all Docker images
make up            # Start all containers
make down          # Stop and remove containers
make restart       # Restart all containers
make ps            # Show running containers
make clean         # Remove all containers, images, and volumes
```

### Development Commands
```bash
make shell         # Open shell in app container
make shell-root    # Open root shell in app container
make logs          # View all container logs
make logs-app      # View app container logs
make logs-postgres # View PostgreSQL logs
make logs-redis    # View Redis logs
```

### Laravel Commands
```bash
make artisan COMMAND="migrate:status"    # Run any artisan command
make migrate                              # Run database migrations
make migrate-fresh                        # Fresh migration with seeding
make seed                                 # Seed the database
make cache-clear                          # Clear all Laravel caches
make passport-install                    # Install Laravel Passport
```

### Queue Commands
```bash
make horizon       # Start Laravel Horizon
make queue-work    # Start queue worker
```

### Database Operations
```bash
make backup        # Backup database to SQL file
make restore FILE=backup.sql  # Restore database from file
```

---

## 🔧 Configuration

### Environment Variables

The Docker setup uses `docker/.env.docker` as a template:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=thetradevisor
DB_USERNAME=thetradevisor
DB_PASSWORD=secure_password_123

REDIS_HOST=redis
REDIS_PORT=6379
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Custom Configuration

For custom configurations:

1. **Copy the environment file:**
   ```bash
   cp docker/.env.docker .env
   ```

2. **Edit the `.env` file** with your settings

3. **Restart containers:**
   ```bash
   make down && make up
   ```

---

## 📊 Performance Tuning

### PHP Configuration (`docker/php.ini`)
- Memory limit: 512MB
- OPcache enabled and optimized
- Upload limit: 64MB
- Execution time: 300 seconds

### Nginx Configuration (`docker/nginx.conf`)
- Gzip compression enabled
- Static asset caching (1 year)
- Security headers configured
- FastCGI optimization

### PostgreSQL Configuration
- Optimized for Laravel
- Connection pooling ready
- Extension support (uuid-ossp, pg_trgm)

### Redis Configuration
- AOF persistence enabled
- Memory optimization
- Connection pooling support

---

## 🚀 Production Deployment

### Step 1: Prepare Environment

```bash
# Clone repository
git clone https://github.com/abuzant/TheTradeVisor.git
cd TheTradeVisor

# Copy and customize environment
cp docker/.env.docker .env
nano .env  # Edit with your production settings
```

### Step 2: Deploy

```bash
# Build and start production containers
docker-compose -f docker-compose.yml up -d --build

# Wait for containers to be ready
sleep 30

# Run database migrations
docker-compose exec app php artisan migrate --force

# Install Laravel Passport
docker-compose exec app php artisan passport:install

# Clear and warm up caches
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### Step 3: Configure SSL (Optional)

For SSL/HTTPS setup, you can:

1. **Use Let's Encrypt with Certbot:**
   ```bash
   # Install certbot in the app container
   docker-compose exec app apk add certbot
   ```

2. **Or use reverse proxy (Traefik/Caddy):**
   Update `docker-compose.yml` to include a reverse proxy

### Step 4: Monitoring

```bash
# Check container status
docker-compose ps

# View logs
make logs

# Monitor resources
docker stats
```

---

## 🔒 Security Considerations

### Default Security Measures
- ✅ All security headers enabled
- ✅ PHP errors hidden in production
- ✅ Database credentials isolated
- ✅ Private Docker network
- ✅ Non-root user for PHP processes

### Recommended Security Updates
1. **Change default passwords** in `.env`
2. **Update APP_KEY** if needed
3. **Configure firewall** on host
4. **Set up SSL certificates**
5. **Regular security updates**:
   ```bash
   docker-compose pull
   docker-compose up -d
   ```

---

## 🐛 Troubleshooting

### Common Issues

#### Container Won't Start
```bash
# Check logs
docker-compose logs app

# Check configuration
docker-compose config

# Rebuild containers
docker-compose down && docker-compose build --no-cache
```

#### Database Connection Issues
```bash
# Check PostgreSQL status
docker-compose exec postgres pg_isready

# Test connection from app container
docker-compose exec app php artisan tinker
>>> DB::connection()->getPdo()
```

#### Permission Issues
```bash
# Fix storage permissions
docker-compose exec --user root app chown -R www-data:www-data /var/www/html/storage
docker-compose exec --user root app chmod -R 755 /var/www/html/storage
```

#### Cache Issues
```bash
# Clear all caches
make cache-clear

# Restart Redis
docker-compose restart redis
```

### Performance Issues

#### Slow Response Times
```bash
# Check OPcache status
docker-compose exec app php -i | grep opcache

# Monitor resource usage
docker stats
```

#### Database Performance
```bash
# Check PostgreSQL connections
docker-compose exec postgres psql -U thetradevisor -c "SELECT * FROM pg_stat_activity;"

# Analyze slow queries
docker-compose exec postgres psql -U thetradevisor -c "SELECT query, mean_time, calls FROM pg_stat_statements ORDER BY mean_time DESC LIMIT 10;"
```

---

## 📈 Scaling Options

### Horizontal Scaling

For high-traffic deployments, you can scale the app containers:

```bash
# Scale to 3 app instances
docker-compose up -d --scale app=3

# Add a load balancer (nginx/haproxy)
# Update docker-compose.yml accordingly
```

### Database Scaling

For database scaling:
1. **Read Replicas:** Configure PostgreSQL read replicas
2. **Connection Pooling:** Add PgBouncer container
3. **Redis Cluster:** Configure Redis cluster for high availability

### File Storage

For file storage scaling:
1. **AWS S3:** Configure Laravel to use S3
2. **NFS Mount:** Mount shared storage for multiple instances
3. **CDN:** Use CDN for static assets

---

## 🔄 Updates and Maintenance

### Updating the Application

```bash
# Pull latest code
git pull origin main

# Rebuild and restart
docker-compose build --no-cache
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate --force

# Clear caches
make cache-clear
```

### Backup Strategy

```bash
# Automated daily backup
echo "0 2 * * * cd /path/to/TheTradeVisor && make backup" | crontab -

# Manual backup
make backup

# Restore from backup
make restore FILE=backup_20251109_020000.sql
```

### Log Rotation

Docker handles log rotation automatically. To configure custom rotation:

```yaml
# In docker-compose.yml
logging:
  driver: "json-file"
  options:
    max-size: "10m"
    max-file: "3"
```

---

## 📚 Additional Resources

### Documentation Links
- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Laravel Docker Deployment](https://laravel.com/docs/deployment#docker)
- [PostgreSQL Docker](https://hub.docker.com/_/postgres)
- [Redis Docker](https://hub.docker.com/_/redis)

### Useful Commands
```bash
# Enter container for debugging
docker-compose exec app sh

# View container resource usage
docker stats $(docker-compose ps -q)

# Clean up unused Docker resources
docker system prune -f

# Export database
docker-compose exec postgres pg_dump -U thetradevisor thetradevisor > backup.sql

# Import database
docker-compose exec -T postgres psql -U thetradevisor thetradevisor < backup.sql
```

---

## ✅ Summary

TheTradeVisor's Docker deployment provides:
- ✅ **One-click setup** - No manual configuration required
- ✅ **Production ready** - Optimized for performance and security
- ✅ **Easy scaling** - Horizontal scaling support
- ✅ **Full monitoring** - Logs and metrics available
- ✅ **Backup/Restore** - Simple database backup commands
- ✅ **Development friendly** - Easy development workflow

**Deploy your trading analytics platform in minutes with Docker!** 🚀

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
