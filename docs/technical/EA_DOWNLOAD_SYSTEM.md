# TradeVisor EA Download & Automatic Installation System

## Overview

Complete implementation of the TradeVisor Expert Advisor download and automatic installation system. Users can download a single installer that automatically detects all MetaTrader 4 and MT5 installations and installs the EA to the correct locations.

## Components Created

### 1. Batch Installation Script
**File**: `/www/public/downloads/install_ea.bat`

**Purpose**: Automatically detect and install EA files to all MetaTrader installations

**Features**:
- ✅ Scans AppData profiles (primary method)
- ✅ Searches Program Files (legacy installations)
- ✅ Checks broker-specific locations (XM, FXCM, Pepperstone, etc.)
- ✅ Creates Advisors subfolder if missing
- ✅ Copies appropriate file (ex4 for MT4, ex5 for MT5)
- ✅ Provides detailed progress feedback
- ✅ Shows installation summary
- ✅ Handles errors gracefully

**Detection Methods**:

1. **AppData Profile Scanning** (Primary - 95% coverage)
   ```
   %AppData%\MetaQuotes\Terminal\{PROFILEID}\MQL4\Experts\Advisors\
   %AppData%\MetaQuotes\Terminal\{PROFILEID}\MQL5\Experts\Advisors\
   ```

2. **Program Files Search** (Legacy installations)
   ```
   C:\Program Files\*MetaTrader*\MQL4\Experts\Advisors\
   C:\Program Files\*MetaTrader*\MQL5\Experts\Advisors\
   C:\Program Files (x86)\*MetaTrader*\MQL4\Experts\Advisors\
   C:\Program Files (x86)\*MetaTrader*\MQL5\Experts\Advisors\
   ```

3. **Broker-Specific Locations**
   ```
   C:\Program Files\XM MT4\
   C:\Program Files\XM MT5\
   C:\Program Files\FXCM\
   C:\Program Files\OANDA\
   C:\Program Files\Pepperstone\
   C:\Program Files\IC Markets\
   (and x86 variants)
   ```

### 2. Download Page
**File**: `/www/resources/views/public/download.blade.php`

**Route**: `/download` (public access)

**Features**:
- ✅ Prominent download button with Google Analytics tracking
- ✅ Feature highlights (auto-detection, one-click, safe)
- ✅ What's included section
- ✅ System requirements
- ✅ Step-by-step installation instructions
- ✅ Manual installation fallback
- ✅ Comprehensive troubleshooting guide
- ✅ Links to FAQ, docs, and support

**Google Analytics Tracking**:
```javascript
onclick="gtag('event', 'download', {
    'event_category': 'EA',
    'event_label': 'TradeVisor Setup',
    'value': 1
});"
```

### 3. Controller Method
**File**: `/www/app/Http/Controllers/PublicController.php`

**Method**: `download()`

**Route**: Added to `/www/routes/web.php`
```php
Route::get('/download', [App\Http\Controllers\PublicController::class, 'download'])->name('download');
```

### 4. Navigation Update
**File**: `/www/resources/views/components/public-layout.blade.php`

Added "Download" link to public navigation between Screenshots and Pricing.

### 5. Documentation

**README.md**: `/www/public/downloads/README.md`
- Technical documentation of batch file logic
- Detection methods explained
- Installation paths reference
- Manual installation instructions
- Troubleshooting guide

**SETUP_INSTRUCTIONS.md**: `/www/public/downloads/SETUP_INSTRUCTIONS.md`
- Complete guide for building installer with InstallForge
- Configuration settings
- Post-installation actions
- Testing procedures
- Deployment instructions

## Installation Flow

### User Journey

1. **Visit Download Page**
   - User navigates to `/download`
   - Sees prominent download button
   - Reads installation instructions

2. **Download Installer**
   - Clicks download button
   - Google Analytics tracks download event
   - Downloads `TradeVisor-Setup.exe` (~2 MB)

3. **Run Installer**
   - Double-clicks setup file
   - May see Windows SmartScreen warning (normal for new software)
   - Installer extracts files to `C:\Program Files\TradeVisor\`

4. **Automatic Installation**
   - Installer runs `install_ea.bat` automatically
   - Batch file scans for all MT4/MT5 installations
   - Copies EA files to appropriate locations
   - Shows progress and summary

5. **Configure in MetaTrader**
   - User opens MT4/MT5 terminal
   - Finds TradeVisor in Navigator
   - Drags onto chart
   - Enters API key
   - Enables AutoTrading and WebRequest

6. **Verification**
   - EA shows smiley face on chart
   - Experts tab shows "Connected to TheTradeVisor API"
   - Dashboard shows account within 60 seconds

## Files Required for Installer

To build the installer with InstallForge, you need:

1. **TradeVisor.ex4** - Compiled MT4 Expert Advisor
2. **TradeVisor.ex5** - Compiled MT5 Expert Advisor
3. **install_ea.bat** - Automatic installation script (created ✅)

## InstallForge Configuration

### Files to Include
```
Source                          Destination
------                          -----------
TradeVisor.ex4         →        {app}\TradeVisor.ex4
TradeVisor.ex5         →        {app}\TradeVisor.ex5
install_ea.bat         →        {app}\install_ea.bat
```

### Post-Installation Action
```
Type: Execute File
File: {app}\install_ea.bat
Parameters: (empty)
Working Directory: {app}
Wait for completion: Yes
Show window: Normal
```

### Output
```
File: TradeVisor-Setup.exe
Size: ~2 MB
Destination: /www/public/downloads/TradeVisor-Setup.exe
```

## Installation Paths

### MT4 Paths
```
Primary:
%AppData%\Roaming\MetaQuotes\Terminal\{PROFILEID}\MQL4\Experts\Advisors\TradeVisor.ex4

