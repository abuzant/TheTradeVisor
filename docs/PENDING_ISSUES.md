# Pending Issues & Technical Debt

**Last Updated:** November 13, 2025  
**Priority:** HIGH

## Critical Issues (Must Fix Soon)

### 1. 419 CSRF Errors on Login/Logout ⚠️
**Status:** TEMPORARY WORKAROUND IN PLACE  
**Priority:** HIGH  
**Impact:** Security risk - CSRF protection disabled

**Current State:**
- CSRF validation disabled for `/login` and `/logout` routes
- Users experiencing intermittent 419 errors
- Workaround allows login but removes CSRF protection

**Location:**
```php
// File: /www/bootstrap/app.php
$middleware->validateCsrfTokens(except: [
    'login',
    'logout',
]);
```

**Root Cause (Suspected):**
1. Cloudflare proxy interfering with CSRF tokens
2. Load balancer (4 Nginx backends) causing token mismatch
3. Session cookie domain/path mismatch
4. Token regeneration timing issues

**Investigation Needed:**
- [ ] Check if Cloudflare is stripping/modifying CSRF tokens
- [ ] Verify session is shared correctly across all PHP-FPM pools
- [ ] Test CSRF token generation and validation flow
- [ ] Check if TrustProxies middleware is needed
- [ ] Verify `APP_URL` matches actual domain

**Proposed Solutions:**
1. **Option A:** Add TrustProxies middleware for Cloudflare IPs
2. **Option B:** Increase CSRF token lifetime
3. **Option C:** Use database session driver instead of Redis
4. **Option D:** Add Cloudflare IP ranges to trusted proxies

**Files to Check:**
- `/www/bootstrap/app.php` - CSRF exclusion
- `/www/config/session.php` - Session configuration
- `/www/.env` - APP_URL, SESSION_DOMAIN
- `/www/app/Http/Middleware/TrustProxies.php` - Proxy configuration

**Testing Steps:**
1. Remove CSRF exclusion
2. Clear all caches
3. Test login from multiple browsers/IPs
4. Monitor for 419 errors
5. Check CSRF token in form vs session

**Estimated Time:** 2-3 hours  
**Risk:** Medium (may cause login issues during testing)

---

### 2. Dashboard Caching Disabled 🔴
**Status:** DISABLED FOR DEBUGGING  
**Priority:** MEDIUM  
**Impact:** Performance - No caching on dashboard

**Current State:**
- All dashboard caching disabled to debug user bleeding issue
- Every request hits database directly
- Increased load on database and PHP-FPM

**Location:**
```php
// File: /www/app/Http/Controllers/DashboardController.php
// EMERGENCY: DISABLE CACHING TO DEBUG USER BLEEDING ISSUE
// $cacheKey = "dashboard.user.{$user->id}...";
// $dashboardData = Cache::remember($cacheKey, 120, function() use (...) {
$dashboardData = (function() use (...) {
    // ... data fetching ...
})(); // Execute immediately, no caching
```

**Why It Was Disabled:**
- To isolate user data bleeding issue
- User bleeding was caused by Cloudflare HTML caching, not Laravel cache
- Laravel cache keys already include user IDs (secure)

**Re-enabling Plan:**
1. ✅ User bleeding issue resolved (Cloudflare fix)
2. ⏳ Monitor for 24 hours to ensure stability
3. ⏳ Re-enable caching with user ID in keys
4. ⏳ Test with multiple users
5. ⏳ Monitor cache hit rates

**Cache Keys (All Include User ID):**
```php
"dashboard.user.{$user->id}.{$displayCurrency}.{$sortBy}.{$sortDirection}"
"dashboard.positions.{$user->id}"
"account.{$user->id}.{$accountId}.details.{$sortBy}.{$sortDirection}"
```

**Estimated Time:** 30 minutes  
**Risk:** Low (cache keys are secure)  
**Performance Gain:** 50-70% reduction in dashboard load time

---

