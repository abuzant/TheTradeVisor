# Affiliate Program Deployment Checklist

## Pre-Deployment

### 1. Code Review
- [ ] All migrations reviewed and tested
- [ ] Models have proper relationships
- [ ] Controllers have proper validation
- [ ] Services have error handling
- [ ] Views are responsive and accessible
- [ ] API endpoints documented
- [ ] Tests passing (100% coverage)

### 2. Database
- [ ] Backup current database
- [ ] Test migrations on staging
- [ ] Verify indexes created
- [ ] Check foreign key constraints
- [ ] Confirm data types correct

### 3. Configuration
- [ ] `.env` variables set
- [ ] Rate limiting configured
- [ ] Fraud detection thresholds set
- [ ] Commission amount verified ($1.99)
- [ ] Minimum payout set ($50)
- [ ] Cookie duration set (30 days)

### 4. Security
- [ ] CSRF protection enabled
- [ ] Rate limiting active
- [ ] SQL injection prevention verified
- [ ] XSS protection enabled
- [ ] Security headers configured
- [ ] SSL certificate valid

## Deployment Steps

### Phase 1: Database Migration
```bash
# 1. Backup database
sudo -u postgres pg_dump thetradevisor > /backup/pre-affiliate-$(date +%Y%m%d).sql

# 2. Run migrations
cd /var/www/thetradevisor.com
php artisan migrate

# 3. Verify tables created
php artisan db:show
```

**Verification:**
- [ ] All 6 tables created
- [ ] User table columns added
- [ ] Indexes created
- [ ] Foreign keys working

### Phase 2: Subdomain Setup
```bash
# 1. Configure DNS in Cloudflare
# - Add A record: join → Server IP
# - Enable proxy (orange cloud)

# 2. Run setup script
sudo ./scripts/setup-affiliate-subdomain.sh

# 3. Verify SSL
curl -I https://join.thetradevisor.com
```

**Verification:**
- [ ] DNS resolves correctly
- [ ] SSL certificate valid
- [ ] HTTPS redirect working
- [ ] Nginx configuration loaded

### Phase 3: Application Deployment
```bash
# 1. Pull latest code
git pull origin main

# 2. Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 3. Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 4. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Restart services
sudo systemctl reload php8.3-fpm
sudo systemctl reload nginx
```

**Verification:**
- [ ] No errors in logs
- [ ] Routes accessible
- [ ] Assets loading correctly

### Phase 4: Testing
```bash
# 1. Run automated tests
php artisan test --filter=Affiliate

# 2. Manual testing
# See manual testing section below
```

**Verification:**
- [ ] All tests passing
- [ ] Manual tests completed

## Post-Deployment

### 1. Smoke Tests

#### Test Affiliate Tracking
```bash
# Should redirect and set cookie
curl -I https://join.thetradevisor.com/offers/test
```
- [ ] Returns 302 redirect
- [ ] Sets affiliate_ref cookie
- [ ] Creates click record

#### Test Affiliate Login
```bash
# Should return 200
curl -I https://join.thetradevisor.com/affiliate/login
```
- [ ] Page loads
- [ ] No errors in console
- [ ] Form submits correctly

#### Test API Endpoints
```bash
# Should return JSON
curl https://thetradevisor.com/api/v1/affiliate/profile \
  -H "Cookie: laravel_session=xxx"
```
- [ ] Returns valid JSON
- [ ] Authentication working
- [ ] Data accurate

### 2. Manual Testing

#### Affiliate Registration Flow
1. [ ] Visit https://join.thetradevisor.com/affiliate/register
2. [ ] Fill form with test data
3. [ ] Submit registration
4. [ ] Verify account created in database
5. [ ] Verify email sent (if configured)
6. [ ] Login with credentials
7. [ ] Access dashboard

#### Referral Tracking Flow
1. [ ] Get referral link from dashboard
2. [ ] Open link in incognito window
3. [ ] Verify redirect to registration
4. [ ] Verify cookie set
5. [ ] Register new user account
6. [ ] Verify click marked as converted
7. [ ] Verify user has referred_by_affiliate_id

#### Conversion Flow
1. [ ] Upgrade test user to paid tier
2. [ ] Verify conversion created
3. [ ] Check fraud score
4. [ ] Verify status is pending
5. [ ] Wait 7 days (or manually update)
6. [ ] Admin approve conversion
7. [ ] Verify earnings updated

#### Payout Flow
1. [ ] Set wallet address in settings
2. [ ] Accumulate $50+ approved earnings
3. [ ] Request payout
4. [ ] Verify payout request created
5. [ ] Admin process payout
6. [ ] Enter transaction hash
7. [ ] Verify status updated to completed

#### Admin Dashboard
1. [ ] Login as admin
2. [ ] Access /admin/affiliates
3. [ ] View affiliate list
4. [ ] View conversion queue
5. [ ] Approve/reject conversions
6. [ ] View payout requests
7. [ ] Process payouts

