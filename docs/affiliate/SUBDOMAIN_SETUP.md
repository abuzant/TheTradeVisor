# Affiliate Subdomain Setup Guide

## Overview
This guide covers the complete setup of `join.thetradevisor.com` for the affiliate program.

## Prerequisites
- Root/sudo access to the server
- Cloudflare account with thetradevisor.com domain
- Server IP address: (check with `curl ifconfig.me`)

## Step 1: Cloudflare DNS Configuration

### Add DNS Record
1. Log in to Cloudflare Dashboard
2. Select `thetradevisor.com` domain
3. Go to **DNS** → **Records**
4. Click **Add record**
5. Configure:
   - **Type**: A
   - **Name**: join
   - **IPv4 address**: [Your server IP]
   - **Proxy status**: Proxied (orange cloud)
   - **TTL**: Auto
6. Click **Save**

### SSL/TLS Settings
1. Go to **SSL/TLS** → **Overview**
2. Set encryption mode to: **Full (strict)**
3. Go to **SSL/TLS** → **Edge Certificates**
4. Enable:
   - ✅ Always Use HTTPS
   - ✅ Automatic HTTPS Rewrites
   - ✅ Minimum TLS Version: 1.2
   - ✅ TLS 1.3

### Security Settings
1. Go to **Security** → **Settings**
2. Set Security Level: **Medium**
3. Enable:
   - ✅ Bot Fight Mode
   - ✅ Challenge Passage (30 minutes)

### Page Rules (Optional but Recommended)
1. Go to **Rules** → **Page Rules**
2. Create rule for `join.thetradevisor.com/offers/*`:
   - Cache Level: Bypass
   - Security Level: Medium
   - Browser Integrity Check: On

## Step 2: Server Configuration

### Run Setup Script
```bash
cd /var/www/thetradevisor.com
sudo ./scripts/setup-affiliate-subdomain.sh
```

The script will:
1. Check DNS configuration
2. Install Certbot (if needed)
3. Obtain SSL certificate from Let's Encrypt
4. Configure Nginx
5. Set up auto-renewal
6. Test configuration

### Manual Setup (if script fails)

#### 1. Install Certbot
```bash
sudo apt-get update
sudo apt-get install -y certbot python3-certbot-nginx
```

#### 2. Obtain SSL Certificate
```bash
sudo systemctl stop nginx
sudo certbot certonly --standalone \
    --agree-tos \
    --email admin@thetradevisor.com \
    -d join.thetradevisor.com
```

#### 3. Configure Nginx
```bash
sudo cp nginx-affiliate-subdomain.conf /etc/nginx/sites-available/join.thetradevisor.com
sudo ln -s /etc/nginx/sites-available/join.thetradevisor.com /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl start nginx
sudo systemctl reload nginx
```

#### 4. Set up Auto-Renewal
```bash
sudo certbot renew --dry-run
```

## Step 3: Verification

### Test DNS Resolution
```bash
dig join.thetradevisor.com
nslookup join.thetradevisor.com
```

### Test SSL Certificate
```bash
curl -I https://join.thetradevisor.com
openssl s_client -connect join.thetradevisor.com:443 -servername join.thetradevisor.com
```

### Test Application Routes
```bash
# Test affiliate tracking
curl -I https://join.thetradevisor.com/offers/test

# Test affiliate login page
curl -I https://join.thetradevisor.com/affiliate/login

# Test affiliate register page
curl -I https://join.thetradevisor.com/affiliate/register
```

### Browser Testing
1. Visit: https://join.thetradevisor.com/affiliate/login
2. Check SSL certificate (should show valid)
3. Test registration flow
4. Test referral link tracking

## Step 4: Monitoring

### Nginx Logs
```bash
# Access logs
tail -f /var/log/nginx/join.thetradevisor.com-access.log

# Error logs
tail -f /var/log/nginx/join.thetradevisor.com-error.log
```

### SSL Certificate Status
```bash
sudo certbot certificates
```

### Certificate Expiry
```bash
echo | openssl s_client -servername join.thetradevisor.com -connect join.thetradevisor.com:443 2>/dev/null | openssl x509 -noout -dates
```

## Troubleshooting

### DNS Not Resolving
- Wait 5-10 minutes for DNS propagation
- Clear local DNS cache: `sudo systemd-resolve --flush-caches`
- Check Cloudflare DNS settings

### SSL Certificate Error
- Ensure DNS is properly configured before running Certbot
- Check port 80 is accessible: `sudo netstat -tulpn | grep :80`
- Verify firewall rules: `sudo ufw status`

### 502 Bad Gateway
- Check PHP-FPM status: `sudo systemctl status php8.3-fpm`
- Check Nginx error logs
- Verify socket path in nginx config

### Rate Limiting Issues
- Check rate limit zone in nginx config
- Monitor: `sudo tail -f /var/log/nginx/join.thetradevisor.com-error.log | grep limit_req`
- Adjust limits if needed

## Maintenance

### SSL Certificate Renewal
Certificates auto-renew via cron. Manual renewal:
```bash
sudo certbot renew
sudo systemctl reload nginx
```

### Update Nginx Configuration
```bash
sudo nano /etc/nginx/sites-available/join.thetradevisor.com
sudo nginx -t
sudo systemctl reload nginx
```

### Backup Configuration
```bash
sudo cp /etc/nginx/sites-available/join.thetradevisor.com \
    /var/www/thetradevisor.com/backups/nginx-join-$(date +%Y%m%d).conf
```

## Security Checklist
- ✅ SSL/TLS enabled (Let's Encrypt)
- ✅ HTTPS redirect configured
- ✅ Security headers added
- ✅ Rate limiting enabled (10 req/min)
- ✅ Cloudflare proxy enabled
- ✅ Bot protection enabled
- ✅ Hidden files blocked
- ✅ Sensitive directories protected

## Performance Optimization
- Static file caching: 1 year
- SSL session caching: 10 minutes
- Cloudflare CDN enabled
- Gzip compression (via Cloudflare)

## Support
For issues, check:
1. Nginx error logs
2. Laravel logs: `/var/www/thetradevisor.com/storage/logs/`
3. System logs: `journalctl -u nginx -f`

## Next Steps
After subdomain is live:
1. Test complete affiliate flow
2. Monitor click tracking
3. Test conversion tracking
4. Verify analytics collection
5. Test payout workflow
