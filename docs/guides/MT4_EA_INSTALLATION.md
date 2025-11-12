# 📊 TheTradeVisor MT4 Expert Advisor - Installation Guide

> **Complete guide for installing and configuring the MT4 EA**

**Version**: 1.0.0  
**Date**: November 8, 2025  
**Status**: ✅ Production Ready

---

## 🎯 Overview

The TheTradeVisor MT4 Expert Advisor automatically sends your trading data to the TheTradeVisor platform for analysis and visualization. It works identically to the MT5 version but is compatible with MetaTrader 4.

### Key Features

- ✅ **Automatic Data Collection** - Sends data every 60 seconds (configurable)
- ✅ **Historical Data Upload** - Optional upload of past trading history
- ✅ **Real-time Monitoring** - Tracks open positions, pending orders, and closed trades
- ✅ **Demo Account Detection** - Automatically rejects demo accounts
- ✅ **Secure Communication** - Uses HTTPS and API key authentication
- ✅ **Identical JSON Format** - Same data structure as MT5 version

---

## 📋 Requirements

### Minimum Requirements
- **MetaTrader 4** (build 1380 or higher)
- **Active Internet Connection**
- **TheTradeVisor Account** with API key
- **Real Trading Account** (demo accounts not supported)

### Recommended
- **Stable VPS** for 24/7 operation
- **Low Latency Connection** for real-time data
- **MT4 Build 1400+** for best compatibility

---

## 🚀 Installation Steps

### Step 1: Download the EA

Download `TheTradeVisor_MT4.mq4` from:
- TheTradeVisor Dashboard → Settings → Download EA
- GitHub Repository (if available)
- Direct from support

### Step 2: Install in MT4

1. **Open MT4**
2. **Open Data Folder**:
   - Click `File` → `Open Data Folder`
   - Or press `Ctrl+Shift+D`

3. **Copy EA File**:
   - Navigate to `MQL4/Experts/` folder
   - Copy `TheTradeVisor_MT4.mq4` into this folder

4. **Compile the EA** (if needed):
   - Open MetaEditor (`F4` in MT4)
   - Open `TheTradeVisor_MT4.mq4`
   - Click `Compile` or press `F7`
   - Check for any errors (should compile successfully)

5. **Restart MT4**:
   - Close and reopen MT4
   - The EA should now appear in the Navigator panel

### Step 3: Configure Allowed URLs

**IMPORTANT**: MT4 requires you to whitelist URLs for WebRequest.

1. **Open Options**:
   - Click `Tools` → `Options`
   - Go to `Expert Advisors` tab

2. **Enable WebRequest**:
   - Check ✅ `Allow WebRequest for listed URL:`
   - Add the following URL:
     ```
     https://api.thetradevisor.com
     ```

3. **Click OK** to save

### Step 4: Get Your API Key

1. **Log into TheTradeVisor**:
   - Go to https://thetradevisor.com
   - Log in to your account

2. **Navigate to API Settings**:
   - Click `Settings` → `API`
   - Your API key will be displayed (format: `tvsr_...`)

3. **Copy the API Key**:
   - Click the copy button
   - Keep it secure (treat it like a password)

### Step 5: Attach EA to Chart

1. **Open a Chart**:
   - Any symbol/timeframe works
   - Recommended: Use a major pair like EURUSD on H1

2. **Drag and Drop EA**:
   - In Navigator panel, expand `Expert Advisors`
   - Drag `TheTradeVisor_MT4` onto the chart

3. **Configure Settings**:
   ```
   API_KEY: tvsr_your_api_key_here  ← Paste your API key
   API_URL: https://api.thetradevisor.com/api/v1/data/collect  ← Leave as is
   SendInterval: 60  ← Send data every 60 seconds
   SendHistoricalData: false  ← Set to true to upload history
   HistoricalDays: 30  ← Number of days to upload (if enabled)
   ```

4. **Enable Auto Trading**:
   - Check ✅ `Allow live trading`
   - Check ✅ `Allow DLL imports` (not needed but good practice)
   - Click `OK`

5. **Verify EA is Running**:
   - You should see a smiley face 😊 in the top-right corner
   - Check the `Experts` tab for initialization message

---

## ⚙️ Configuration Options

### API_KEY (Required)
- **Type**: String
- **Default**: `"tvsr_your_api_key_here"`
- **Description**: Your TheTradeVisor API key
- **Example**: `"tvsr_abc123def456ghi789"`

