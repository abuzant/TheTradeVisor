# Nginx Setup - Production vs Development

**Last Updated:** November 9, 2025

---

## ⚠️ Important Note for Developers

The production deployment uses a **multi-instance, load-balanced setup** with multiple nginx servers. This is **our specific production choice** and is **NOT required** for development or standard deployments.

---

## Production Setup (Our Choice)

### Architecture
```
Internet
    ↓
Cloudflare CDN
    ↓
Main Nginx (Load Balancer)
    ↓
├── Nginx Instance 1 → PHP-FPM 1
├── Nginx Instance 2 → PHP-FPM 2
└── Nginx Instance 3 → PHP-FPM 3
```

### Why We Use This
- **High traffic handling**: Multiple PHP-FPM pools
- **Zero-downtime deployments**: Can restart instances one at a time
- **Resource isolation**: Each instance has dedicated resources
- **Redundancy**: If one instance fails, others continue serving

### Configuration Files
- Main load balancer: `/etc/nginx/sites-available/thetradevisor-lb.conf`
- Instance 1: `/etc/nginx-instance1/sites-available/thetradevisor.conf`
- Instance 2: `/etc/nginx-instance2/sites-available/thetradevisor.conf`
- Instance 3: `/etc/nginx-instance3/sites-available/thetradevisor.conf`

---

## Standard Setup (Recommended for Most Users)

### Simple Architecture
```
Internet
    ↓
Nginx
    ↓
PHP-FPM
    ↓
Laravel Application
```

### This is Perfectly Fine!

**For development and most production deployments, you only need:**
- ✅ One nginx server
- ✅ One PHP-FPM pool
- ✅ Standard Laravel configuration

### Standard Configuration

**File:** `/etc/nginx/sites-available/thetradevisor`

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name thetradevisor.com www.thetradevisor.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name thetradevisor.com www.thetradevisor.com;

    root /var/www/thetradevisor.com/public;
    index index.php index.html;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/thetradevisor.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/thetradevisor.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### Enable and Start
```bash
# Create symbolic link
sudo ln -s /etc/nginx/sites-available/thetradevisor /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Restart nginx
sudo systemctl restart nginx
```

---

## When to Use Load Balancing

### ✅ Use Load Balancing If:
- You have **very high traffic** (thousands of concurrent users)
- You need **zero-downtime deployments**
- You have **multiple servers** in your infrastructure
- You need **advanced failover** capabilities

### ❌ Don't Need Load Balancing If:
- You're **developing locally**
- You have **moderate traffic** (< 1000 concurrent users)
- You're running on a **single server**
- You want **simple deployment**

---

## Quick Start for Developers

### 1. Clone Repository
```bash
git clone git@github.com:abuzant/TheTradeVisor.git
cd TheTradeVisor
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Setup Database
```bash
php artisan migrate
php artisan db:seed
```

### 5. Build Assets
```bash
npm run build
```

### 6. Serve (Development)
```bash
php artisan serve
# Visit: http://localhost:8000
```

### 7. Serve (Production - Simple)
Use the standard nginx configuration above.

---

## Comparison

| Feature | Standard Setup | Load-Balanced Setup |
|---------|---------------|---------------------|
| **Complexity** | Low | High |
| **Servers** | 1 nginx | 1 LB + 3 nginx |
| **Setup Time** | 15 minutes | 2 hours |
| **Maintenance** | Easy | Complex |
| **Cost** | Low | Higher |
| **Traffic Capacity** | 1000 users | 10,000+ users |
| **Downtime** | Brief restarts | Zero downtime |
| **Recommended For** | Most users | High-traffic sites |

---

## Our Production Specs (For Reference)

**Server:** Ubuntu 22.04 LTS  
**CPU:** 8 cores  
**RAM:** 16 GB  
**Storage:** 200 GB SSD  

**Load Balancer:**
- Nginx 1.24
- Round-robin algorithm
- Health checks every 5 seconds

**Backend Instances:**
- 3 nginx instances (ports 8081, 8082, 8083)
- 3 PHP-FPM pools (separate sockets)
- Each pool: 50 max children

**Why This Setup:**
- Handles 5,000+ concurrent users
- Zero-downtime deployments
- Automatic failover
- Resource isolation

---

## Conclusion

**For GitHub Developers:**
- ✅ Use the **standard single-nginx setup**
- ✅ It's simpler and works perfectly
- ✅ Our load-balanced setup is optional

**For Production (High Traffic):**
- Consider load balancing if needed
- Start simple, scale when necessary
- Monitor traffic and performance first

---

## Additional Resources

- [Laravel Deployment Documentation](https://laravel.com/docs/deployment)
- [Nginx Configuration Guide](https://nginx.org/en/docs/)
- [PHP-FPM Tuning](https://www.php.net/manual/en/install.fpm.configuration.php)

---

**Remember:** The multi-instance nginx setup is our production choice for high traffic. **You don't need it** for development or standard deployments!


---

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)

For project support and inquiries:  
�� [your-email@example.com](mailto:your-email@example.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)
