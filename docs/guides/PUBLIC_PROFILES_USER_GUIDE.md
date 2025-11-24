# Public Profiles & Leaderboard User Guide

**Last Updated:** November 24, 2025  
**Feature Version:** 2.7.0

---

## 📖 Table of Contents

1. [Overview](#overview)
2. [For Traders: Making Your Profile Public](#for-traders-making-your-profile-public)
3. [Appearing on the Leaderboard](#appearing-on-the-leaderboard)
4. [Privacy & Display Options](#privacy--display-options)
5. [For Visitors: Viewing Profiles](#for-visitors-viewing-profiles)
6. [Sharing Your Profile](#sharing-your-profile)
7. [Widget Presets Explained](#widget-presets-explained)
8. [Understanding the Stats](#understanding-the-stats)
9. [Badges & Verification](#badges--verification)
10. [Troubleshooting](#troubleshooting)

---

## Overview

TheTradeVisor's Public Profiles feature allows traders to showcase their trading performance publicly and compete on a global leaderboard. You have complete control over what information you share and how you appear to others.

### Key Features
- **Public Trading Profiles:** Share your performance with custom URLs
- **Top Traders Leaderboard:** Compete globally based on multiple metrics
- **Privacy Controls:** Choose exactly what to show and how to appear
- **Real-time Stats:** Performance data from the last 30 days
- **Social Sharing:** Share your achievements on social media

---

## For Traders: Making Your Profile Public

### Step 1: Enable Public Profile

1. **Navigate to Profile Settings**
   - Click your profile icon in the top right
   - Select "Profile Settings"

2. **Enable Leaderboard Visibility**
   - Find the "Show on Leaderboard" setting
   - Set it to **Yes**

3. **Choose Your Display Mode**
   - **Username Mode:** Shows your @username
   - **Display Name Mode:** Shows your custom display name
   - **Anonymous Mode:** Shows "Anonymous Trader"

4. **Set Your Public Username** (if not already set)
   - Choose a unique username (alphanumeric + underscore)
   - This will be part of your profile URL
   - Example: `@john_trader`

### Step 2: Configure Account Visibility

1. **Navigate to Public Profiles Management**
   - Go to **Accounts** → **Public Profiles**
   - You'll see all your connected trading accounts

2. **For Each Account You Want to Make Public:**

   **a) Set Visibility**
   - Select **"Public"** from the dropdown (default is "Private")

   **b) Configure Account Slug**
   - Enter a URL-friendly name for this account
   - Use lowercase letters, numbers, and hyphens only
   - Example: `main-account`, `scalping-ea`, `gold-strategy`

   **c) Choose Widget Preset**
   - **Minimal:** Basic stats only (profit, trades, win rate)
   - **Balanced:** Stats + equity curve + top symbols
   - **Maximum Transparency:** Everything including recent trades

   **d) Additional Options**
   - Toggle "Show Symbol Performance" (top 10 symbols traded)
   - Toggle "Show Recent Trades Timeline" (last 10 trades)

3. **Save Changes**
   - Click "Update Profile" for each account
   - Changes take effect within 15 minutes (cache refresh)

### Step 3: Get Your Profile URL

Your public profile URL follows this format:
```
https://thetradevisor.com/@username/account-slug/account-number
```

**Example:**
```
https://thetradevisor.com/@john_trader/main-account/12345678
```

You can find your complete URL on the Public Profiles management page.

---

## Appearing on the Leaderboard

### Requirements

To appear on the Top Traders Leaderboard, you must:

1. ✅ Enable "Show on Leaderboard" in profile settings
2. ✅ Have at least one public account
3. ✅ Have trading activity in the last 30 days
4. ✅ Have closed trades (not just open positions)

### How Rankings Work

The leaderboard aggregates statistics across **all your public accounts**:

#### Ranking Criteria

1. **Total Profit (Default)**
   - Sum of all profits across your public accounts
   - Displayed in USD for fair comparison
   - Includes both realized and unrealized P&L

2. **ROI (Return on Investment)**
   - Formula: `(Total Profit / Total Initial Balance) × 100`
   - Aggregated across all public accounts
   - Shows percentage return on capital

3. **Win Rate**
   - Weighted average based on trade count per account
   - Formula: `(Total Winning Trades / Total Trades) × 100`
   - Higher is better (but consider profit factor too)

4. **Profit Factor**
   - Formula: `Gross Profit / Gross Loss`
   - Aggregated from all public accounts
   - Values > 1.0 indicate profitability

### Leaderboard Display

- **Top 50 Traders:** Only the top 50 appear on the leaderboard
- **Expandable Rows:** Click any trader to see their individual accounts
- **Filter Tabs:** Switch between ranking criteria
- **Real-time Updates:** Data refreshes every 15 minutes

---

## Privacy & Display Options

### Display Modes

#### 1. Username Mode
- Shows your public username (e.g., `@john_trader`)
- Profile URL uses your username
- Best for building a personal brand

#### 2. Display Name Mode
- Shows your custom display name (e.g., "John's Trading")
- More flexible than username
- Can include spaces and special characters

#### 3. Anonymous Mode
- Shows "Anonymous Trader" everywhere
- Your username is hidden
- Profile URL still uses your username (but not displayed)
- Best for privacy while still competing

### What's Always Private

The following information is **never** shown publicly:
- Your email address
- Account credentials and API keys
- Broker login details
- Personal information
- Private accounts
- Accounts you haven't explicitly made public

### Account-Level Privacy

Each account has independent privacy settings:
- **Private:** Not visible to anyone (default)
- **Public:** Visible to everyone with the URL
- You can have some accounts public and others private

---

## For Visitors: Viewing Profiles

### Accessing Public Profiles

#### Via Leaderboard
1. Visit `/top-traders` (no login required)
2. Browse the top 50 traders
3. Filter by: Total Profit, ROI, Win Rate, or Profit Factor
4. Click on any trader to expand and see their accounts
5. Click "View Profile" to see detailed stats

#### Via Direct URL
If you have a trader's profile URL, you can visit it directly:
```
https://thetradevisor.com/@username/account-slug/account-number
```

### What You Can See

Depending on the trader's widget preset, you may see:

**Always Visible:**
- Performance metrics (30-day window)
- Total profit, trades, win rate
- ROI and profit factor
- Account currency

**Conditionally Visible:**
- Equity curve chart (daily snapshots)
- Top 10 symbols traded with performance
- Recent trades timeline (last 10 trades)
- Account details (broker, platform type)

**Never Visible:**
- Real-time open positions
- Account credentials
- Personal information
- Private accounts

---

## Sharing Your Profile

### Social Media Sharing

Your public profile includes Open Graph and Twitter Card meta tags for rich previews:

- **Facebook:** Automatic preview with stats
- **Twitter:** Card with performance metrics
- **LinkedIn:** Professional trading profile
- **WhatsApp:** Preview with key stats

### Direct Link Sharing

Copy your profile URL and share it anywhere:
- Email signatures
- Trading forums
- Personal websites
- Discord/Telegram groups

### Embedding (Coming Soon)

Future feature: Embeddable widgets for your website or blog.

---

## Widget Presets Explained

### Minimal
**Best for:** Privacy-conscious traders who want basic visibility

**Shows:**
- Total profit
- Number of trades
- Win rate
- ROI
- Profit factor

**Hides:**
- Equity curve
- Symbol performance
- Recent trades
- Account details

---

### Balanced (Recommended)
**Best for:** Most traders who want to showcase performance

**Shows:**
- All Minimal stats
- Equity curve chart (30-day)
- Top 10 symbols with performance
- Account currency and platform

**Hides:**
- Recent trades timeline
- Detailed trade information

---

### Maximum Transparency
**Best for:** Verified traders, signal providers, educators

**Shows:**
- All Balanced stats
- Recent trades timeline (last 10)
- Trade entry/exit times
- Position types and volumes
- Full account details

**Note:** Even with maximum transparency, sensitive data (credentials, API keys) is never exposed.

---

## Understanding the Stats

### Performance Metrics

#### Total Profit
- Sum of all closed trades
- Displayed in account's native currency on profile
- Converted to USD on leaderboard for comparison
- Includes commissions and swaps

#### Total Trades
- Count of all closed trades
- Does not include open positions
- Both winning and losing trades

#### Win Rate
- Percentage of winning trades
- Formula: `(Winning Trades / Total Trades) × 100`
- Example: 60% means 60 out of 100 trades were profitable

#### ROI (Return on Investment)
- Percentage return on initial capital
- Formula: `(Total Profit / Initial Balance) × 100`
- Example: 25% ROI on $10,000 = $2,500 profit

#### Profit Factor
- Ratio of gross profit to gross loss
- Formula: `Gross Profit / Gross Loss`
- Values > 1.0 indicate profitability
- Example: 2.0 means you make $2 for every $1 lost

### Time Period

All statistics show **last 30 days** performance:
- Rolling 30-day window
- Updates daily
- Older trades automatically excluded

### Currency Display

- **Single Account View:** Shows account's native currency (EUR, AED, etc.)
- **Leaderboard/Multi-Account:** Converted to USD for fair comparison

---

## Badges & Verification

### Current Badges

Badges appear next to trader names on the leaderboard:

- **🏆 Top Performer:** Top 10 on any ranking criteria
- **⭐ Verified Trader:** Account verified by TheTradeVisor (coming soon)
- **📊 Transparent:** Using Maximum Transparency preset
- **🔥 Hot Streak:** 7+ consecutive winning days (coming soon)

### How to Earn Badges

1. **Top Performer:** Rank in top 10 on any filter
2. **Transparent:** Set widget preset to "Maximum Transparency"
3. **Verified Trader:** Contact support for verification (coming soon)
4. **Hot Streak:** Maintain winning streak (coming soon)

### Verification Process (Coming Soon)

To get verified:
1. Contact support with your profile URL
2. Provide additional account verification
3. Agree to transparency requirements
4. Receive verified badge within 48 hours

---

## Troubleshooting

### My profile isn't showing on the leaderboard

**Check:**
1. ✅ "Show on Leaderboard" is enabled in profile settings
2. ✅ At least one account is set to "Public"
3. ✅ You have closed trades in the last 30 days
4. ✅ Wait 15 minutes for cache to refresh

### My stats look wrong or outdated

**Solution:**
- Stats are cached for 15 minutes
- Wait for automatic refresh
- If still wrong after 30 minutes, contact support

### Profile URL returns 404 error

**Check:**
1. ✅ Account is set to "Public" (not "Private")
2. ✅ URL format is correct: `/@username/slug/account-number`
3. ✅ Account number matches your actual account
4. ✅ Username matches your public username

### Symbols showing weird names

**Normal behavior:**
- We show normalized symbols by default (e.g., "XAUUSD")
- Hover over symbol to see broker's raw name (e.g., "XAUUSD.sd")
- This is for better readability

### Can't change my username

**Note:**
- Username can only be set once initially
- Contact support if you need to change it
- This prevents profile URL confusion

### Equity curve looks flat or wrong

**Possible causes:**
1. Not enough trading activity (need daily snapshots)
2. Account snapshots not being collected
3. Cache issue (wait 15 minutes)

**Solution:**
- Ensure your account is actively syncing
- Check that snapshots are being recorded
- Contact support if issue persists

### Recent trades not showing

**Check:**
1. ✅ "Show Recent Trades Timeline" is enabled
2. ✅ Widget preset is "Balanced" or "Maximum Transparency"
3. ✅ You have closed trades in last 30 days

---

## FAQ Quick Reference

### Q: Is there a cost to make my profile public?
**A:** No, public profiles are free for all subscription tiers.

### Q: Can I make my profile private again?
**A:** Yes, simply set "Show on Leaderboard" to "No" or set accounts to "Private".

### Q: How do I remove a specific account from public view?
**A:** Go to Accounts → Public Profiles and set that account to "Private".

### Q: Can I have different settings for different accounts?
**A:** Yes! Each account has independent privacy and widget settings.

### Q: What happens if I delete an account?
**A:** The public profile is automatically removed. The URL will return 404.

### Q: Can I customize my profile page design?
**A:** Not currently, but custom themes are planned for future releases.

### Q: How do I report an inappropriate profile?
**A:** Contact support at hello@thetradevisor.com with the profile URL.

---

## Next Steps

1. **Enable Your Profile:** Follow [Step 1](#step-1-enable-public-profile)
2. **Configure Accounts:** Follow [Step 2](#step-2-configure-account-visibility)
3. **Share Your URL:** Follow [Sharing Guide](#sharing-your-profile)
4. **Compete on Leaderboard:** Check your ranking at `/top-traders`

---

## Related Documentation

- [Public Profiles Implementation](../features/PUBLIC_PROFILES_IMPLEMENTATION.md) - Technical details
- [API Documentation](../api/PUBLIC_PROFILES_API.md) - API endpoints
- [Technical Architecture](../technical/PUBLIC_PROFILES_ARCHITECTURE.md) - System design
- [FAQ](../FAQ.md) - Frequently asked questions

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
