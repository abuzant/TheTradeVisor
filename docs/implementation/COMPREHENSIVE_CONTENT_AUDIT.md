# Comprehensive Content Audit - All Pages Needing Updates
**Date:** November 21, 2025  
**Status:** 🔴 CRITICAL UPDATES NEEDED

---

## 🚨 CRITICAL FINDINGS

### 1. **Enterprise Menu in Main Navigation - REMOVE** ❌
**Location:** `/www/resources/views/layouts/navigation.blade.php` (Lines 58-82, 298-312)

**Issue:** Shows "Enterprise" dropdown menu for enterprise admins in the main app
**Why It's Wrong:** Enterprise admins should ONLY use `https://enterprise.thetradevisor.com/`
**Action:** REMOVE both desktop and mobile enterprise menus completely

**Current Code:**
```php
@if(Auth::user()->enterpriseBroker)
    <!-- Enterprise Dropdown -->
    ...enterprise menu links...
@endif
```

**Solution:** Delete this entire section. Enterprise admins access their portal via subdomain only.

---

### 2. **API Documentation - Outdated Rate Limits** ❌
**Location:** `/www/resources/views/public/api-docs.blade.php` (Lines 57-86)

**Issue:** Shows old subscription tiers (Free/Pro/Enterprise) with rate limits
**Current Content:**
- Free: 100 requests/hour
- Pro: 1,000 requests/hour  
- Enterprise: Unlimited

**New Reality:**
- All users: Same rate limits (no tiers)
- Enterprise API: Separate endpoint with different auth

**Action:** 
- Remove tier-based rate limit table
- Update to show single rate limit for all users
- Add separate section for Enterprise API (different endpoint)

---

### 3. **FAQ Page - Outdated Pricing Info** ❌
**Location:** `/www/resources/views/public/faq.blade.php`

**Issues Found:**
- Line 73: "$9.99 one-time payment for lifetime access"
- Line 78: "cancel your subscription at any time"
- References to "billing period"
- References to "additional accounts cost"

**Action:** Complete rewrite of Pricing & Billing section

---

### 4. **Pricing Page - Completely Outdated** ❌
**Location:** `/www/resources/views/public/pricing.blade.php`

**Issues:**
- Shows 3 plans: Free (1 account), Pay Per Account ($9.99), Enterprise
- Mentions "pay per account" model
- Says "One-time payment, lifetime access"
- Shows account limits

**Action:** Complete rewrite (already attempted, hit token limit)

---

### 5. **For Brokers Page - Needs Update** ⚠️
**Location:** `/www/resources/views/public/for-brokers.blade.php`

**Action:** Check if it explains the new broker-paid model correctly

---

### 6. **Documentation Page** ⚠️
**Location:** `/www/resources/views/public/docs.blade.php`

**Action:** Check for subscription/tier references

---

### 7. **Landing Page** ⚠️
**Location:** `/www/resources/views/public/landing.blade.php`

**Action:** Check hero messaging and CTAs

---

## 📋 DETAILED UPDATE PLAN

### Priority 1: Remove Misleading UI (DO FIRST)

#### A. Remove Enterprise Menu from Main Navigation
**File:** `/www/resources/views/layouts/navigation.blade.php`

**Lines to DELETE:**
- Lines 58-82 (Desktop enterprise dropdown)
- Lines 298-312 (Mobile enterprise menu)

**Reason:** Enterprise admins use subdomain portal, not main app navigation

---

#### B. Update API Documentation Rate Limits
**File:** `/www/resources/views/public/api-docs.blade.php`

**Current (Lines 57-86):**
```html
<h2>Rate Limits</h2>
<p>API rate limits are based on your subscription tier...</p>
<table>
  <tr><td>Free</td><td>100</td></tr>
  <tr><td>Pro</td><td>1,000</td></tr>
  <tr><td>Enterprise</td><td>Unlimited</td></tr>
</table>
```

**New:**
```html
<h2>Rate Limits</h2>
<p>All users have the same API rate limits:</p>
<table>
  <tr><td>Standard API</td><td>1,000 requests/hour</td></tr>
</table>

<h3>Enterprise API</h3>
<p>Brokers with enterprise subscriptions have access to a separate Enterprise API at:</p>
<code>https://api.thetradevisor.com/enterprise/v1</code>
<p>Enterprise API uses different authentication (ent_ prefixed keys) and has higher rate limits.</p>
```

