# How to Upload Platform Screenshots

## Quick Instructions

You have 15 screenshots ready to be added to the website. Here's how to upload them:

### Step 1: Prepare the Screenshots

Rename your screenshot files to match these exact names:

1. Image 1 (Account Performance) → `account-performance.png`
2. Image 2 (Open Positions) → `open-positions.png`
3. Image 3 (Closed Positions) → `closed-positions.png`
4. Image 4 (Equity Curve) → `equity-curve.png`
5. Image 5 (Trading by Symbol) → `trading-by-symbol.png`
6. Image 6 (Trading by Hour) → `trading-by-hour.png`
7. Image 7 (Trading Session) → `trading-session-analysis.png`
8. Image 8 (Risk Analytics) → `risk-analytics.png`
9. Image 9 (Performance Leaderboards) → `performance-leaderboards.png`
10. Image 10 (Real-Time Activity) → `realtime-activity.png`
11. Image 11 (Profit/Loss Distribution) → `profit-loss-distribution.png`
12. Image 12 (Recent Activity) → `recent-activity.png`
13. Image 13 (Trading Patterns) → `trading-patterns.png`
14. Image 14 (Best Trading Days) → `best-trading-days.png`
15. Image 15 (Market Volatility) → `market-volatility.png`

### Step 2: Upload via Command Line

```bash
# Navigate to the screenshots directory
cd /www/public/images/screenshots

# Upload all your renamed screenshots here
# You can use scp, rsync, or your preferred method
```

### Step 3: Or Upload via FTP/SFTP

1. Connect to your server
2. Navigate to: `/www/public/images/screenshots/`
3. Upload all 15 renamed PNG files

### Step 4: Verify

Visit your website at: `https://thetradevisor.com/screenshots`

All screenshots should now be visible!

## Alternative: Quick Upload Script

If you have all files in a local directory, use this script:

```bash
#!/bin/bash
# Save this as upload-screenshots.sh

# Your screenshot files directory
LOCAL_DIR="/path/to/your/screenshots"

# Server details
SERVER="your-server"
REMOTE_DIR="/www/public/images/screenshots"

# Upload all PNG files
scp ${LOCAL_DIR}/*.png ${SERVER}:${REMOTE_DIR}/

echo "✅ Screenshots uploaded successfully!"
```

## File Mapping Reference

| Your Screenshot | Rename To | Description |
|----------------|-----------|-------------|
| Image 1 | account-performance.png | Account Performance Chart |
| Image 2 | open-positions.png | Live Open Positions Table |
| Image 3 | closed-positions.png | Recent Closed Positions |
| Image 4 | equity-curve.png | Equity Curve (30 Days) |
| Image 5 | trading-by-symbol.png | Symbol Distribution Chart |
| Image 6 | trading-by-hour.png | Trading Activity by Hour |
| Image 7 | trading-session-analysis.png | Session Performance Radar |
| Image 8 | risk-analytics.png | Risk Analytics Dashboard |
| Image 9 | performance-leaderboards.png | Performance Leaderboards |
| Image 10 | realtime-activity.png | Real-Time Activity Monitor |
| Image 11 | profit-loss-distribution.png | P/L Distribution Chart |
| Image 12 | recent-activity.png | Recent Activity Feed |
| Image 13 | trading-patterns.png | Trading Patterns Analysis |
| Image 14 | best-trading-days.png | Best Trading Days |
| Image 15 | market-volatility.png | Market Volatility Analysis |

## Need Help?

If screenshots don't appear after uploading:

1. Check file permissions: `chmod 644 /www/public/images/screenshots/*.png`
2. Clear Laravel cache: `php artisan cache:clear`
3. Clear browser cache: Ctrl+Shift+R (or Cmd+Shift+R on Mac)

## What's Already Done

✅ Screenshots page created at `/screenshots`
✅ Navigation link added
✅ Route and controller configured
✅ Styled containers ready
✅ SEO optimized
✅ Mobile responsive
✅ Pushed to GitHub

**You just need to upload the 15 image files!**


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

