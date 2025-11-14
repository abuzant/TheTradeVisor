# TheTradeVisor Expert Advisor Setup Guide

## Overview

TheTradeVisor provides two Expert Advisors (EAs) for automated trading data collection:
- **TheTradeVisor_MT4.mq4** - For MetaTrader 4 platforms
- **TheTradeVisor_MT5.mq5** - For MetaTrader 5 platforms

Both EAs automatically collect and send your trading data to TheTradeVisor for advanced analytics and performance tracking.

---

## Installation

### Step 1: Download Your EA

1. Log in to your TheTradeVisor account at https://thetradevisor.com
2. Navigate to **Settings** → **API Keys**
3. Download the appropriate EA for your platform (MT4 or MT5)

### Step 2: Install the EA

**For MT4:**
1. Open MetaTrader 4
2. Click **File** → **Open Data Folder**
3. Navigate to **MQL4** → **Experts**
4. Copy `TheTradeVisor_MT4.mq4` to this folder
5. Restart MetaTrader 4
6. The EA will appear in the **Navigator** panel under **Expert Advisors**

**For MT5:**
1. Open MetaTrader 5
2. Click **File** → **Open Data Folder**
3. Navigate to **MQL5** → **Experts**
4. Copy `TheTradeVisor_MT5.mq5` to this folder
5. Restart MetaTrader 5
6. The EA will appear in the **Navigator** panel under **Expert Advisors**

### Step 3: Enable WebRequest

**CRITICAL:** You must allow the EA to communicate with TheTradeVisor servers.

1. Go to **Tools** → **Options** → **Expert Advisors**
2. Check **Allow WebRequest for listed URL**
3. Add this URL to the list:
   ```
   https://api.thetradevisor.com
   ```
4. Click **OK**

### Step 4: Get Your API Key

1. Log in to TheTradeVisor at https://thetradevisor.com
2. Go to **Settings** → **API Keys**
3. Click **Generate New API Key**
4. Copy your API key (starts with `tvsr_`)
5. **IMPORTANT:** Store this key securely - it will only be shown once!

### Step 5: Attach EA to Chart

1. Drag the EA from **Navigator** onto any chart
2. In the settings window:
   - **API_KEY**: Paste your API key from Step 4
   - **SendInterval** (MT4) / **UPDATE_INTERVAL** (MT5): How often to send data (in seconds)
   - **SendHistoricalData** (MT4 only): Enable to upload historical trades on first run
   - **HistoricalDays** (MT4 only): Number of days of history to upload
3. Click **OK**

---

## Configuration Parameters

### MT4 Parameters

| Parameter | Default | Description |
|-----------|---------|-------------|
| `API_KEY` | (empty) | Your TheTradeVisor API key (required) |
| `SendInterval` | 60 | Send data every N seconds (minimum: 30) |
| `SendHistoricalData` | false | Upload historical trades on first run |
| `HistoricalDays` | 30 | Days of history to upload (if enabled) |

### MT5 Parameters

| Parameter | Default | Description |
|-----------|---------|-------------|
| `API_KEY` | (empty) | Your TheTradeVisor API key (required) |
| `UPDATE_INTERVAL` | 120 | Send data every N seconds (minimum: 60) |
| `COLLECT_ACCOUNT_INFO` | true | Send account information |
| `COLLECT_POSITIONS` | true | Send open positions |
| `COLLECT_ORDERS` | true | Send pending orders |
| `COLLECT_HISTORY` | true | Send closed trades |
| `HISTORY_DAYS_REGULAR` | 2 | Days of recent history to include |
| `HISTORY_UPLOAD_INTERVAL` | 30 | Seconds between historical uploads |
| `ANONYMIZE_ACCOUNT` | false | Hash account number for privacy |
| `DEBUG_MODE` | false | Enable detailed logging |

---

## What Data is Collected?

### Account Information
- Account number (or hashed if anonymized)
- Broker name and server
- Account balance, equity, margin
- Leverage and currency
- Account type (real/demo/contest)

### Open Positions (MT5) / Market Orders (MT4)
- Ticket number
- Symbol, type (buy/sell)
- Volume, open price, current price
- Stop loss, take profit
- Current profit, swap, commission
- Open time and comment

### Pending Orders
- Ticket number
- Symbol, order type
- Volume, price levels
- Stop loss, take profit
- Setup time and expiration

### Historical Trades
- All closed positions/deals
- Entry and exit prices
- Profit/loss, swap, commission
- Open and close times
- Trade duration

---

## Important Notes

### ✅ Supported Accounts
- **Real trading accounts only**
- Demo and contest accounts are **NOT** supported

### 🔒 Security
- API endpoint is hardcoded and cannot be changed
- Your data is encrypted in transit (HTTPS)
- API keys are never logged or displayed
- Account numbers can be anonymized (MT5 only)

### 📊 Data Usage
- Data is sent to: `https://api.thetradevisor.com/api/v1/data/collect`
- Data is processed asynchronously (queued)
- Historical data is uploaded gradually to avoid overload
- All data is stored securely and used only for your analytics

