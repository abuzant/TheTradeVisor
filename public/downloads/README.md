# TradeVisor EA Automatic Installer

## Overview

The TradeVisor automatic installer (`install_ea.bat`) intelligently detects all MetaTrader 4 and MetaTrader 5 installations on a Windows PC and automatically copies the appropriate Expert Advisor files to the correct locations.

## Files Included

1. **TradeVisor.ex4** - Expert Advisor for MetaTrader 4
2. **TradeVisor.ex5** - Expert Advisor for MetaTrader 5
3. **install_ea.bat** - Automatic installation script

## How It Works

### Detection Methods

The installer uses three comprehensive methods to find all MetaTrader installations:

#### Method 1: AppData Profile Scanning (Primary)
- **Location**: `%AppData%\MetaQuotes\Terminal\{PROFILEID}\`
- **MT4 Path**: `MQL4\Experts\Advisors\`
- **MT5 Path**: `MQL5\Experts\Advisors\`
- **Why**: This is where MT4/MT5 stores user data profiles (32-character hash folders)
- **Coverage**: Catches 95%+ of installations

#### Method 2: Program Files Search (Legacy)
- **Locations**: 
  - `C:\Program Files\*MetaTrader*\`
  - `C:\Program Files (x86)\*MetaTrader*\`
  - `C:\Program Files\*MT4*\`
  - `C:\Program Files\*MT5*\`
- **Paths**:
  - `MQL4\Experts\Advisors\` (MT4)
  - `MQL5\Experts\Advisors\` (MT5)
  - `MQL\Experts\` (Legacy)
- **Why**: Older installations may store data in Program Files
- **Coverage**: Catches legacy and portable installations

#### Method 3: Broker-Specific Locations
- **Common Brokers**:
  - XM MT4/MT5
  - FXCM
  - OANDA
  - Pepperstone
  - IC Markets
- **Locations**: Both `Program Files` and `Program Files (x86)`
- **Why**: Some brokers install to custom directories
- **Coverage**: Catches broker-branded terminals

### Installation Process

1. **Scan Phase**
   - Searches all three methods simultaneously
   - Identifies MT4 vs MT5 by folder structure
   - Counts total installations found

2. **Copy Phase**
   - Creates `Advisors` subfolder if missing
   - Copies appropriate EA file (ex4 for MT4, ex5 for MT5)
   - Verifies successful copy operation

3. **Report Phase**
   - Shows summary of installations
   - Reports success/failure for each terminal
   - Provides next steps for user

## Technical Details

### MT4 Installation Paths

```
Primary:
%AppData%\Roaming\MetaQuotes\Terminal\{PROFILEID}\MQL4\Experts\Advisors\

Legacy:
C:\Program Files\MetaTrader 4\MQL4\Experts\Advisors\
C:\Program Files (x86)\MetaTrader 4\MQL4\Experts\Advisors\
C:\Program Files\{BrokerName}\MQL4\Experts\Advisors\

Very Old:
C:\Program Files\MetaQuotes\MQL\Experts\
```

### MT5 Installation Paths

```
Primary:
%AppData%\Roaming\MetaQuotes\Terminal\{PROFILEID}\MQL5\Experts\Advisors\

Legacy:
C:\Program Files\MetaTrader 5\MQL5\Experts\Advisors\
C:\Program Files (x86)\MetaTrader 5\MQL5\Experts\Advisors\
C:\Program Files\{BrokerName}\MQL5\Experts\Advisors\

Very Old:
C:\Program Files\MetaQuotes\MQL5\Experts\
```

## Batch File Logic

### Key Features

1. **Smart Detection**: Uses wildcards and pattern matching to find installations
2. **Error Handling**: Checks for file existence before copying
3. **Permission Handling**: Gracefully handles access denied errors
4. **User Feedback**: Provides clear progress messages and final summary
5. **Idempotent**: Safe to run multiple times without issues

### Variables Used

- `INSTALL_DIR`: Directory where batch file is located
- `MT4_EA`: Path to TradeVisor.ex4 file
- `MT5_EA`: Path to TradeVisor.ex5 file
- `MT4_COUNT`: Number of MT4 installations found
- `MT5_COUNT`: Number of MT5 installations found
- `TOTAL_COUNT`: Total installations completed

### Exit Codes

- `0`: Success (at least one installation completed)
- `1`: Error (EA files not found or no installations detected)

## Manual Installation

If automatic installation fails, users can manually install:

### For MT4:
1. Open MT4 terminal
2. Go to **File → Open Data Folder**
3. Navigate to `MQL4\Experts\Advisors`
4. Copy `TradeVisor.ex4` to this folder
5. Restart MT4

### For MT5:
1. Open MT5 terminal
2. Go to **File → Open Data Folder**
3. Navigate to `MQL5\Experts\Advisors`
4. Copy `TradeVisor.ex5` to this folder
5. Restart MT5

## Configuration After Installation

Users must configure the EA in MetaTrader:

1. **Enable AutoTrading**: Click green button in toolbar
2. **Allow WebRequest**: 
   - Tools → Options → Expert Advisors
   - Check "Allow WebRequest for listed URL"
   - Add: `https://api.thetradevisor.com`
3. **Enter API Key**: Get from TheTradeVisor dashboard
4. **Enable DLL Imports**: Check in EA settings

## References

- [MQL5 Forum: Where to place EA files](https://www.mql5.com/en/forum/156651)
- [MQL5 Articles: File System in MetaTrader](https://www.mql5.com/en/articles/1388)
- [MetaTrader 5 Official Documentation](https://www.metatrader5.com/en/terminal/help/startworking/settings#ea)

## Troubleshooting

### Installer doesn't find terminals
- Run as Administrator
- Check if MetaTrader is installed in non-standard location
- Use manual installation method

### Permission denied errors
- Close MetaTrader before running installer
- Run installer as Administrator
- Check antivirus isn't blocking

### EA not appearing in Navigator
- Restart MetaTrader
- Press F4 to open MetaEditor and compile
- Check file is in correct folder

## Support

For help:
- Website: https://thetradevisor.com
- Documentation: https://thetradevisor.com/docs
- Contact: hello@thetradevisor.com

---

**Version**: 1.0  
**Last Updated**: November 2025  
**Platform**: Windows 7/8/10/11  
**License**: Proprietary
