# API Subdomain Redirect - Deployment Guide

## Overview

This deployment prevents SEO duplicate content issues by redirecting non-EA traffic from `api.thetradevisor.com` to the main site `https://thetradevisor.com/`.

**Problem Solved:**
- Search engines were indexing `api.thetradevisor.com` as duplicate content
- Browsers hitting the API subdomain would see the full website
- This hurts SEO rankings and creates confusion

**Solution:**
- Legitimate EA requests with valid API keys → Work normally
- Browser/bot traffic → Redirected to main site with 301 permanent redirect
- Search engines → Blocked via robots.txt and X-Robots-Tag header

## Components

### 1. Laravel Middleware
**File:** `/www/app/Http/Middleware/RedirectApiSubdomain.php`

**Logic:**
- Checks if request is to `api.thetradevisor.com`
- Allows POST requests to API endpoints with Authorization header
- Blocks browser user agents (Mozilla, Chrome, Safari, etc.)
- Redirects all other traffic to main site

**Registered in:** `/www/bootstrap/app.php` as global middleware

### 2. Nginx Configuration
**File:** `/www/nginx-api-subdomain.conf`

**Features:**
- Redirects GET requests to root `/` immediately (before PHP)
- Serves API-specific robots.txt that blocks all search engines
- Adds `X-Robots-Tag: noindex, nofollow` header
- Redirects favicon and common paths to main site
- Allows API endpoints to pass through to Laravel

### 3. Robots.txt
**File:** `/www/public/robots-api.txt`

Explicitly blocks all major search engines from indexing the API subdomain.

## Deployment Steps

### Step 1: Backup Current Configuration

```bash
# Backup current nginx config
sudo cp /etc/nginx/sites-available/api.thetradevisor.com /etc/nginx/sites-available/api.thetradevisor.com.backup.$(date +%Y%m%d_%H%M%S)
```

### Step 2: Update Nginx Configuration

```bash
# Copy new configuration
sudo cp /var/www/thetradevisor.com/nginx-api-subdomain.conf /etc/nginx/sites-available/api.thetradevisor.com

# Test nginx configuration
sudo nginx -t
```

### Step 3: Reload Services

```bash
# Reload nginx (no downtime)
sudo systemctl reload nginx

# Clear Laravel cache
cd /var/www/thetradevisor.com
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

### Step 4: Verify Deployment

#### Test 1: Browser Access (Should Redirect)
```bash
# Should return 301 redirect to https://thetradevisor.com/
curl -I https://api.thetradevisor.com/

# Expected output:
# HTTP/2 301
# location: https://thetradevisor.com/
```

#### Test 2: API Health Check (Should Work)
```bash
# Should return 200 OK with JSON
curl -I https://api.thetradevisor.com/api/health

# Expected output:
# HTTP/2 200
# content-type: application/json
```

#### Test 3: EA Request (Should Work)
```bash
# Should work with valid API key
curl -X POST https://api.thetradevisor.com/api/v1/data/collect \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"test": "data"}'

# Expected: API response (not redirect)
```

#### Test 4: Robots.txt (Should Block)
```bash
# Should return blocking robots.txt
curl https://api.thetradevisor.com/robots.txt

# Expected output:
# User-agent: *
# Disallow: /
```

#### Test 5: Check Headers
```bash
# Should include X-Robots-Tag
curl -I https://api.thetradevisor.com/api/health | grep -i robot

# Expected output:
# x-robots-tag: noindex, nofollow
```

## Monitoring

### Check Logs for Redirects
```bash
# See redirected requests
sudo tail -f /var/log/nginx/api-thetradevisor-access.log | grep "301"

# Check Laravel logs for redirect middleware
sudo tail -f /var/www/thetradevisor.com/storage/logs/laravel.log | grep "Redirecting non-API traffic"
```

### Verify EA Traffic Still Works
```bash
# Check successful API requests
sudo tail -f /var/log/nginx/api-thetradevisor-access.log | grep "POST.*200"
```

## Rollback Procedure

If issues occur:

```bash
# Restore previous nginx config
sudo cp /etc/nginx/sites-available/api.thetradevisor.com.backup.YYYYMMDD_HHMMSS /etc/nginx/sites-available/api.thetradevisor.com

# Test and reload
sudo nginx -t
sudo systemctl reload nginx

# Comment out middleware in bootstrap/app.php
# Line 40-42: Comment out RedirectApiSubdomain middleware
```

## Expected Behavior

| Request Type | Source | Result |
|-------------|--------|--------|
| Browser GET to `/` | Chrome/Firefox | 301 → https://thetradevisor.com/ |
| Browser GET to `/dashboard` | Safari | 301 → https://thetradevisor.com/dashboard |
| GET to `/api/health` | Any | 200 OK (allowed for monitoring) |
| POST to `/api/v1/data/collect` | EA with API key | 200 OK (processed normally) |
| POST to `/api/v1/data/collect` | Browser | 301 → https://thetradevisor.com/api/v1/data/collect |
| POST without API key | Any | 401 Unauthorized (API validation) |
| Search engine crawler | Googlebot | 301 redirect + robots.txt block |

## SEO Impact

**Positive:**
- ✅ Eliminates duplicate content penalty
- ✅ Consolidates all SEO value to main domain
- ✅ Prevents confusion in search results
- ✅ Proper use of 301 permanent redirects

**No Negative Impact:**
- ✅ EA functionality unchanged
- ✅ API endpoints work normally
- ✅ No performance degradation

## Security Benefits

- Reduces attack surface (API subdomain not browsable)
- Prevents information disclosure via web interface
- Maintains API-only access pattern
- Logs suspicious access attempts

## Notes

- Middleware runs on every request (minimal overhead)
- Nginx handles obvious redirects before PHP (faster)
- Health check endpoint remains accessible for monitoring
- All redirects are 301 (permanent) for SEO
- User-Agent detection prevents browser access even with API key

## Support

If EA requests are being blocked:
1. Check API key is valid and user is active
2. Verify POST method is used (not GET)
3. Confirm Authorization header is present
4. Check user agent doesn't contain browser patterns
5. Review Laravel logs for detailed error messages

---

**Deployed:** 2025-11-17  
**Version:** 1.0  
**Status:** Production Ready