Legacy:
C:\Program Files\MetaTrader 4\MQL4\Experts\Advisors\TradeVisor.ex4
C:\Program Files (x86)\MetaTrader 4\MQL4\Experts\Advisors\TradeVisor.ex4

Very Old:
C:\Program Files\MetaQuotes\MQL\Experts\TradeVisor.ex4
```

### MT5 Paths
```
Primary:
%AppData%\Roaming\MetaQuotes\Terminal\{PROFILEID}\MQL5\Experts\Advisors\TradeVisor.ex5

Legacy:
C:\Program Files\MetaTrader 5\MQL5\Experts\Advisors\TradeVisor.ex5
C:\Program Files (x86)\MetaTrader 5\MQL5\Experts\Advisors\TradeVisor.ex5

Very Old:
C:\Program Files\MetaQuotes\MQL5\Experts\TradeVisor.ex5
```

## Configuration After Installation

Users must configure these settings in MetaTrader:

### 1. Enable AutoTrading
- Click green AutoTrading button in toolbar
- Or press Ctrl+E

### 2. Allow WebRequest
- Tools → Options → Expert Advisors
- Check "Allow WebRequest for listed URL"
- Add: `https://api.thetradevisor.com`

### 3. Enable DLL Imports
- In EA settings dialog
- Check "Allow DLL imports"

### 4. Enter API Key
- Get from Settings → API Key page
- Enter in EA configuration
- Same key for all accounts

## Troubleshooting

### Installer Issues

**No MetaTrader detected**:
- Run as Administrator
- Check non-standard installation paths
- Use manual installation method

**Permission denied**:
- Close MetaTrader before running
- Run installer as Administrator
- Check antivirus settings

### EA Issues

**Not appearing in Navigator**:
- Restart MetaTrader
- Press F4 to compile in MetaEditor
- Verify file is in correct folder

**Connection errors**:
- Check WebRequest is enabled
- Verify API key is correct
- Enable AutoTrading
- Check firewall settings

**No data in dashboard**:
- Wait 60 seconds for sync
- Check Experts tab for errors
- Verify account is active
- Ensure at least one trade exists

## Google Analytics Events

Download tracking:
```javascript
Event: download
Category: EA
Label: TradeVisor Setup
Value: 1
```

This allows tracking:
- Number of downloads
- Download conversion rate
- User journey from landing to download

## Security Considerations

### Windows SmartScreen
New software may trigger SmartScreen warning:
- Normal for software not widely distributed
- User must click "More info" → "Run anyway"
- Can be avoided with code signing certificate (~$100-300/year)

### Antivirus
Some antivirus may flag batch file:
- False positive (no malicious code)
- User may need to whitelist
- Code signing helps reduce false positives

### Permissions
Batch file requests admin if needed:
- Only for Program Files access
- AppData doesn't require admin
- Gracefully handles permission denied

## Testing Checklist

Before release:

- [ ] Test on clean Windows 10 VM
- [ ] Test on Windows 11
- [ ] Test with MT4 only
- [ ] Test with MT5 only
- [ ] Test with both MT4 and MT5
- [ ] Test with multiple terminals
- [ ] Test with broker-branded terminals
- [ ] Test with no MetaTrader installed
- [ ] Test manual installation method
- [ ] Test uninstaller
- [ ] Verify Google Analytics tracking
- [ ] Test download link
- [ ] Test all documentation links

## Deployment Steps

1. **Build Installer**:
   - Open InstallForge
   - Configure as per SETUP_INSTRUCTIONS.md
   - Build TradeVisor-Setup.exe

2. **Upload Files**:
   ```bash
   scp TradeVisor-Setup.exe user@server:/www/public/downloads/
   ```

3. **Verify Access**:
   - Visit https://thetradevisor.com/download
   - Test download button
   - Check Google Analytics

4. **Update Links**:
   - Add download link to landing page
   - Add to dashboard for logged-in users
   - Update documentation

## Future Enhancements

### Potential Improvements

1. **Auto-Update System**:
   - Check for EA updates on startup
   - Download and install automatically
   - Notify user of new features

2. **Configuration Wizard**:
   - GUI for entering API key
   - Test connection before saving
   - Guided setup process

3. **Multi-Language Support**:
   - Translate installer to multiple languages
   - Match MetaTrader language preference

4. **Silent Installation**:
   - Command-line parameters for automation
   - Corporate deployment support
   - Group policy integration

5. **Telemetry**:
   - Report installation success/failure
   - Track which detection method worked
   - Improve installer based on data

## Support Resources

### For Users
- Download Page: https://thetradevisor.com/download
- Documentation: https://thetradevisor.com/docs
- FAQ: https://thetradevisor.com/faq
- Support: hello@thetradevisor.com

### For Developers
- Batch File: `/www/public/downloads/install_ea.bat`
- README: `/www/public/downloads/README.md`
- Setup Guide: `/www/public/downloads/SETUP_INSTRUCTIONS.md`
- This Document: `/www/docs/technical/EA_DOWNLOAD_SYSTEM.md`

## References

- [MQL5 Forum: EA File Locations](https://www.mql5.com/en/forum/156651)
- [MQL5 Articles: File System](https://www.mql5.com/en/articles/1388)
- [InstallForge Documentation](https://installforge.net/docs/)
- [MetaTrader 5 Official Guide](https://www.metatrader5.com/en/terminal/help/startworking/settings#ea)

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
