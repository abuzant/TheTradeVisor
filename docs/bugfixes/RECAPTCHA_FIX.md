# reCAPTCHA Fix - "Invalid key type" Error

## Issue
The reCAPTCHA was showing "ERROR for site owner: Invalid key type" on login and registration pages.

## Root Cause
The middleware was mixing reCAPTCHA v2 (checkbox) frontend implementation with v3 (score-based) backend validation. The `getScore()` method only exists in v3, causing the error when used with v2 keys.

## Fix Applied
Removed the v3 score check from the middleware (`VerifyRecaptcha.php`) to make it compatible with v2 implementation.

**File Changed:** `/www/app/Http/Middleware/VerifyRecaptcha.php`

**What was removed:**
```php
// Check score for v3 (0.0 to 1.0, higher is better)
if ($response->getScore() < 0.5) {
    return redirect()->back()
        ->withErrors(['recaptcha' => 'Suspicious activity detected. Please try again.'])
        ->withInput();
}
```

## Current Implementation

### Frontend (v2 Checkbox)
- Uses `https://www.google.com/recaptcha/api.js`
- Displays checkbox: `<div class="g-recaptcha" data-sitekey="...">`
- Used on: Login, Register, Forgot Password, Reset Password, Contact Form

### Backend (v2 Validation)
- Validates token with Google's API
- Checks `isSuccess()` only (no score)
- Returns error if validation fails

## reCAPTCHA Keys

Current keys in `.env`:
```
RECAPTCHA_ENABLED=true
RECAPTCHA_SITE_KEY=your_recaptcha_v2_site_key
RECAPTCHA_SECRET_KEY=your_recaptcha_v2_secret_key
```

### If Keys Are Invalid

If you still see "Invalid key type" error, the keys need to be regenerated:

1. Go to: https://www.google.com/recaptcha/admin
2. Register a new site:
   - **Label:** TheTradeVisor
   - **reCAPTCHA type:** reCAPTCHA v2 → "I'm not a robot" Checkbox
   - **Domains:** 
     - `thetradevisor.com`
     - `www.thetradevisor.com`
     - `localhost` (for testing)
3. Copy the Site Key and Secret Key
4. Update `.env`:
   ```bash
   RECAPTCHA_SITE_KEY=your_new_site_key
   RECAPTCHA_SECRET_KEY=your_new_secret_key
   ```
5. Clear config cache:
   ```bash
   php artisan config:clear
   ```

## Testing

### Test reCAPTCHA is Working

1. Visit: `https://thetradevisor.com/login`
2. You should see the reCAPTCHA checkbox
3. Try to login without checking the box → Should show error
4. Check the box and login → Should work

### Disable reCAPTCHA (if needed)

In `.env`:
```
RECAPTCHA_ENABLED=false
```

Then clear cache:
```bash
php artisan config:clear
```

## Alternative: Upgrade to v3

If you want to use reCAPTCHA v3 (invisible, score-based):

### 1. Get v3 Keys
- Go to: https://www.google.com/recaptcha/admin
- Register site with **reCAPTCHA v3**
- Add domains: `thetradevisor.com`, `www.thetradevisor.com`

### 2. Update Frontend
Replace in all views (login, register, etc.):

**Remove:**
```html
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<div class="g-recaptcha" data-sitekey="..."></div>
```

**Add:**
```html
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
<script>
grecaptcha.ready(function() {
    grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'login'})
    .then(function(token) {
        document.getElementById('recaptcha-token').value = token;
    });
});
</script>
<input type="hidden" id="recaptcha-token" name="g-recaptcha-response">
```

### 3. Update Middleware
Restore the score check in `VerifyRecaptcha.php`:
```php
if ($response->getScore() < 0.5) {
    return redirect()->back()
        ->withErrors(['recaptcha' => 'Suspicious activity detected. Please try again.'])
        ->withInput();
}
```

## Current Status

✅ **Fixed** - Middleware now works with v2 implementation  
⚠️ **Action Required** - If keys are invalid, regenerate them at Google reCAPTCHA admin

## Files Involved

- **Middleware:** `/www/app/Http/Controllers/VerifyRecaptcha.php`
- **Config:** `/www/config/services.php`
- **Environment:** `/www/.env`
- **Views:**
  - `/www/resources/views/auth/login.blade.php`
  - `/www/resources/views/auth/register.blade.php`
  - `/www/resources/views/auth/forgot-password.blade.php`
  - `/www/resources/views/auth/reset-password.blade.php`
  - `/www/resources/views/public/contact.blade.php`
  - `/www/resources/views/layouts/guest.blade.php`
  - `/www/resources/views/layouts/app.blade.php`

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
