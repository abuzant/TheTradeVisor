# 🚀 TheTradeVisor v1.4.0 - Release Notes

**Release Date**: November 17, 2025  
**Tag**: v1.4.0  
**Status**: ⚠️ **SUPERSEDED BY v1.7.0**

> **⚠️ IMPORTANT NOTE:** The pricing model described in this release has been completely replaced in v1.7.0 (November 21, 2025). TheTradeVisor is now **completely FREE** for all users with unlimited accounts. Access to extended time periods is controlled by broker whitelist status, not payment. See [v1.7.0 Release Notes](RELEASE_NOTES_v1.7.0_NOV21_2025.md) for current pricing model.

---

## 📋 Overview

Version 1.4.0 introduces critical account limit enforcement, a simplified pricing model, and enhanced user experience improvements. This release focuses on preventing subscription abuse while making the platform more accessible with a clear pay-per-account model.

**NOTE:** This pricing model was active from November 17-20, 2025 and has since been replaced with a completely free model.

---

## 🎯 What's New

### 🔒 Account Limit Enforcement

**Prevent Subscription Abuse**

Users can no longer bypass account limits by connecting unlimited trading accounts with a single API key. The system now enforces subscription tier limits at multiple levels.

**Implementation:**
- ✅ **Controller-level check** - Immediate rejection in `DataCollectionController`
- ✅ **Job-level safety net** - Secondary check in `ProcessTradingData` job
- ✅ **Clear error messages** - Users see exactly why and how to upgrade
- ✅ **Comprehensive logging** - All violations logged for monitoring

**Error Response Example:**
```json
{
  "success": false,
  "error": "ACCOUNT_LIMIT_EXCEEDED",
  "message": "Account limit reached. You have 1 account(s) but your free plan allows 1. Please upgrade at https://thetradevisor.com/pricing to add more accounts.",
  "current_accounts": 1,
  "max_accounts": 1,
  "subscription_tier": "free",
  "upgrade_url": "https://thetradevisor.com/pricing"
}
```

**Impact:**
- Prevents free users from connecting unlimited accounts
- Enforces fair usage across all tiers
- Protects revenue model
- Clear upgrade path for users

---

### 🔄 Auto-Redirect Authenticated Users

**Better User Experience**

Logged-in users are now automatically redirected to their dashboard when trying to access guest-only pages like login or registration.

**Protected Routes:**
- `/login` → Redirects to `/dashboard`
- `/register` → Redirects to `/dashboard`
- `/forgot-password` → Redirects to `/dashboard`

**Benefits:**
- Prevents confusion
- Saves user time
- Logical, expected behavior
- Cleaner user flow

**Implementation:**
- Custom `RedirectIfAuthenticated` middleware
- Registered as 'guest' alias
- Zero performance impact

---

### 💰 Pricing Model Overhaul

**Simplified, Transparent Pricing**

We've completely restructured our pricing to be simpler and more transparent.

**Old Model (Removed):**
- ❌ Free: 1 account
- ❌ PRO: $4.99/month for 3 accounts
- ❌ Enterprise: Custom pricing

**New Model:**
- ✅ **Free**: 1 account, full features, forever
- ✅ **Pay-Per-Account**: $9.99 one-time per additional account
- ✅ **Enterprise**: Unlimited accounts, custom solutions

**Key Changes:**
- Removed PRO tier completely
- One-time payment instead of monthly subscription
- First account always FREE
- No recurring fees for additional accounts
- Lifetime access per account purchased

**Why This Change:**
- Simpler for users to understand
- More affordable long-term
- No surprise recurring charges
- Better value proposition
- Scalable business model

---

## 🔧 Technical Improvements

### Code Quality

**PHPUnit Tests:**
- `AccountLimitEnforcementTest` - 5 comprehensive tests
- `PricingUpdateTest` - 10 validation tests
- Full coverage of new features

**Clean Code:**
- Well-documented middleware
- Clear error handling
- Comprehensive logging
- Maintainable structure

### Security

**Enhanced Protection:**
- Account limits enforced at multiple levels
- Proper authentication checks
- Clear error messages without exposing internals
- Comprehensive audit logging

### Performance

**Zero Impact:**
- Single count query for limit checks
- Only runs for new accounts
- Cached authentication checks
- Optimized middleware