### API_URL (Advanced)
- **Type**: String
- **Default**: `"https://api.thetradevisor.com/api/v1/data/collect"`
- **Description**: API endpoint URL
- **Note**: Only change if instructed by support

### SendInterval
- **Type**: Integer (seconds)
- **Default**: `60`
- **Range**: 30-300 seconds
- **Description**: How often to send data
- **Recommended**: 60 seconds for real-time, 300 for reduced bandwidth

### SendHistoricalData
- **Type**: Boolean
- **Default**: `false`
- **Description**: Upload historical trading data on first run
- **Note**: Set to `true` only once, then disable

### HistoricalDays
- **Type**: Integer
- **Default**: `30`
- **Range**: 1-365 days
- **Description**: Number of days of history to upload
- **Note**: Only used if `SendHistoricalData` is `true`

---

## 📊 Data Sent to API

### JSON Structure

The EA sends the following data structure (identical to MT5):

```json
{
  "meta": {
    "is_historical": false,
    "is_first_run": true,
    "history_date": "2025-11-08",
    "history_day_number": 1
  },
  "account": {
    "account_number": "12345678",
    "account_hash": "abc123...",
    "broker": "Your Broker Name",
    "server": "BrokerServer-Live",
    "trade_mode": 0,
    "balance": 10000.00,
    "equity": 10500.00,
    "margin": 500.00,
    "free_margin": 10000.00,
    "leverage": 100,
    "currency": "USD"
  },
  "positions": [
    {
      "ticket": 123456,
      "symbol": "EURUSD",
      "type": 0,
      "volume": 0.10,
      "price_open": 1.08500,
      "price_current": 1.08550,
      "sl": 1.08400,
      "tp": 1.08700,
      "profit": 5.00,
      "swap": -0.50,
      "commission": -1.00,
      "time_open": "2025-11-08 10:00:00",
      "comment": ""
    }
  ],
  "orders": [],
  "deals": []
}
```

### Field Descriptions

#### Meta Fields
- `is_historical`: Boolean - true for historical data, false for current
- `is_first_run`: Boolean - true on first EA run
- `history_date`: String - Date of historical data (YYYY-MM-DD)
- `history_day_number`: Integer - Day number in upload sequence

#### Account Fields
- `account_number`: String - MT4 account number
- `account_hash`: String - Unique hash of account + server
- `broker`: String - Broker company name
- `server`: String - Trading server name
- `trade_mode`: Integer - 0=Real, 1=Demo, 2=Contest
- `balance`: Float - Account balance
- `equity`: Float - Account equity
- `margin`: Float - Used margin
- `free_margin`: Float - Free margin
- `leverage`: Integer - Account leverage
- `currency`: String - Account currency (USD, EUR, etc.)

#### Position Fields (Open Trades)
- `ticket`: Integer - Order ticket number
- `symbol`: String - Trading symbol
- `type`: Integer - 0=BUY, 1=SELL
- `volume`: Float - Lot size
- `price_open`: Float - Open price
- `price_current`: Float - Current price
- `sl`: Float - Stop loss
- `tp`: Float - Take profit
- `profit`: Float - Current profit/loss
- `swap`: Float - Swap charges
- `commission`: Float - Commission
- `time_open`: String - Open time (YYYY-MM-DD HH:MM:SS)
- `comment`: String - Order comment

#### Order Fields (Pending Orders)
- `ticket`: Integer - Order ticket number
- `symbol`: String - Trading symbol
- `type`: Integer - 2=BUY LIMIT, 3=SELL LIMIT, 4=BUY STOP, 5=SELL STOP
- `volume`: Float - Lot size
- `price_open`: Float - Pending order price
- `sl`: Float - Stop loss
- `tp`: Float - Take profit
- `time_setup`: String - Setup time
- `comment`: String - Order comment

#### Deal Fields (Closed Trades)
- Same as positions, plus:
- `price_close`: Float - Close price
- `time_close`: String - Close time

---

## 🔍 Troubleshooting

### EA Not Sending Data

**Check the Experts Tab**:
```
Look for messages like:
✅ "TheTradeVisor MT4 EA initialized"
✅ "Current data sent successfully"
❌ "WebRequest error: 4060"
❌ "Invalid API key"
```

**Common Issues**:

1. **WebRequest Error 4060**
   - **Cause**: URL not whitelisted
   - **Fix**: Add `https://api.thetradevisor.com` to allowed URLs

