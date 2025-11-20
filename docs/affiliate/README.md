# TheTradeVisor Affiliate Program - Complete Documentation

## Table of Contents
1. [Overview](#overview)
2. [System Architecture](#system-architecture)
3. [Database Schema](#database-schema)
4. [Features](#features)
5. [Installation](#installation)
6. [Configuration](#configuration)
7. [Usage](#usage)
8. [API Reference](#api-reference)
9. [Testing](#testing)
10. [Security](#security)
11. [Troubleshooting](#troubleshooting)

## Overview

The TheTradeVisor Affiliate Program is a comprehensive referral system that allows anyone to earn $1.99 USDT for every paid signup they refer. The system includes:

- **Automatic affiliate account creation** for all traders
- **Unified credentials** (same email/password for trader and affiliate accounts)
- **Multi-layer fraud detection** (6 different checks)
- **Real-time analytics** and performance tracking
- **USDT payouts** (TRC20/ERC20)
- **Admin approval workflow** for conversions and payouts
- **Dedicated subdomain** (join.thetradevisor.com)
- **RESTful API** for programmatic access

### Key Metrics
- **Commission**: $1.99 USD per paid signup
- **Minimum Payout**: $50.00 USD
- **Cooling Period**: 7 days before conversion approval
- **Rate Limit**: 10 clicks/minute per IP
- **Cookie Duration**: 30 days

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    User Journey Flow                         │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│  1. Click Tracking (join.thetradevisor.com/offers/{slug})  │
│     - IP logging                                             │
│     - Fingerprinting                                         │
│     - UTM parameter capture                                  │
│     - 30-day cookie set                                      │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│  2. User Registration (thetradevisor.com/register)          │
│     - Affiliate ID linked                                    │
│     - Click marked as converted                              │
│     - Auto-create affiliate account for new user             │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│  3. Subscription Upgrade (User → Paid Tier)                 │
│     - Conversion record created ($1.99)                      │
│     - Fraud detection runs                                   │
│     - Status: pending (7-day cooling)                        │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│  4. Admin Review (After 7 days)                             │
│     - Manual approval/rejection                              │
│     - Fraud score review                                     │
│     - Status: approved/rejected                              │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│  5. Payout Request (Minimum $50)                            │
│     - Affiliate requests withdrawal                          │
│     - Admin processes USDT payment                           │
│     - Transaction hash recorded                              │
└─────────────────────────────────────────────────────────────┘
```

## Database Schema

### Tables Created
1. **affiliates** - Core affiliate accounts
2. **affiliate_clicks** - Click tracking with UTM params
3. **affiliate_conversions** - Paid signup tracking
4. **affiliate_payouts** - USDT payment records
5. **affiliate_analytics** - Daily aggregated metrics
6. **users** (modified) - Added affiliate_id, referred_by_affiliate_id

### Entity Relationships
```
User ──┬── hasOne → Affiliate
       └── belongsTo → Affiliate (referred_by)

Affiliate ──┬── hasMany → AffiliateClick
            ├── hasMany → AffiliateConversion
            ├── hasMany → AffiliatePayout
            └── hasMany → AffiliateAnalytic

AffiliateClick ──┬── belongsTo → Affiliate
                 └── belongsTo → User (conversion_user_id)

AffiliateConversion ──┬── belongsTo → Affiliate
                      ├── belongsTo → AffiliateClick
                      └── belongsTo → User

AffiliatePayout ──┬── belongsTo → Affiliate
                  └── belongsTo → User (processed_by)

AffiliateAnalytic ──└── belongsTo → Affiliate
```

## Features

### For Affiliates
- ✅ Instant account creation
- ✅ Unique referral link with custom slug
- ✅ Real-time click tracking
- ✅ UTM campaign builder
- ✅ QR code generator
- ✅ Performance analytics dashboard
- ✅ Geographic distribution reports
- ✅ Campaign performance metrics
- ✅ Payout history
- ✅ USDT wallet management
- ✅ RESTful API access

### For Admins
- ✅ Affiliate management dashboard
- ✅ Conversion approval queue
- ✅ Fraud detection dashboard
- ✅ Payout processing interface
- ✅ Performance leaderboard
- ✅ Suspicious activity alerts
- ✅ Bulk operations
- ✅ Detailed audit logs

### Security Features
- ✅ Multi-layer fraud detection
- ✅ IP-based rate limiting
- ✅ Browser fingerprinting
- ✅ Bot detection
- ✅ Self-referral prevention
- ✅ Rapid conversion detection
- ✅ 7-day cooling period
- ✅ Manual admin approval

## Installation

### Prerequisites
- PHP 8.3+
- PostgreSQL 16+
- Nginx
- Composer
- Node.js & NPM

### Step 1: Run Migrations
```bash
cd /var/www/thetradevisor.com
php artisan migrate
```

### Step 2: Register Event Listener
The `EventServiceProvider` is already configured. Verify:
```bash
php artisan event:list | grep Affiliate
```

### Step 3: Configure Subdomain
```bash
sudo ./scripts/setup-affiliate-subdomain.sh
```

Follow the prompts to:
- Configure DNS in Cloudflare
- Obtain SSL certificate
- Configure Nginx
- Set up auto-renewal

### Step 4: Test Installation
```bash
# Test affiliate tracking
curl -I https://join.thetradevisor.com/offers/test

# Test affiliate login
curl -I https://join.thetradevisor.com/affiliate/login

# Run tests
php artisan test --filter=Affiliate
```

## Configuration

### Environment Variables
Add to `.env`:
```env
# Affiliate Program Settings
AFFILIATE_COMMISSION=1.99
AFFILIATE_MIN_PAYOUT=50.00
AFFILIATE_COOKIE_DAYS=30
AFFILIATE_COOLING_PERIOD_DAYS=7

# Rate Limiting
AFFILIATE_CLICK_RATE_LIMIT=10  # per minute per IP

# Fraud Detection
AFFILIATE_FRAUD_THRESHOLD=50
AFFILIATE_AUTO_APPROVE=false
```

### Cloudflare DNS
1. Add A record: `join` → Server IP
2. Enable proxy (orange cloud)
3. SSL/TLS: Full (strict)
4. Enable: Always Use HTTPS, Auto HTTPS Rewrites

### Nginx Configuration
Located at: `/etc/nginx/sites-available/join.thetradevisor.com`

Key settings:
- Rate limiting: 10 req/min per IP
- SSL: TLS 1.2+
- Security headers enabled
- Static file caching: 1 year

## Usage

### For Affiliates

#### 1. Registration
```
https://join.thetradevisor.com/affiliate/register
```

#### 2. Get Referral Link
```
https://join.thetradevisor.com/offers/{your-slug}
```

#### 3. Track Performance
```
https://join.thetradevisor.com/affiliate/dashboard
https://join.thetradevisor.com/affiliate/analytics
```

#### 4. Request Payout
Minimum $50 approved earnings required.

### For Admins

#### 1. View Affiliates
```
https://thetradevisor.com/admin/affiliates
```

#### 2. Review Conversions
```
https://thetradevisor.com/admin/affiliates/conversions/list
```

#### 3. Process Payouts
```
https://thetradevisor.com/admin/affiliates/payouts/list
```

### Programmatic Access

See [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) for complete API reference.

Example:
```bash
curl -X GET "https://thetradevisor.com/api/v1/affiliate/stats?days=30" \
  -H "Cookie: laravel_session=YOUR_SESSION"
```

## API Reference

Full API documentation: [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)

### Available Endpoints
- `GET /api/v1/affiliate/profile` - Get affiliate profile
- `GET /api/v1/affiliate/stats` - Get statistics
- `GET /api/v1/affiliate/performance` - Get daily performance
- `GET /api/v1/affiliate/campaigns` - Get top campaigns
- `GET /api/v1/affiliate/geo` - Get geographic distribution
- `GET /api/v1/affiliate/clicks` - Get recent clicks
- `GET /api/v1/affiliate/conversions` - Get conversions
- `GET /api/v1/affiliate/payouts` - Get payout history

## Testing

### Run All Tests
```bash
php artisan test
```

### Run Affiliate Tests Only
```bash
php artisan test --filter=Affiliate
```

### Test Coverage
- ✅ Click tracking
- ✅ Cookie setting
- ✅ User registration with referral
- ✅ Conversion creation
- ✅ Fraud detection
- ✅ Inactive affiliate handling
- ✅ Invalid slug handling

### Manual Testing Checklist
- [ ] Click affiliate link → verify click recorded
- [ ] Register with affiliate cookie → verify user linked
- [ ] Upgrade to paid → verify conversion created
- [ ] Check fraud score → verify within threshold
- [ ] Request payout → verify admin notification
- [ ] Admin approve conversion → verify earnings updated
- [ ] Admin process payout → verify USDT sent

## Security

### Fraud Detection System

#### 6-Layer Detection
1. **IP-based** (>50 clicks/24h = +30 score)
2. **Fingerprint duplication** (+25 score)
3. **Self-referral** (+50 score)
4. **Bot detection** (+40 score)
5. **Rapid conversions** (+35 score)
6. **No referrer** (+10 score)

**Threshold**: 50+ = suspicious

#### Rate Limiting
- 10 clicks/minute per IP
- Enforced at Nginx level
- Returns HTTP 429 if exceeded

#### Cooling Period
- 7 days before conversion eligible for approval
- Prevents quick fraud schemes
- Allows time for chargebacks/refunds

### Best Practices
1. Monitor suspicious activity daily
2. Review high fraud scores manually
3. Verify wallet addresses before payout
4. Keep SSL certificates updated
5. Regular security audits
6. Monitor rate limit violations

## Troubleshooting

### Common Issues

#### 1. Clicks Not Tracking
**Symptoms**: No records in `affiliate_clicks` table

**Solutions**:
- Check rate limiting: `tail -f /var/log/nginx/join.thetradevisor.com-error.log`
- Verify affiliate is active: `SELECT is_active FROM affiliates WHERE slug = 'xxx'`
- Check session configuration
- Clear Redis cache: `php artisan cache:clear`

#### 2. Conversions Not Creating
**Symptoms**: User upgraded but no conversion record

**Solutions**:
- Verify event listener registered: `php artisan event:list`
- Check user has `referred_by_affiliate_id`: `SELECT referred_by_affiliate_id FROM users WHERE id = xxx`
- Review Laravel logs: `tail -f storage/logs/laravel.log`
- Manually fire event for testing

#### 3. Payouts Failing
**Symptoms**: Payout stuck in pending

**Solutions**:
- Verify minimum threshold met
- Check wallet address format
- Ensure admin has processed
- Review `affiliate_payouts` table status

#### 4. SSL Certificate Issues
**Symptoms**: HTTPS not working

**Solutions**:
```bash
# Check certificate
sudo certbot certificates

# Renew if needed
sudo certbot renew

# Reload nginx
sudo systemctl reload nginx
```

#### 5. High Fraud Scores
**Symptoms**: Legitimate conversions marked suspicious

**Solutions**:
- Review fraud detection thresholds in `ClickFraudDetector.php`
- Adjust scoring weights
- Whitelist known IPs if needed
- Manual admin override available

### Logs to Check
```bash
# Nginx access logs
tail -f /var/log/nginx/join.thetradevisor.com-access.log

# Nginx error logs
tail -f /var/log/nginx/join.thetradevisor.com-error.log

# Laravel logs
tail -f /var/www/thetradevisor.com/storage/logs/laravel.log

# PHP-FPM logs
tail -f /var/log/php8.3-fpm.log
```

### Database Queries for Debugging
```sql
-- Check affiliate stats
SELECT * FROM affiliates WHERE slug = 'xxx';

-- Recent clicks
SELECT * FROM affiliate_clicks 
WHERE affiliate_id = xxx 
ORDER BY clicked_at DESC LIMIT 10;

-- Pending conversions
SELECT * FROM affiliate_conversions 
WHERE status = 'pending' 
ORDER BY converted_at DESC;

-- Suspicious conversions
SELECT * FROM affiliate_conversions 
WHERE is_suspicious = true 
ORDER BY fraud_score DESC;
```

## Maintenance

### Daily Tasks
- [ ] Review pending conversions
- [ ] Check suspicious activity
- [ ] Process payout requests
- [ ] Monitor error logs

### Weekly Tasks
- [ ] Review fraud detection effectiveness
- [ ] Analyze top performing affiliates
- [ ] Check rate limit violations
- [ ] Backup database

### Monthly Tasks
- [ ] Generate performance reports
- [ ] Review and adjust fraud thresholds
- [ ] Audit payout accuracy
- [ ] Update documentation

## Support

### Documentation
- Main README: [README.md](./README.md)
- API Docs: [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)
- Subdomain Setup: [SUBDOMAIN_SETUP.md](./SUBDOMAIN_SETUP.md)

### Contact
- Technical Issues: dev@thetradevisor.com
- Affiliate Support: affiliates@thetradevisor.com
- Admin Questions: admin@thetradevisor.com

## Changelog

### Version 1.0.0 (2025-01-20)
- Initial release
- Complete affiliate system
- Fraud detection
- Admin dashboard
- API endpoints
- Subdomain configuration
- Comprehensive documentation

## License
Proprietary - TheTradeVisor © 2025