### 3. Performance Testing

#### Load Testing
```bash
# Test affiliate click endpoint
ab -n 1000 -c 10 https://join.thetradevisor.com/offers/test

# Test API endpoint
ab -n 100 -c 5 -C "laravel_session=xxx" \
  https://thetradevisor.com/api/v1/affiliate/stats
```

**Targets:**
- [ ] Click endpoint: <200ms avg
- [ ] API endpoints: <500ms avg
- [ ] No 500 errors
- [ ] Rate limiting working

#### Database Performance
```sql
-- Check slow queries
SELECT query, mean_exec_time, calls 
FROM pg_stat_statements 
WHERE query LIKE '%affiliate%' 
ORDER BY mean_exec_time DESC 
LIMIT 10;
```
- [ ] All queries <100ms
- [ ] Indexes being used
- [ ] No N+1 queries

### 4. Monitoring Setup

#### Log Monitoring
```bash
# Set up log rotation
sudo nano /etc/logrotate.d/affiliate

# Add monitoring alerts
# (Configure based on your monitoring system)
```

**Configure Alerts For:**
- [ ] High fraud scores (>80)
- [ ] Rate limit violations
- [ ] Failed payouts
- [ ] SSL expiry (30 days)
- [ ] Database errors

#### Metrics to Track
- [ ] Daily clicks
- [ ] Daily signups
- [ ] Daily conversions
- [ ] Average fraud score
- [ ] Conversion rate
- [ ] Payout volume
- [ ] API response times

### 5. Documentation

- [ ] Update main README
- [ ] Document any custom configurations
- [ ] Create runbook for common issues
- [ ] Update API documentation
- [ ] Create admin user guide
- [ ] Create affiliate user guide

### 6. Backup Verification

```bash
# Verify backup exists
ls -lh /backup/thetradevisor/

# Test restore (on staging)
sudo -u postgres psql thetradevisor_staging < backup.sql
```

- [ ] Backup completed successfully
- [ ] Backup size reasonable
- [ ] Test restore successful

## Rollback Plan

### If Issues Occur

#### 1. Immediate Rollback
```bash
# 1. Revert code
git revert HEAD
git push origin main

# 2. Rollback database
sudo -u postgres psql thetradevisor < /backup/pre-affiliate-YYYYMMDD.sql

# 3. Restart services
sudo systemctl restart php8.3-fpm nginx

# 4. Clear caches
php artisan cache:clear
php artisan config:clear
```

#### 2. Disable Affiliate Features
```bash
# Quick disable without rollback
# Add to .env:
AFFILIATE_ENABLED=false

# Clear config cache
php artisan config:clear
```

#### 3. Disable Subdomain
```bash
# Temporarily disable
sudo rm /etc/nginx/sites-enabled/join.thetradevisor.com
sudo systemctl reload nginx
```

## Post-Launch Monitoring

### First 24 Hours
- [ ] Monitor error logs every hour
- [ ] Check affiliate registrations
- [ ] Verify click tracking working
- [ ] Monitor fraud scores
- [ ] Check API response times
- [ ] Verify no memory leaks

### First Week
- [ ] Daily error log review
- [ ] Monitor conversion rates
- [ ] Review fraud detection accuracy
- [ ] Check payout requests
- [ ] Gather user feedback
- [ ] Performance optimization

### First Month
- [ ] Weekly performance reports
- [ ] Fraud detection tuning
- [ ] User satisfaction survey
- [ ] ROI analysis
- [ ] Feature requests review
- [ ] Documentation updates

## Success Criteria

### Technical
- [ ] 99.9% uptime
- [ ] <200ms average response time
- [ ] Zero critical bugs
- [ ] <1% fraud rate
- [ ] All tests passing

### Business
- [ ] 100+ affiliate signups
- [ ] 10+ conversions
- [ ] <5% rejection rate
- [ ] Positive affiliate feedback
- [ ] ROI positive

## Sign-Off

### Development Team
- [ ] Code reviewed and approved
- [ ] Tests passing
- [ ] Documentation complete

**Signed:** _________________ Date: _________

### DevOps Team
- [ ] Infrastructure ready
- [ ] Monitoring configured
- [ ] Backups verified

**Signed:** _________________ Date: _________

### Product Owner
- [ ] Features verified
- [ ] User acceptance complete
- [ ] Ready for production

**Signed:** _________________ Date: _________

## Emergency Contacts

- **On-Call Developer:** [Contact]
- **DevOps Lead:** [Contact]
- **Database Admin:** [Contact]
- **Security Team:** [Contact]

## Notes

_Add any deployment-specific notes here_

---

**Deployment Date:** _____________
**Deployed By:** _____________
**Deployment Duration:** _____________
**Issues Encountered:** _____________