2. **Invalid API Key**
   - **Cause**: Wrong or expired API key
   - **Fix**: Get new API key from TheTradeVisor dashboard

3. **No Internet Connection**
   - **Cause**: MT4 can't reach the internet
   - **Fix**: Check firewall, VPN, or internet connection

4. **Demo Account Rejected**
   - **Cause**: Using demo account
   - **Fix**: Connect a real trading account

5. **EA Not Running**
   - **Cause**: Auto trading disabled
   - **Fix**: Click "Auto Trading" button in MT4 toolbar

### Verify EA is Working

1. **Check Experts Tab**:
   - Should see "Data sent successfully" every 60 seconds

2. **Check TheTradeVisor Dashboard**:
   - Your account should appear in the accounts list
   - Data should update every minute

3. **Check API Logs** (if available):
   - Contact support for API log access

---

## 🔐 Security Best Practices

### API Key Security
- ✅ Never share your API key
- ✅ Treat it like a password
- ✅ Regenerate if compromised
- ✅ Use different keys for different accounts

### VPS Security
- ✅ Use reputable VPS provider
- ✅ Enable firewall
- ✅ Keep MT4 updated
- ✅ Use strong passwords

### Data Privacy
- ✅ All data sent via HTTPS (encrypted)
- ✅ API key required for authentication
- ✅ Data stored securely on TheTradeVisor servers
- ✅ No third-party access

---

## 📈 Historical Data Upload

### How It Works

1. **Enable Historical Upload**:
   - Set `SendHistoricalData` to `true`
   - Set `HistoricalDays` to desired number (e.g., 30)

2. **EA Behavior**:
   - On first run, EA sends historical data day by day
   - Sends 1 day per send interval (60 seconds)
   - Example: 30 days = 30 minutes to upload

3. **Progress Tracking**:
   - Check Experts tab for messages like:
     ```
     Sending historical data for day 1/30 (2025-10-09)
     Historical data sent for 2025-10-09
     ```

4. **After Upload Complete**:
   - Disable `SendHistoricalData` (set to `false`)
   - EA will continue sending current data only

### Tips
- ✅ Upload during off-hours (low trading activity)
- ✅ Monitor progress in Experts tab
- ✅ Don't restart EA during upload
- ✅ Disable after upload completes

---

## 🆚 MT4 vs MT5 Differences

### MT4 Limitations
- ❌ No built-in SHA256 hashing (uses simplified hash)
- ❌ Different order types numbering
- ❌ No position concept (uses tickets)
- ❌ Limited WebRequest functionality

### How We Handle It
- ✅ Custom hash function (compatible with backend)
- ✅ Order type mapping (MT4 → MT5 equivalent)
- ✅ Ticket-based position tracking
- ✅ Same JSON structure as MT5

### Backend Compatibility
- ✅ **100% Compatible** - Backend accepts both MT4 and MT5 data
- ✅ **Same API Endpoint** - No changes needed
- ✅ **Identical JSON Format** - Data structure is the same
- ✅ **Automatic Detection** - Backend handles both versions

---

## 📞 Support

### Getting Help

**Documentation**:
- [API Documentation](../reference/API_DOCUMENTATION.md)
- [Artisan Commands](../reference/ARTISAN_COMMANDS.md)

**Contact**:
- 📧 Email: [hello@thetradevisor.com](mailto:hello@thetradevisor.com)
- 🌐 Website: [https://thetradevisor.com](https://thetradevisor.com)

**Common Questions**:
- "Can I use multiple EAs?" - Yes, one per account
- "Does it affect my trading?" - No, it only reads data
- "What about my privacy?" - All data encrypted and secure
- "Can I pause data collection?" - Yes, remove EA from chart

---

## 👨‍💻 Author & Contact

**Ruslan Abuzant**  
📧 Email: [ruslan@abuzant.com](mailto:ruslan@abuzant.com)  
🌐 Website: [https://abuzant.com](https://abuzant.com)  
💼 LinkedIn: [linkedin.com/in/ruslanabuzant](https://linkedin.com/in/ruslanabuzant)

For project support and inquiries:  
📧 [hello@thetradevisor.com](mailto:hello@thetradevisor.com)  
🌐 [https://thetradevisor.com](https://thetradevisor.com)

---

**Version**: 1.0.0  
**Last Updated**: November 8, 2025  
**Status**: Production Ready ✅
