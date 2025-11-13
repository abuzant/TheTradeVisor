# 🔴 PENDING ISSUES - Quick Reference

**Last Updated:** November 13, 2025  
**Full Details:** See `docs/PENDING_ISSUES.md`

---

## 🚨 CRITICAL (Must Fix ASAP)

### 1. 419 CSRF Errors ⚠️
- **Status:** TEMPORARY WORKAROUND - CSRF disabled for login/logout
- **Priority:** HIGH
- **Time:** 2-3 hours
- **Risk:** Security vulnerability
- **File:** `bootstrap/app.php` line 19-24
- **Action:** Investigate Cloudflare/load balancer, re-enable CSRF

### 2. Dashboard Caching Disabled 🔴
- **Status:** DISABLED FOR DEBUGGING
- **Priority:** MEDIUM
- **Time:** 30 minutes
- **Impact:** Increased DB load
- **File:** `app/Http/Controllers/DashboardController.php`
- **Action:** Re-enable after 24h monitoring

### 3. Emergency Logging Active 📝
- **Status:** TEMPORARY DEBUG LOGGING
- **Priority:** LOW
- **Time:** 15 minutes
- **Impact:** Large log files
- **File:** `app/Http/Controllers/DashboardController.php`
- **Action:** Remove after 24h stability

### 4. Cloudflare Dev Mode ⚡
- **Status:** ENABLED (TEMPORARY)
- **Priority:** MEDIUM
- **Time:** 15 minutes
- **Impact:** No CDN caching
- **Location:** Cloudflare Dashboard
- **Action:** Disable after page rules verified

---

## 📋 MEDIUM PRIORITY

### 5. Session Configuration Review 🔧
- **Time:** 1-2 hours
- **Questions:** Should SESSION_DOMAIN be set? Should SESSION_ENCRYPT be true?
- **Action:** Review and optimize session settings

### 6. Load Balancer Session Sharing 🔄
- **Time:** 2-3 hours
- **Environment:** 4 Nginx backends, 5 PHP-FPM pools
- **Action:** Verify session sharing across backends

### 7. Trade Count Discrepancy 📊
- **Issue:** 289 trades vs 177 trades
- **Time:** 1 hour
- **Action:** Investigate query differences

---

## ⏰ TIMELINE

### Next 24 Hours:
- ✅ Monitor user bleeding fix
- ⏳ Verify stability

### Next Week:
1. Fix CSRF errors (2-3h)
2. Re-enable caching (30m)
3. Remove logging (15m)
4. Disable Dev Mode (15m)
5. Review sessions (1-2h)

### Next Month:
6. Verify load balancer (2-3h)
7. Fix trade count (1h)
8. Code cleanup (2-3h)
9. Update docs (2-3h)

---

## 🎯 RECOMMENDED ORDER

1. **Wait 24 hours** - Monitor stability ✅
2. **Fix CSRF** - Critical security issue (2-3h)
3. **Re-enable cache** - Performance improvement (30m)
4. **Remove logging** - Cleanup (15m)
5. **Disable Dev Mode** - Restore CDN (15m)
6. **Review sessions** - Optimization (1-2h)
7. **Everything else** - As time permits

---

## 📞 QUICK COMMANDS

### Check Logs:
```bash
tail -f /www/storage/logs/laravel.log
```

### Clear Caches:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Restart Services:
```bash
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
```

### Check Redis:
```bash
redis-cli -n 1 DBSIZE  # Cache DB
redis-cli -n 2 DBSIZE  # Session DB
```

---

## 📚 DOCUMENTATION

- **Full Details:** `docs/PENDING_ISSUES.md`
- **Security Fix:** `docs/USER_DATA_BLEEDING_FIX.md`
- **Trade Grouping:** `docs/ADMIN_TRADES_GROUPING.md`
- **Session Summary:** `docs/SESSION_SUMMARY_NOV_13_2025.md`
- **GitHub Issue:** `docs/GITHUB_ISSUE_USER_DATA_BLEEDING.md`

---

**Total Estimated Time:** 16-24 hours  
**Critical Issues:** 4  
**Medium Priority:** 3  
**Low Priority:** 3

**Next Session Focus:** Fix CSRF errors and re-enable caching
