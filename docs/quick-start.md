# ⚡ Quick Start Guide

Get TheTradeVisor up and running in 5 minutes!

## 🎯 Overview

This guide will help you:
1. Set up your first trading account
2. Connect your MT4/MT5 terminal
3. View your first analytics

## 📝 Prerequisites

- TheTradeVisor installed and running
- MT4 or MT5 terminal
- Basic understanding of trading

## 🚀 Step 1: Register an Account

1. Visit your TheTradeVisor installation
2. Click **Register**
3. Fill in your details:
   - Name
   - Email
   - Password
4. Verify your email (check spam folder)

## 🔑 Step 2: Get Your API Key

1. Log in to your account
2. Go to **Settings** → **API Key**
3. Copy your API key (starts with `tvsr_`)
4. **Keep it secure!** This is your authentication token

## 📊 Step 3: Install MT4/MT5 Expert Advisor

### Download the EA

1. Go to **Dashboard**
2. Click **Download EA** button
3. Save the `.ex4` or `.ex5` file

### Install in MT4/MT5

1. Open MetaTrader
2. Go to **File** → **Open Data Folder**
3. Navigate to `MQL4/Experts` (MT4) or `MQL5/Experts` (MT5)
4. Copy the downloaded EA file here
5. Restart MetaTrader
6. The EA should appear in the Navigator panel

### Configure the EA

1. Drag the EA onto any chart
2. In the **Inputs** tab, enter:
   - **API Key**: Your API key from Step 2
   - **API URL**: Your TheTradeVisor URL
   - **Account Name**: A friendly name for this account
3. Enable **Allow DLL imports** in the **Common** tab
4. Click **OK**

## ✅ Step 4: Verify Connection

### Check in MetaTrader

Look for messages in the **Experts** tab:
```
TheTradeVisor EA initialized
Account registered successfully
Sending trade data...
```

### Check in TheTradeVisor

1. Go to **Dashboard**
2. You should see your trading account listed
3. Check **Last Sync** time - should be recent

## 📈 Step 5: View Your Analytics

### Dashboard

- **Account Overview**: Balance, equity, profit
- **Recent Trades**: Latest trading activity
- **Performance Chart**: Visual performance tracking

### Analytics Page

Visit `/analytics` to see:
- **Global Trading Statistics**
- **Popular Trading Pairs**
- **Trading by Hour**
- **Top Trading Countries** (if GeoIP is configured)

### Performance Page

Visit `/performance` for:
- **Win Rate Analysis**
- **Profit/Loss Breakdown**
- **Trading Patterns**
- **Risk Metrics**

## 🎨 Step 6: Customize Your Experience

### Set Display Currency

1. Go to **Settings** → **Currency**
2. Select your preferred currency (USD, EUR, GBP, etc.)
3. All amounts will be converted automatically

### Manage Multiple Accounts

1. Install the EA on multiple MT4/MT5 terminals
2. Use the same API key
3. All accounts will appear in your dashboard
4. Compare performance across accounts

## 📱 Step 7: Explore Features

### Broker Analytics

- Compare broker performance
- See which brokers are most popular
- Analyze spreads and execution

### Symbol Analytics

- View performance by trading pair
- Identify your most profitable symbols
- See global trading trends

### Export Data

- Export trades to CSV
- Generate PDF reports
- Download account data

## 🔔 Pro Tips

### Optimize Performance

1. **Enable Auto-Sync**: EA syncs every 5 minutes by default
2. **Check Logs**: Monitor the Experts tab for any issues
3. **Keep EA Running**: Don't remove the EA from charts

### Security Best Practices

1. **Never share your API key**
2. **Use strong passwords**
3. **Enable 2FA** (if available)
4. **Regularly review account activity**

### Get the Most Value

1. **Review analytics weekly**: Identify patterns
2. **Compare brokers**: Find the best execution
3. **Track performance**: Set goals and monitor progress
4. **Export reports**: Keep records for tax purposes

## 🆘 Troubleshooting

### EA Not Connecting

**Problem**: "Connection failed" in Experts tab

**Solutions**:
1. Check your API key is correct
2. Verify API URL matches your installation
3. Ensure "Allow DLL imports" is enabled
4. Check firewall isn't blocking connection

### No Data Showing

**Problem**: Dashboard is empty

**Solutions**:
1. Wait 5-10 minutes for first sync
2. Check EA is running (smile icon in chart corner)
3. Verify trades exist in MT4/MT5 history
4. Check `storage/logs/laravel.log` for errors

### Sync Issues

**Problem**: "Last Sync" time is old

**Solutions**:
1. Restart MetaTrader
2. Remove and re-add the EA
3. Check internet connection
4. Verify API key hasn't been regenerated

## 📚 Next Steps

Now that you're set up, explore:

- [Dashboard Guide](features/dashboard.md)
- [Analytics Features](features/analytics.md)
- [API Documentation](api/overview.md)
- [Performance Tracking](features/performance.md)

## 🎉 You're All Set!

You're now ready to track and analyze your trading performance with TheTradeVisor!

### Need Help?

- **Email**: hello@thetradevisor.com
- **Website**: https://thetradevisor.com/
- **GitHub**: https://github.com/abuzant/TheTradeVisor

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

---

**Happy Trading!** 📈