### 3. Emergency Logging Active 📝
**Status:** TEMPORARY DEBUG LOGGING  
**Priority:** LOW  
**Impact:** Log file growth

**Current State:**
- Emergency logging active in DashboardController
- Logs every dashboard access with user details
- Logs account data being passed to view

**Location:**
```php
// File: /www/app/Http/Controllers/DashboardController.php
\Log::emergency('DASHBOARD ACCESS', [
    'user_id' => $user->id,
    'user_email' => $user->email,
    'user_name' => $user->name,
    'session_id' => session()->getId(),
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'auth_id' => auth()->id(),
    'auth_email' => auth()->user()->email ?? 'NULL',
]);

\Log::emergency('USER ACCOUNTS', [...]);
\Log::emergency('PASSING TO VIEW', [...]);
```

**Purpose:**
- Debug user data bleeding issue
- Verify correct user is accessing dashboard
- Confirm correct data is being passed to view

**Removal Plan:**
1. ⏳ Monitor for 24 hours after user bleeding fix
2. ⏳ Confirm no more user data bleeding reports
3. ⏳ Remove emergency logging
4. ⏳ Keep standard logging (errors, warnings)

**Estimated Time:** 15 minutes  
**Risk:** None  
**Benefit:** Cleaner logs, less disk usage

---

### 4. Cloudflare Development Mode ⚡
**Status:** ENABLED (TEMPORARY)  
**Priority:** MEDIUM  
**Impact:** No CDN caching (slower for users)

**Current State:**
- Development Mode enabled in Cloudflare (bypasses cache for 3 hours)
- Used to immediately stop HTML caching during user bleeding fix
- Auto-expires after 3 hours but can be manually disabled

**Why It's Enabled:**
- Immediate fix for user data bleeding
- Allowed testing without waiting for cache purge
- Ensures no stale cached pages

**Disable Plan:**
1. ⏳ Verify page rules are working correctly
2. ⏳ Test that authenticated pages are not cached
3. ⏳ Disable Development Mode
4. ⏳ Monitor cache status headers
5. ⏳ Verify users see correct data

**Testing:**
```bash
curl -I https://thetradevisor.com/dashboard
# Should show: cf-cache-status: DYNAMIC
# Should NOT show: cf-cache-status: HIT
```

**Estimated Time:** 15 minutes  
**Risk:** Low  
**Benefit:** Faster page loads for users (CDN caching for static assets)

---

## Medium Priority Issues

### 5. Session Configuration Review 🔧
**Status:** NEEDS REVIEW  
**Priority:** MEDIUM

**Current Settings:**
```bash
SESSION_DRIVER=redis
SESSION_LIFETIME=1440
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_EXPIRE_ON_CLOSE=false
SESSION_CONNECTION=session
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_HTTP_ONLY=true
```

**Questions to Answer:**
- [ ] Should `SESSION_DOMAIN` be set to `.thetradevisor.com`?
- [ ] Should `SESSION_ENCRYPT` be true?
- [ ] Is `SESSION_LIFETIME` (24 hours) appropriate?
- [ ] Are Redis session settings optimal?

**Testing Needed:**
- [ ] Test session persistence across page loads
- [ ] Test "Remember Me" functionality
- [ ] Test session timeout behavior
- [ ] Test session sharing across subdomains (if any)

**Estimated Time:** 1-2 hours

---

### 6. Load Balancer Session Sharing 🔄
**Status:** NEEDS VERIFICATION  
**Priority:** MEDIUM

**Environment:**
- 4 Nginx backend servers (ports 8081-8084)
- Main Nginx load balancer on port 443
- 5 PHP-FPM pools (www, pool1-4)
- Redis session storage (DB 2)

**Potential Issues:**
- Sessions may not be shared correctly between PHP-FPM pools
- Load balancer may not use sticky sessions
- Redis connection may not be consistent

**Verification Steps:**
- [ ] Check Nginx load balancer configuration
- [ ] Verify all PHP-FPM pools use same Redis session DB
- [ ] Test session persistence when switching between backends
- [ ] Check if sticky sessions are needed