### ⚡ Performance
- Minimal impact on trading performance
- Data is sent in background
- Failed uploads are retried automatically
- MT5 EA includes intelligent throttling for historical data

---

## Error Messages & Solutions

### ❌ "ERROR: API_KEY is empty!"
**Solution:** You must set your API key in the EA settings. Get your key from https://thetradevisor.com/settings/api-keys

### ❌ "INVALID API KEY (401)"
**Solution:** Your API key is incorrect or has been revoked. Generate a new key from your TheTradeVisor account.

### ❌ "DEMO ACCOUNT REJECTED"
**Solution:** TheTradeVisor only accepts real trading accounts. Demo and contest accounts are not supported.

### ❌ "ACCOUNT SUSPENDED"
**Solution:** Your TheTradeVisor account has been suspended. Contact support at hello@thetradevisor.com

### ⚠️ "ACCOUNT PAUSED"
**Solution:** This specific trading account has been paused in TheTradeVisor. You can unpause it from your account settings.

### ❌ "WebRequest error code: 4060"
**Solution:** You haven't allowed the EA to access the internet. Follow Step 3 in the installation guide above.

### ❌ "Invalid data format (400)"
**Solution:** The EA sent malformed data. This is usually a bug - please contact support with your MT4/MT5 logs.

### ❌ "Server error (500)"
**Solution:** TheTradeVisor servers are experiencing issues. The EA will retry automatically. If the problem persists, check https://status.thetradevisor.com

---

## Troubleshooting

### EA is not sending data

1. **Check Expert Advisors are enabled:**
   - Look for a smiley face icon in the top-right corner of MT4/MT5
   - If it's crossed out, click it to enable EAs

2. **Check the Experts tab:**
   - Open the **Terminal** window (Ctrl+T)
   - Click the **Experts** tab
   - Look for messages from TheTradeVisor EA

3. **Verify WebRequest is allowed:**
   - Tools → Options → Expert Advisors
   - Ensure `https://api.thetradevisor.com` is in the allowed list

4. **Check your API key:**
   - Make sure you copied the entire key (starts with `tvsr_`)
   - No spaces before or after the key
   - Key is still valid (not revoked)

### Historical upload is slow (MT5)

This is **normal behavior**. The MT5 EA uploads historical data gradually to avoid:
- Overloading the server
- Consuming excessive bandwidth
- Impacting trading performance

The EA uploads one day of history every 30 seconds by default. For a 1-year history, this takes about 6 hours.

You can monitor progress in the Experts tab:
```
Day 10 uploaded successfully. Progress: 2.7% (10/365 days)
```

### Data not appearing in TheTradeVisor

1. **Wait a few minutes:** Data is processed asynchronously
2. **Refresh your browser:** Clear cache and reload
3. **Check account status:** Ensure your account is active
4. **Verify EA is running:** Check the Experts tab for success messages

---

## Support

### Documentation
- Main site: https://thetradevisor.com
- Help center: https://thetradevisor.com/help
- API documentation: https://thetradevisor.com/docs/api

### Contact
- Email: hello@thetradevisor.com
- Support hours: Monday-Friday, 9 AM - 5 PM UTC

### Reporting Issues

When contacting support, please provide:
1. Your TheTradeVisor account email
2. Broker name and account number
3. MT4 or MT5 version
4. Error messages from the Experts tab
5. Screenshots if applicable

---

## Frequently Asked Questions

### Q: Can I use the same API key for multiple accounts?
**A:** Yes! One API key can be used across all your trading accounts (MT4 and MT5).

### Q: Will the EA interfere with my trading?
**A:** No. The EA only reads data - it never places trades or modifies positions.

### Q: What happens if my internet disconnects?
**A:** The EA will retry sending data when the connection is restored. No data is lost.

### Q: Can I pause data collection?
**A:** Yes. Either remove the EA from the chart, or pause the account in your TheTradeVisor settings.

### Q: Is my trading data secure?
**A:** Yes. All data is transmitted over HTTPS and stored securely. We never share your data with third parties.

### Q: How much bandwidth does the EA use?
**A:** Very minimal. Typical usage is less than 1 MB per day for active accounts.

### Q: Can I use this on a VPS?
**A:** Yes! The EA works perfectly on VPS environments. Just ensure WebRequest is allowed.

### Q: What if I change brokers?
**A:** Simply attach the EA to your new broker's MT4/MT5 with the same API key. TheTradeVisor will track both accounts separately.

---

## Version History

### MT5 v2.0
- Intelligent historical data upload with throttling
- Enhanced error handling and JSON response parsing
- Hardcoded API endpoint for security
- Improved logging and progress tracking
- Support for account anonymization

### MT4 v1.0
- Initial release
- Real-time data collection
- Optional historical data upload
- JSON response parsing
- Hardcoded API endpoint for security

---

## License & Terms

By using TheTradeVisor Expert Advisors, you agree to:
- TheTradeVisor Terms of Service
- TheTradeVisor Privacy Policy
- Only use real trading accounts
- Not reverse engineer or modify the EA code
- Not redistribute the EA without permission

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
