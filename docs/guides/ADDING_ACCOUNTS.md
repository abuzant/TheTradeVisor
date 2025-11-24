# Adding Trading Accounts Guide

## Overview
This guide explains how to connect your MT4/MT5 trading accounts to TheTradeVisor.

## Prerequisites
- Active trading account with supported broker
- MT4 or MT5 terminal
- Expert Advisor (EA) installed
- Account credentials (investor password)

## Supported Brokers
TheTradeVisor supports most major brokers including:
- IC Markets
- Pepperstone
- FXCM
- OANDA
- And many more...

## Step-by-Step Setup

### 1. Download Expert Advisor
1. Login to your TheTradeVisor account
2. Navigate to Settings → Trading Accounts
3. Download the appropriate EA for your platform:
   - MT4: `TheTradeVisor_MT4.ex4`
   - MT5: `TheTradeVisor_MT5.ex5`

### 2. Install Expert Advisor

#### For MT4:
1. Open MT4 terminal
2. Go to File → Open Data Folder
3. Navigate to MQL4 → Experts
4. Copy `TheTradeVisor_MT4.ex4` to this folder
5. Restart MT4

#### For MT5:
1. Open MT5 terminal
2. Go to File → Open Data Folder
3. Navigate to MQL5 → Experts
4. Copy `TheTradeVisor_MT5.ex5` to this folder
5. Restart MT5

### 3. Configure Expert Advisor
1. In MT4/MT5, press Ctrl+N to open Navigator
2. Find "TheTradeVisor" under Expert Advisors
3. Drag it to a chart
4. Configure inputs:
   - **Server**: `thetradevisor.com`
   - **API Key**: Your API key from TheTradeVisor
   - **Account ID**: Your account number
   - **Symbol Mapping**: Auto-detect (recommended)

### 4. Enable Auto Trading
1. Click "AutoTrading" button in toolbar
2. Ensure EA is running (smiley face in top right)
3. Check Experts tab for any errors

### 5. Add Account in TheTradeVisor
1. Go to Settings → Trading Accounts
2. Click "Add New Account"
3. Fill in:
   - **Account Name**: Display name
   - **Account Number**: Your MT4/MT5 account number
   - **Broker**: Select from dropdown
   - **Platform**: MT4 or MT5
   - **Server**: Your broker's server
   - **Investor Password**: Read-only password
4. Click "Add Account"

## Symbol Mapping

### Auto-Detection (Recommended)
The EA will automatically detect and map symbols like:
- `XAUUSD` → `XAUUSD.sd`
- `EURUSD` → `EURUSD.sd`

### Manual Mapping
If auto-detection fails:
1. Go to Settings → Symbol Mapping
2. Add manual mappings
3. Format: `Broker Symbol` → `TheTradeVisor Symbol`

## Verification

### 1. Check Connection Status
- Green dot = Connected
- Yellow dot = Syncing
- Red dot = Disconnected

### 2. Verify Data Flow
1. Wait 5-10 minutes
2. Check Analytics dashboard
3. Verify recent trades appear

### 3. Check Logs
- MT4/MT5 Experts tab
- TheTradeVisor Settings → Logs

## Troubleshooting

### Connection Issues
- Verify API key is correct
- Check firewall settings
- Ensure EA is enabled
- Verify broker server

### Data Not Appearing
- Check symbol mapping
- Verify account permissions
- Check time synchronization
- Review error logs

### EA Errors
- Reinstall EA
- Check MT4/MT5 version
- Verify .NET Framework (MT5)
- Contact broker if needed

## Security

### Investor Password
- Use read-only investor password
- Never share main trading password
- Change password regularly

### API Key
- Keep API key secure
- Regenerate if compromised
- Use IP restrictions if available

## Multiple Accounts

You can add multiple trading accounts:
- Up to 5 for standard accounts
- Unlimited for enterprise accounts

## Next Steps

After adding accounts:
1. [View your dashboard](DASHBOARD_OVERVIEW.md)
2. [Explore analytics features](BASIC_ANALYTICS.md)
3. [Set up public profile](../guides/PUBLIC_PROFILES_USER_GUIDE.md)

## Support

For account connection issues:
- Check troubleshooting section
- Contact support with screenshots
- Include EA logs and error messages