---

### Priority 2: Update Public Content

#### C. Rewrite Pricing Page
**File:** `/www/resources/views/public/pricing.blade.php`

**New Structure:**
1. Hero: "Free Trading Analytics. Unlimited Accounts."
2. Two cards:
   - **For Traders**: $0/forever, unlimited accounts
   - **For Brokers**: $999/month, enterprise features
3. FAQ: Updated to remove payment/billing questions

---

#### D. Update FAQ Page
**File:** `/www/resources/views/public/faq.blade.php`

**Section to Rewrite:** "Pricing & Billing"

**New Questions:**
- Q: Is TheTradeVisor really free?
  - A: Yes! 100% free for all traders with unlimited accounts.
  
- Q: Do I need a credit card?
  - A: No credit card required. Just sign up and start connecting accounts.
  
- Q: What's the difference between standard and enterprise brokers?
  - A: Standard brokers: 7 days data view. Enterprise brokers: 180 days data view.
  
- Q: How do I get 180-day data access?
  - A: If your broker is an enterprise partner, you automatically get 180-day access.
  
- Q: I'm a broker, how do I become an enterprise partner?
  - A: Contact our sales team at [email]. Enterprise plans start at $999/month.

**Remove:**
- All questions about payments
- All questions about subscriptions
- All questions about account limits
- All questions about billing

---

#### E. Check For Brokers Page
**File:** `/www/resources/views/public/for-brokers.blade.php`

**Verify it includes:**
- New pricing model explanation
- Enterprise portal features
- REST API information
- $999/month pricing
- Contact information

---

#### F. Check Landing Page
**File:** `/www/resources/views/public/landing.blade.php`

**Update:**
- Hero: "Free Trading Analytics for Everyone"
- Remove any pricing mentions
- Update CTAs to emphasize "Free"

---

#### G. Check Documentation Page
**File:** `/www/resources/views/public/docs.blade.php`

**Remove:**
- Subscription tier references
- Account limit information
- Payment/billing docs

---

### Priority 3: Internal Pages

#### H. Check Dashboard
**File:** `/www/resources/views/dashboard.blade.php`

**Verify:**
- No subscription tier badges
- No "upgrade" prompts for account limits
- Account limit shows "Unlimited"

---

#### I. Check Plans Page (if exists)
**File:** `/www/resources/views/plans.blade.php`

**Action:** Determine if still used, update or remove

---

## 🎯 EXECUTION ORDER

### Phase 1: Remove Misleading UI (IMMEDIATE)
1. ✅ Remove enterprise menu from navigation
2. ✅ Update API docs rate limits

### Phase 2: Critical Public Pages (TODAY)
3. ✅ Rewrite pricing page
4. ✅ Update FAQ page
5. ✅ Check/update for-brokers page

### Phase 3: Supporting Pages (TODAY)
6. ✅ Check landing page
7. ✅ Check docs page
8. ✅ Check dashboard

### Phase 4: Verification (TODAY)
9. ✅ Test all pages
10. ✅ Verify no subscription references remain

---

## 📝 FILES TO UPDATE

### Must Update (Critical):
1. `/www/resources/views/layouts/navigation.blade.php` - Remove enterprise menu
2. `/www/resources/views/public/api-docs.blade.php` - Fix rate limits
3. `/www/resources/views/public/pricing.blade.php` - Complete rewrite
4. `/www/resources/views/public/faq.blade.php` - Update pricing section

### Should Check (Important):
5. `/www/resources/views/public/for-brokers.blade.php`
6. `/www/resources/views/public/landing.blade.php`
7. `/www/resources/views/public/docs.blade.php`
8. `/www/resources/views/dashboard.blade.php`

### May Need Update:
9. `/www/resources/views/plans.blade.php` (if exists)
10. Email templates (if any)

---

## ✅ WHAT'S ALREADY CORRECT

- ✅ Admin broker management views
- ✅ Enterprise portal views (subdomain)
- ✅ Backend logic
- ✅ Database structure
- ✅ API endpoints
- ✅ Middleware
- ✅ Controllers

---

## 🚀 READY TO EXECUTE

All issues identified. Ready to fix them systematically.

**Next Action:** Start with Phase 1 - Remove misleading UI