**Files to Check:**
- `/etc/nginx/nginx.conf` - Load balancer config
- `/etc/nginx/sites-available/thetradevisor.com` - Site config
- `/etc/php/8.3/fpm/pool.d/*.conf` - PHP-FPM pool configs

**Estimated Time:** 2-3 hours

---

### 7. Trade Count Discrepancy 📊
**Status:** REPORTED BUT NOT INVESTIGATED  
**Priority:** LOW

**Issue:**
- User reports 289 trades in `/accounts/111` (last 30 days)
- Performance page shows 177 trades total
- Discrepancy needs investigation

**Possible Causes:**
1. Different date ranges (30 days vs all time)
2. Different filtering (trades vs deals)
3. Entry type filtering (in/out/inout)
4. Account filtering
5. Symbol filtering

**Investigation Steps:**
- [ ] Check query in `/accounts/111` page
- [ ] Check query in Performance page
- [ ] Compare date ranges
- [ ] Compare filters
- [ ] Verify data consistency

**Estimated Time:** 1 hour

---

## Low Priority / Future Improvements

### 8. Position Sync Timing ⏱️
**Status:** WORKING BUT COULD BE IMPROVED  
**Priority:** LOW

**Current State:**
- Positions sync from MT5 every X minutes (check cron)
- Open trades may show $0.00 profit until sync completes
- No real-time position updates

**Improvements:**
- [ ] Add real-time position updates via WebSocket
- [ ] Reduce sync interval for active accounts
- [ ] Add manual "Sync Now" button
- [ ] Show last sync time on page

**Estimated Time:** 4-6 hours

---

### 9. Code Cleanup 🧹
**Status:** NEEDS CLEANUP  
**Priority:** LOW

**Items:**
- [ ] Remove commented-out code in DashboardController
- [ ] Remove temporary debug logging
- [ ] Clean up old migration files
- [ ] Remove unused views
- [ ] Update PHPDoc comments

**Estimated Time:** 2-3 hours

---

### 10. Documentation Updates 📚
**Status:** ONGOING  
**Priority:** LOW

**Needed:**
- [ ] Update README with recent changes
- [ ] Document Cloudflare configuration
- [ ] Document session configuration
- [ ] Add troubleshooting guide
- [ ] Update API documentation (if any)

**Estimated Time:** 2-3 hours

---

## Summary

### Immediate Action Required:
1. **419 CSRF Errors** - Must fix to re-enable CSRF protection
2. **Dashboard Caching** - Re-enable after 24h monitoring
3. **Emergency Logging** - Remove after 24h monitoring
4. **Cloudflare Dev Mode** - Disable after verifying page rules

### Can Wait:
5. Session configuration review
6. Load balancer verification
7. Trade count discrepancy
8. Position sync improvements
9. Code cleanup
10. Documentation updates

### Estimated Total Time:
- Critical issues: 4-6 hours
- Medium priority: 4-6 hours
- Low priority: 8-12 hours
- **Total: 16-24 hours**

### Recommended Order:
1. Monitor user bleeding fix for 24 hours ✅
2. Fix 419 CSRF errors (2-3 hours)
3. Re-enable dashboard caching (30 min)
4. Remove emergency logging (15 min)
5. Disable Cloudflare Dev Mode (15 min)
6. Review session configuration (1-2 hours)
7. Verify load balancer setup (2-3 hours)
8. Investigate trade count discrepancy (1 hour)
9. Code cleanup (2-3 hours)
10. Documentation updates (2-3 hours)

---

## Notes

- All temporary workarounds are clearly marked in code with comments
- Emergency logging helps with debugging but should be removed
- CSRF protection is critical and must be re-enabled ASAP
- Dashboard caching is safe to re-enable (keys include user IDs)
- Cloudflare configuration is now correct for authenticated pages

**Last Review:** November 13, 2025  
**Next Review:** November 14, 2025 (after 24h monitoring)