---

## 📝 Documentation Updates

### New Documentation

**INSTALLATION.md** (NEW)
- Complete installation guide
- Docker one-click deployment
- Manual installation steps
- System requirements
- Configuration guide
- Troubleshooting section

### Updated Documentation

**README.md**
- Updated to v1.4.0
- Added subscription tiers table
- New features highlighted
- Updated badges

**CHANGELOG.md**
- Detailed v1.4.0 changes
- Organized by category
- Clear descriptions

---

## 📦 Files Changed

### Modified Files (15):
1. `README.md` - Version, features, subscription tiers
2. `docs/CHANGELOG.md` - v1.4.0 changes
3. `app/Http/Controllers/Admin/UserManagementController.php` - Removed 'pro' tier
4. `app/Http/Controllers/Api/DataCollectionController.php` - Account limit check
5. `app/Jobs/ProcessTradingData.php` - Account limit safety net
6. `bootstrap/app.php` - Registered RedirectIfAuthenticated middleware
7. `resources/views/admin/dashboard.blade.php` - Removed PRO badge
8. `resources/views/admin/users/edit.blade.php` - Removed PRO option
9. `resources/views/admin/users/index.blade.php` - Removed PRO badge
10. `resources/views/admin/users/show.blade.php` - Removed PRO badge
11. `resources/views/public/faq.blade.php` - Updated pricing
12. `resources/views/public/pricing.blade.php` - New pricing model
13. `resources/views/settings/api-key.blade.php` - Clarified account management
14. `RELEASE_NOTES_v1.4.0.md` - This file
15. `RELEASE_v1.3.0_SUMMARY.md` - Previous release summary

### New Files (4):
1. `INSTALLATION.md` - Comprehensive installation guide
2. `app/Http/Middleware/RedirectIfAuthenticated.php` - Custom middleware
3. `tests/Feature/AccountLimitEnforcementTest.php` - Feature tests
4. `tests/Unit/PricingUpdateTest.php` - Unit tests

### Statistics:
- **Total Changes**: 1,107 insertions, 86 deletions
- **Files Modified**: 15
- **Files Created**: 4
- **Tests Added**: 15 tests

---

## 🚀 Upgrade Instructions

### For Existing Installations:

```bash
# 1. Pull latest changes
git pull origin main
git checkout v1.4.0

# 2. Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 3. Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 4. Recache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Restart services
sudo systemctl restart horizon
sudo systemctl reload php8.3-fpm
sudo systemctl reload nginx
```

### For New Installations:

See [INSTALLATION.md](INSTALLATION.md) for complete setup instructions.

**Docker Quick Start:**
```bash
git clone https://github.com/abuzant/TheTradeVisor.git
cd TheTradeVisor
git checkout v1.4.0
docker-compose up -d
```

---

## 🧪 Testing

### Automated Tests

All tests passing ✅

**Account Limit Tests:**
- Free user can connect 1st account ✅
- Free user cannot connect 2nd account ✅
- Basic user can connect up to limit ✅
- Basic user cannot exceed limit ✅
- Enterprise user unlimited accounts ✅

**Pricing Tests:**
- PRO tier removed from validation ✅
- Admin views updated ✅
- Pricing page shows correct info ✅
- FAQ updated ✅

### Manual Verification

- ✅ Account limits enforced correctly
- ✅ Redirect middleware works
- ✅ Error messages clear and helpful
- ✅ Logging comprehensive
- ✅ No breaking changes

---

## ⚠️ Breaking Changes

### None! 🎉

This release is **100% backward compatible**:
- Existing accounts unaffected
- No database migrations required
- No configuration changes needed
- Existing API keys continue to work
- All features remain functional

### Migration Notes

**For Users:**
- Existing PRO users remain on their plan
- No action required
- Grandfathered accounts preserved

**For Admins:**
- PRO tier removed from new user creation
- Existing PRO users can be manually managed
- Update admin workflows if needed

---

## 📊 Impact Analysis

### User Experience
- ✅ Clearer pricing model
- ✅ Better account management
- ✅ Prevents confusion with redirects
- ✅ Clear error messages
- ✅ Obvious upgrade path

### Security
- ✅ Account limits enforced
- ✅ Prevents subscription bypass
- ✅ Proper authentication checks
- ✅ Comprehensive logging

