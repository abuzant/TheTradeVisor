# Building TradeVisor-Setup.exe with InstallForge

## Prerequisites

1. Download and install InstallForge from https://installforge.net/
2. Have the following files ready:
   - `TradeVisor.ex4` (MT4 Expert Advisor)
   - `TradeVisor.ex5` (MT5 Expert Advisor)
   - `install_ea.bat` (from this directory)

## InstallForge Configuration

### General Settings

- **Application Name**: TradeVisor
- **Application Version**: 1.0.0
- **Company Name**: TheTradeVisor
- **Company Website**: https://thetradevisor.com
- **Support Email**: hello@thetradevisor.com

### Files to Include

Add these files to the installer:

1. **TradeVisor.ex4**
   - Source: Your compiled MT4 EA file
   - Destination: `{app}\TradeVisor.ex4`

2. **TradeVisor.ex5**
   - Source: Your compiled MT5 EA file
   - Destination: `{app}\TradeVisor.ex5`

3. **install_ea.bat**
   - Source: `/www/public/downloads/install_ea.bat`
   - Destination: `{app}\install_ea.bat`

### Installation Settings

- **Default Installation Directory**: `C:\Program Files\TradeVisor`
- **Allow user to change directory**: Yes
- **Create desktop shortcut**: No (not needed for EA)
- **Create start menu entry**: No (not needed for EA)

### Post-Installation Actions

**CRITICAL**: Configure the installer to run the batch file after installation:

1. Go to **Setup → Post-Installation**
2. Add a new action:
   - **Type**: Execute File
   - **File**: `{app}\install_ea.bat`
   - **Parameters**: (leave empty)
   - **Working Directory**: `{app}`
   - **Wait for completion**: Yes
   - **Show window**: Normal (so user can see progress)

### Dialog Customization

#### Welcome Screen
```
Welcome to the TradeVisor Expert Advisor Setup

This installer will automatically detect all your MetaTrader 4 and 
MetaTrader 5 installations and install the TradeVisor EA to each one.

The installation process will:
• Scan your computer for MT4/MT5 terminals
• Copy the appropriate EA files to each terminal
• Show you a summary of installations completed

Click Next to continue.
```

#### License Agreement
Use the standard license or create a custom EULA if needed.

#### Installation Progress
Keep default settings - the batch file will show its own progress.

#### Completion Screen
```
TradeVisor EA Installation Complete!

The Expert Advisor has been installed to your MetaTrader terminals.

Next Steps:
1. Open your MT4 or MT5 terminal
2. Find TradeVisor in the Navigator panel
3. Drag it onto a chart
4. Enter your API key from thetradevisor.com
5. Enable AutoTrading and WebRequest

Visit https://thetradevisor.com/download for detailed instructions.
```

### Installer Appearance

- **Icon**: Use TradeVisor logo (if available)
- **Banner**: Use company branding
- **Style**: Modern flat design
- **Colors**: Match website theme (indigo/purple)

### Advanced Settings

#### Compression
- **Compression Level**: Maximum (to reduce file size)
- **Solid Compression**: Yes

#### Security
- **Sign the installer**: Recommended (requires code signing certificate)
- **Require Administrator**: No (batch file will request if needed)

#### Uninstaller
- **Create uninstaller**: Yes
- **Uninstaller Location**: `{app}\uninstall.exe`
- **Remove files on uninstall**: Yes
- **Note**: Uninstaller will NOT remove EA files from MT4/MT5 folders (by design)

## Building the Installer

1. Open InstallForge
2. Create new project or load existing configuration
3. Configure all settings as above
4. Add all three files (ex4, ex5, bat)
5. Configure post-installation action to run batch file
6. Test the configuration
7. Click **Build** → **Build Setup**
8. Output file will be: `TradeVisor-Setup.exe`

## Testing the Installer

Before releasing:

1. **Test on clean VM**:
   - Windows 10/11 fresh install
   - Install MT4 and/or MT5
   - Run the installer
   - Verify EA appears in Navigator

2. **Test with multiple terminals**:
   - Install multiple MT4/MT5 instances
   - Run installer
   - Verify all terminals receive the EA

3. **Test edge cases**:
   - No MetaTrader installed (should show warning)
   - Non-standard installation paths
   - Permission issues (Program Files)

4. **Test uninstaller**:
   - Verify it removes installer files
   - Verify it doesn't break MT4/MT5

## Deployment

1. Upload `TradeVisor-Setup.exe` to `/www/public/downloads/`
2. Update download page if needed
3. Test download link works
4. Verify Google Analytics tracking fires

## File Locations After Installation

The installer places files in:
```
C:\Program Files\TradeVisor\
├── TradeVisor.ex4
├── TradeVisor.ex5
├── install_ea.bat
└── uninstall.exe
```

The batch file then copies EA files to:
```
%AppData%\MetaQuotes\Terminal\{PROFILEID}\MQL4\Experts\Advisors\TradeVisor.ex4
%AppData%\MetaQuotes\Terminal\{PROFILEID}\MQL5\Experts\Advisors\TradeVisor.ex5
```

## Troubleshooting Build Issues

### InstallForge won't start
- Run as Administrator
- Check Windows version compatibility

### Files not being included
- Verify file paths are correct
- Use absolute paths if relative paths fail

### Post-installation action not running
- Check "Wait for completion" is enabled
- Verify batch file path uses `{app}` variable
- Test batch file manually first

### Installer too large
- Increase compression level
- Enable solid compression
- Remove unnecessary files

## Version Control

Keep these files in version control:
- `install_ea.bat` (this directory)
- InstallForge project file (`.ifp`)
- Build scripts (if any)

Do NOT commit:
- `TradeVisor-Setup.exe` (binary, too large)
- Compiled EA files (proprietary)

## Code Signing (Optional but Recommended)

To avoid Windows SmartScreen warnings:

1. Purchase code signing certificate
2. Install certificate on build machine
3. Configure InstallForge to sign installer
4. Rebuild with signature

Cost: ~$100-300/year
Benefit: Professional appearance, no warnings

## Support

For InstallForge help:
- Website: https://installforge.net/
- Documentation: https://installforge.net/docs/
- Forum: https://installforge.net/forum/

For TradeVisor questions:
- Email: hello@thetradevisor.com
- Website: https://thetradevisor.com/contact

---

**Last Updated**: November 2025  
**InstallForge Version**: 1.4.x or later  
**Target Platform**: Windows 7/8/10/11
