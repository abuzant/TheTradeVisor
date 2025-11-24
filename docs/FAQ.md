# Frequently Asked Questions (FAQ)

**Last Updated:** November 24, 2025

---

## 📖 Table of Contents

1. [General Questions](#general-questions)
2. [Public Profiles & Leaderboard](#public-profiles--leaderboard)
3. [Account Management](#account-management)
4. [Trading Data & Sync](#trading-data--sync)
5. [Subscription & Billing](#subscription--billing)
6. [Technical Issues](#technical-issues)

---

## General Questions

### Q: What is TheTradeVisor?
**A:** TheTradeVisor is a comprehensive trading analytics platform that connects to your MT4/MT5 trading accounts and provides detailed performance analysis, risk metrics, and public profile sharing capabilities.

### Q: Which trading platforms are supported?
**A:** We support both MetaTrader 4 (MT4) and MetaTrader 5 (MT5) platforms from any broker.

### Q: Is my trading data secure?
**A:** Yes. We use industry-standard encryption for all data transmission and storage. Your account credentials are encrypted, and we never have access to your trading account funds. We only read trading data for analysis.

### Q: Do I need to install any software?
**A:** No. TheTradeVisor is a web-based platform. Simply connect your trading accounts through our secure API integration.

---

## Public Profiles & Leaderboard

### Q: How do I make my trading profile public?
**A:** Follow these steps:
1. Go to **Profile Settings**
2. Enable "Show on Leaderboard"
3. Choose your display mode (Username, Display Name, or Anonymous)
4. Go to **Accounts → Public Profiles**
5. Set individual accounts to "Public"
6. Configure account slug and widget preset
7. Save changes

Your profile will be accessible at: `https://thetradevisor.com/@username/account-slug/account-number`

### Q: Can I choose what information to show on my public profile?
**A:** Yes! For each account you can configure:
- **Widget Preset:** Minimal, Balanced, or Maximum Transparency
- **Show Symbol Performance:** Toggle top 10 symbols table
- **Show Recent Trades:** Toggle last 10 trades timeline
- **Display Mode:** Username, Display Name, or Anonymous

### Q: How do I appear on the leaderboard?
**A:** To appear on the Top Traders Leaderboard:
1. Enable "Show on Leaderboard" in profile settings
2. Have at least one public account
3. Have trading activity in the last 30 days
4. Have closed trades (not just open positions)

The leaderboard is accessible at: `/top-traders`

### Q: Can I be anonymous on the leaderboard?
**A:** Yes! Set your public display mode to "Anonymous" and you'll appear as "Anonymous Trader" on the leaderboard. Your username will still be part of the URL but won't be displayed publicly.

### Q: How are leaderboard rankings calculated?
**A:** Rankings aggregate stats across all your public accounts:
- **Total Profit:** Sum of all profits (displayed in USD for comparison)
- **ROI:** Total profit / total initial balance × 100
- **Win Rate:** Weighted average based on trade count per account
- **Profit Factor:** Total gross profit / total gross loss

### Q: Why do I see different symbol names when I hover?
**A:** We show normalized symbol names by default (e.g., "XAUUSD") for better readability. When you hover over a symbol, you'll see the raw broker-specific name (e.g., "XAUUSD.sd"). This helps identify the exact symbol while keeping the display clean.

### Q: How often is the leaderboard updated?
**A:** Profile data is cached for 15 minutes. Your stats will update automatically within this timeframe after new trades are synced.

### Q: Can I remove my profile from the leaderboard?
**A:** Yes, simply:
- Disable "Show on Leaderboard" in your profile settings, OR
- Set all accounts to "Private" in Accounts → Public Profiles

Your profile will be removed from the leaderboard immediately.

### Q: What time period does the leaderboard show?
**A:** The leaderboard displays performance from the **last 30 days** only. This is a rolling 30-day window that updates daily.

### Q: Can I share my public profile on social media?
**A:** Yes! Your profile URL is shareable on all social media platforms. We've included Open Graph and Twitter Card meta tags for rich previews on:
- Facebook
- Twitter
- LinkedIn
- WhatsApp
- Discord/Telegram

### Q: What are badges and how do I earn them?
**A:** Badges are achievements displayed on your profile and leaderboard entry. Current badges include:
- **🏆 Top Performer:** Rank in top 10 on any ranking criteria
- **📊 Transparent:** Using Maximum Transparency widget preset
- **⭐ Verified Trader:** Account verified by TheTradeVisor (coming soon)
- **🔥 Hot Streak:** 7+ consecutive winning days (coming soon)

### Q: Can I customize my profile page design?
**A:** Currently, profiles use our standard template with three widget presets (Minimal, Balanced, Maximum). Custom themes and layouts are planned for future releases.

### Q: Is there a cost to make my profile public?
**A:** No, public profiles are free for all subscription tiers.

### Q: Can I make my profile private again?
**A:** Yes, you can toggle between public and private at any time in Accounts → Public Profiles. Changes take effect within 15 minutes (cache refresh).

### Q: How do I remove a specific account from public view?
**A:** Go to **Accounts → Public Profiles** and set that account to "Private". The account will be removed from public view while keeping your other accounts public.

### Q: Can I have different settings for different accounts?
**A:** Yes! Each account has independent privacy and widget settings. You can have some accounts public with Maximum Transparency and others private or public with Minimal preset.

### Q: What happens if I delete an account?
**A:** The public profile is automatically removed. The URL will return a 404 error.

### Q: Can I change my username after setting it?
**A:** No, usernames are permanent once set. This prevents confusion with profile URLs. If you need to change it, contact support at hello@thetradevisor.com.

### Q: Can I change my account slug after setting it?
**A:** No, account slugs are permanent once set for the same reason as usernames. This ensures profile URLs remain stable.

### Q: Why isn't my profile showing on the leaderboard?
**A:** Check these requirements:
1. ✅ "Show on Leaderboard" is enabled in profile settings
2. ✅ At least one account is set to "Public"
3. ✅ You have closed trades in the last 30 days (not just open positions)
4. ✅ Wait 15 minutes for cache to refresh

### Q: My stats look wrong or outdated. What should I do?
**A:** Stats are cached for 15 minutes for performance. Wait for automatic refresh. If still wrong after 30 minutes, contact support.

### Q: My profile URL returns a 404 error. Why?
**A:** Check:
1. ✅ Account is set to "Public" (not "Private")
2. ✅ URL format is correct: `/@username/slug/account-number`
3. ✅ Account number matches your actual account
4. ✅ Username matches your public username

### Q: How do I report an inappropriate profile?
**A:** Contact support at hello@thetradevisor.com with the profile URL. We take violations of our terms of service seriously.

---

## Account Management

### Q: How many trading accounts can I connect?
**A:** It depends on your subscription tier:
- **Free:** 1 account
- **Basic:** 3 accounts
- **Pro:** 10 accounts
- **Enterprise:** Unlimited accounts

### Q: Can I connect accounts from different brokers?
**A:** Yes! You can connect accounts from any broker that supports MT4 or MT5.

### Q: How do I add a new trading account?
**A:** Go to **Accounts → Add Account** and follow the connection wizard. You'll need your account number and investor password (read-only access).

### Q: Can I pause an account temporarily?
**A:** Yes. Go to **Accounts**, find the account, and click "Pause". Paused accounts won't sync new data but historical data remains accessible.

### Q: How do I delete an account?
**A:** Go to **Accounts**, find the account, and click "Delete". This action is permanent and will remove all associated data.

---

## Trading Data & Sync

### Q: How often does my trading data sync?
**A:** Data syncs automatically every 15 minutes for active accounts. You can also manually trigger a sync from the account page.

### Q: Why is my data not syncing?
**A:** Common causes:
- Incorrect investor password
- Broker server is down
- Account is paused
- Network connectivity issues

Check your account status page for specific error messages.

### Q: Can I import historical data?
**A:** Yes, we automatically import your full trading history when you first connect an account. This may take a few minutes depending on the amount of data.

### Q: What happens to my data if I cancel my subscription?
**A:** Your data is retained for 90 days after cancellation. You can reactivate your subscription within this period to restore full access.

---

## Subscription & Billing

### Q: What subscription tiers are available?
**A:**
- **Free:** 1 account, basic analytics
- **Basic:** 3 accounts, advanced analytics
- **Pro:** 10 accounts, all features
- **Enterprise:** Unlimited accounts, priority support

### Q: Can I upgrade or downgrade my subscription?
**A:** Yes, you can change your subscription tier at any time from **Settings → Subscription**. Changes take effect immediately.

### Q: What payment methods do you accept?
**A:** We accept credit cards, debit cards, and PayPal through our secure payment processor.

### Q: Is there a free trial?
**A:** Yes! All new users start with a free account (1 trading account). You can upgrade at any time.

### Q: Can I get a refund?
**A:** We offer a 30-day money-back guarantee for annual subscriptions. Contact support for refund requests.

---

## Technical Issues

### Q: The site is loading slowly. What should I do?
**A:** Try these steps:
1. Clear your browser cache
2. Try a different browser
3. Check your internet connection
4. Check our status page (coming soon)

If issues persist, contact support.

### Q: I forgot my password. How do I reset it?
**A:** Click "Forgot Password" on the login page and follow the email instructions.

### Q: How do I enable two-factor authentication (2FA)?
**A:** Go to **Settings → Security** and follow the 2FA setup wizard. We support authenticator apps like Google Authenticator and Authy.

### Q: Can I access TheTradeVisor on mobile?
**A:** Yes! Our platform is fully responsive and works on all mobile devices through your web browser. Native mobile apps are planned for future releases.

### Q: I'm getting a "419 Page Expired" error. What does this mean?
**A:** This is a CSRF token expiration. Simply refresh the page and try again. This happens if you leave a form open for too long.

### Q: How do I contact support?
**A:** You can reach us at:
- **Email:** hello@thetradevisor.com
- **Response Time:** Within 24 hours (usually faster)
- **Priority Support:** Available for Pro and Enterprise tiers

---

## Related Documentation

- [User Guide](guides/PUBLIC_PROFILES_USER_GUIDE.md) - Detailed guide for public profiles
- [API Documentation](api/PUBLIC_PROFILES_API.md) - API endpoints and data structures
- [Technical Architecture](technical/PUBLIC_PROFILES_ARCHITECTURE.md) - System design
- [Implementation Details](features/PUBLIC_PROFILES_IMPLEMENTATION.md) - Development guide

---

## Still Have Questions?

If you can't find the answer you're looking for:
- **Email:** hello@thetradevisor.com
- **Documentation:** Browse our full documentation at `/docs`
- **Community:** Join our Discord server (coming soon)

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