### Business
- ✅ Simplified pricing (no PRO tier)
- ✅ Clear monetization ($9.99/account)
- ✅ Prevents abuse
- ✅ Scalable model
- ✅ Better conversion funnel

### Technical
- ✅ Clean, maintainable code
- ✅ Comprehensive tests
- ✅ Good documentation
- ✅ Production-ready
- ✅ Zero performance impact

---

## 🎯 Subscription Tiers

| Tier | Price | Accounts | Features |
|------|-------|----------|----------|
| **Free** | $0 | 1 account | Full analytics, real-time data, global insights |
| **Pay-Per-Account** | $9.99 one-time | Unlimited | Add accounts as needed, lifetime access per account |
| **Enterprise** | Custom | Unlimited | Custom solutions, priority support, dedicated infrastructure |

**Key Points:**
- ✅ First account is **FREE** forever
- ✅ Additional accounts: **$9.99 one-time payment** (no monthly fees!)
- ✅ Account limits enforced to prevent abuse
- ✅ All tiers include full platform features
- ✅ Enterprise tier for trading firms and institutions

---

## 🐛 Bug Fixes

### Account Management
- Fixed: Users could bypass account limits with single API key
- Fixed: No clear error message when limit exceeded
- Fixed: Logged-in users could access guest pages

### UI/UX
- Fixed: PRO tier still showing in admin views
- Fixed: Confusing pricing on pricing page
- Fixed: API key page unclear about account management

---

## 🔮 What's Next

### Planned for v1.5.0:
- Payment integration (Stripe)
- Automated account purchase flow
- Email notifications for limit violations
- Enhanced analytics dashboard
- Mobile app (iOS/Android)

### Under Consideration:
- Multi-currency support
- Advanced broker analytics
- Social trading features
- API marketplace

---

## 📞 Support & Feedback

### Getting Help

- 📧 **Email**: [hello@thetradevisor.com](mailto:hello@thetradevisor.com)
- 🌐 **Website**: [https://thetradevisor.com](https://thetradevisor.com)
- 📖 **Documentation**: [docs/](docs/)
- 🐛 **Issues**: [GitHub Issues](https://github.com/abuzant/TheTradeVisor/issues)

### Report Issues

Found a bug? Please report it:
1. Check existing issues first
2. Create detailed bug report
3. Include steps to reproduce
4. Attach logs if possible

### Feature Requests

Have an idea? We'd love to hear it:
1. Open a GitHub issue
2. Describe the feature
3. Explain the use case
4. Vote on existing requests

---

## 🙏 Acknowledgments

### Contributors

Special thanks to everyone who contributed to this release through:
- Bug reports
- Feature suggestions
- Code reviews
- Testing
- Documentation

### Technologies

Built with:
- [Laravel 11](https://laravel.com) - The PHP Framework
- [PostgreSQL 16](https://postgresql.org) - Advanced Database
- [Redis 7](https://redis.io) - In-Memory Data Store
- [Tailwind CSS](https://tailwindcss.com) - Utility-First CSS
- [Alpine.js](https://alpinejs.dev) - Lightweight JavaScript
- [Chart.js](https://chartjs.org) - Beautiful Charts

---

## 📜 License

This project is proprietary software. All rights reserved.

For licensing inquiries: [hello@thetradevisor.com](mailto:hello@thetradevisor.com)

---

## 👨‍💻 Author

**Ruslan Abuzant**  
📧 [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 [https://abuzant.com](https://abuzant.com)  
💼 [LinkedIn](https://linkedin.com/in/ruslanabuzant)

❤️ From Palestine to the world with Love

---

## ✅ Release Checklist

- [x] Code changes implemented
- [x] Tests created and passing
- [x] Documentation updated
- [x] CHANGELOG updated
- [x] README updated
- [x] Installation guide created
- [x] Release notes created
- [x] Version bumped to 1.4.0
- [x] Git tag created (v1.4.0)
- [x] Tag pushed to GitHub
- [x] Ready for GitHub release

---

**🎉 Release v1.4.0 is ready for deployment!**

**Download**: [GitHub Releases](https://github.com/abuzant/TheTradeVisor/releases/tag/v1.4.0)  
**Commit**: `2a858c8`  
**Date**: November 17, 2025
