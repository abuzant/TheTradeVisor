@echo off
setlocal enabledelayedexpansion

:: ============================================================================
:: TradeVisor EA Automatic Installer
:: Detects all MetaTrader 4/5 installations and copies EA files automatically
:: ============================================================================

echo.
echo ========================================
echo   TradeVisor EA Installation Script
echo ========================================
echo.

:: Get the directory where this batch file is located (installation directory)
set "INSTALL_DIR=%~dp0"
set "MT4_EA=%INSTALL_DIR%TradeVisor.ex4"
set "MT5_EA=%INSTALL_DIR%TradeVisor.ex5"

:: Counters for installations
set /a MT4_COUNT=0
set /a MT5_COUNT=0
set /a TOTAL_COUNT=0

:: Check if EA files exist
if not exist "%MT4_EA%" (
    echo ERROR: TradeVisor.ex4 not found in installation directory!
    goto :end_error
)

if not exist "%MT5_EA%" (
    echo ERROR: TradeVisor.ex5 not found in installation directory!
    goto :end_error
)

echo [1/4] Searching for MetaTrader installations...
echo.

:: ============================================================================
:: METHOD 1: Search in AppData\Roaming\MetaQuotes\Terminal (Primary Method)
:: This is where MT4/MT5 stores user data profiles
:: ============================================================================

set "APPDATA_BASE=%AppData%\MetaQuotes\Terminal"

if exist "%APPDATA_BASE%" (
    echo Scanning AppData profiles...
    
    :: Loop through all profile directories (32-character hash folders)
    for /d %%P in ("%APPDATA_BASE%\*") do (
        set "PROFILE_DIR=%%P"
        
        :: Check for MT5 installation (MQL5\Experts folder)
        if exist "!PROFILE_DIR!\MQL5\Experts" (
            echo   Found MT5 profile: %%~nxP
            
            :: Create Advisors subfolder if it doesn't exist
            if not exist "!PROFILE_DIR!\MQL5\Experts\Advisors" (
                mkdir "!PROFILE_DIR!\MQL5\Experts\Advisors" 2>nul
            )
            
            :: Copy MT5 EA file
            copy /Y "%MT5_EA%" "!PROFILE_DIR!\MQL5\Experts\Advisors\" >nul 2>&1
            if !errorlevel! equ 0 (
                echo   [OK] Installed TradeVisor.ex5 to MT5 profile
                set /a MT5_COUNT+=1
                set /a TOTAL_COUNT+=1
            ) else (
                echo   [FAILED] Could not copy to MT5 profile
            )
        )
        
        :: Check for MT4 installation (MQL4\Experts folder)
        if exist "!PROFILE_DIR!\MQL4\Experts" (
            echo   Found MT4 profile: %%~nxP
            
            :: Create Advisors subfolder if it doesn't exist
            if not exist "!PROFILE_DIR!\MQL4\Experts\Advisors" (
                mkdir "!PROFILE_DIR!\MQL4\Experts\Advisors" 2>nul
            )
            
            :: Copy MT4 EA file
            copy /Y "%MT4_EA%" "!PROFILE_DIR!\MQL4\Experts\Advisors\" >nul 2>&1
            if !errorlevel! equ 0 (
                echo   [OK] Installed TradeVisor.ex4 to MT4 profile
                set /a MT4_COUNT+=1
                set /a TOTAL_COUNT+=1
            ) else (
                echo   [FAILED] Could not copy to MT4 profile
            )
        )
    )
)

echo.
echo [2/4] Checking Program Files locations...
echo.

:: ============================================================================
:: METHOD 2: Search in Program Files (Legacy installations)
:: Older MT4/MT5 versions may store data here
:: ============================================================================

:: Check both Program Files directories (x86 and x64)
set "PF_PATHS[0]=%ProgramFiles%"
set "PF_PATHS[1]=%ProgramFiles(x86)%"

for /L %%i in (0,1,1) do (
    if defined PF_PATHS[%%i] (
        set "PF_DIR=!PF_PATHS[%%i]!"
        
        :: Search for MetaTrader installations in Program Files
        for /d %%M in ("!PF_DIR!\*MetaTrader*" "!PF_DIR!\*MT4*" "!PF_DIR!\*MT5*") do (
            set "MT_DIR=%%M"
            
            :: Check for MT5 in Program Files
            if exist "!MT_DIR!\MQL5\Experts" (
                echo   Found MT5 installation: %%~nxM
                
                if not exist "!MT_DIR!\MQL5\Experts\Advisors" (
                    mkdir "!MT_DIR!\MQL5\Experts\Advisors" 2>nul
                )
                
                copy /Y "%MT5_EA%" "!MT_DIR!\MQL5\Experts\Advisors\" >nul 2>&1
                if !errorlevel! equ 0 (
                    echo   [OK] Installed TradeVisor.ex5
                    set /a MT5_COUNT+=1
                    set /a TOTAL_COUNT+=1
                ) else (
                    echo   [FAILED] Permission denied or path not accessible
                )
            )
            
            :: Check for MT4 in Program Files
            if exist "!MT_DIR!\MQL4\Experts" (
                echo   Found MT4 installation: %%~nxM
                
                if not exist "!MT_DIR!\MQL4\Experts\Advisors" (
                    mkdir "!MT_DIR!\MQL4\Experts\Advisors" 2>nul
                )
                
                copy /Y "%MT4_EA%" "!MT_DIR!\MQL4\Experts\Advisors\" >nul 2>&1
                if !errorlevel! equ 0 (
                    echo   [OK] Installed TradeVisor.ex4
                    set /a MT4_COUNT+=1
                    set /a TOTAL_COUNT+=1
                ) else (
                    echo   [FAILED] Permission denied or path not accessible
                )
            )
            
            :: Check for legacy MQL folder structure (very old installations)
            if exist "!MT_DIR!\MQL\Experts" (
                echo   Found legacy MetaTrader installation: %%~nxM
                
                :: Try to determine if it's MT4 or MT5 by checking executable
                if exist "!MT_DIR!\terminal.exe" (
                    copy /Y "%MT4_EA%" "!MT_DIR!\MQL\Experts\" >nul 2>&1
                    if !errorlevel! equ 0 (
                        echo   [OK] Installed TradeVisor.ex4 (legacy MT4)
                        set /a MT4_COUNT+=1
                        set /a TOTAL_COUNT+=1
                    )
                )
                
                if exist "!MT_DIR!\terminal64.exe" (
                    copy /Y "%MT5_EA%" "!MT_DIR!\MQL\Experts\" >nul 2>&1
                    if !errorlevel! equ 0 (
                        echo   [OK] Installed TradeVisor.ex5 (legacy MT5)
                        set /a MT5_COUNT+=1
                        set /a TOTAL_COUNT+=1
                    )
                )
            )
        )
    )
)

echo.
echo [3/4] Checking common broker-specific locations...
echo.

:: ============================================================================
:: METHOD 3: Check common broker-specific installation paths
:: ============================================================================

set "BROKER_PATHS[0]=C:\Program Files\XM MT4"
set "BROKER_PATHS[1]=C:\Program Files\XM MT5"
set "BROKER_PATHS[2]=C:\Program Files\FXCM"
set "BROKER_PATHS[3]=C:\Program Files\OANDA"
set "BROKER_PATHS[4]=C:\Program Files\Pepperstone"
set "BROKER_PATHS[5]=C:\Program Files\IC Markets"
set "BROKER_PATHS[6]=C:\Program Files (x86)\XM MT4"
set "BROKER_PATHS[7]=C:\Program Files (x86)\XM MT5"
set "BROKER_PATHS[8]=C:\Program Files (x86)\FXCM"
set "BROKER_PATHS[9]=C:\Program Files (x86)\OANDA"
set "BROKER_PATHS[10]=C:\Program Files (x86)\Pepperstone"
set "BROKER_PATHS[11]=C:\Program Files (x86)\IC Markets"

for /L %%i in (0,1,11) do (
    if defined BROKER_PATHS[%%i] (
        set "BROKER_DIR=!BROKER_PATHS[%%i]!"
        
        if exist "!BROKER_DIR!" (
            :: Check for MT5
            if exist "!BROKER_DIR!\MQL5\Experts" (
                echo   Found broker MT5: !BROKER_DIR!
                
                if not exist "!BROKER_DIR!\MQL5\Experts\Advisors" (
                    mkdir "!BROKER_DIR!\MQL5\Experts\Advisors" 2>nul
                )
                
                copy /Y "%MT5_EA%" "!BROKER_DIR!\MQL5\Experts\Advisors\" >nul 2>&1
                if !errorlevel! equ 0 (
                    echo   [OK] Installed TradeVisor.ex5
                    set /a MT5_COUNT+=1
                    set /a TOTAL_COUNT+=1
                )
            )
            
            :: Check for MT4
            if exist "!BROKER_DIR!\MQL4\Experts" (
                echo   Found broker MT4: !BROKER_DIR!
                
                if not exist "!BROKER_DIR!\MQL4\Experts\Advisors" (
                    mkdir "!BROKER_DIR!\MQL4\Experts\Advisors" 2>nul
                )
                
                copy /Y "%MT4_EA%" "!BROKER_DIR!\MQL4\Experts\Advisors\" >nul 2>&1
                if !errorlevel! equ 0 (
                    echo   [OK] Installed TradeVisor.ex4
                    set /a MT4_COUNT+=1
                    set /a TOTAL_COUNT+=1
                )
            )
        )
    )
)

:: ============================================================================
:: SUMMARY
:: ============================================================================

echo.
echo [4/4] Installation Summary
echo ========================================

if !TOTAL_COUNT! gtr 0 (
    echo.
    echo SUCCESS! TradeVisor EA has been installed to:
    echo   - %MT4_COUNT% MetaTrader 4 instance(s)
    echo   - %MT5_COUNT% MetaTrader 5 instance(s)
    echo.
    echo NEXT STEPS:
    echo 1. Open your MetaTrader terminal
    echo 2. Click on "Navigator" panel (Ctrl+N)
    echo 3. Expand "Expert Advisors" section
    echo 4. You should see "TradeVisor" listed
    echo 5. Drag and drop it onto a chart
    echo 6. Enter your API key when prompted
    echo.
    echo NOTE: If you don't see the EA, try:
    echo   - Restarting MetaTrader
    echo   - Pressing F4 to open MetaEditor and compile
    echo   - Checking Tools ^> Options ^> Expert Advisors ^> Allow automated trading
    echo.
    goto :end_success
) else (
    echo.
    echo WARNING: No MetaTrader installations detected!
    echo.
    echo This could mean:
    echo   1. MetaTrader is not installed on this computer
    echo   2. MetaTrader is installed in a non-standard location
    echo   3. You need administrator privileges to access the folders
    echo.
    echo MANUAL INSTALLATION:
    echo.
    echo For MetaTrader 4:
    echo   1. Open MT4 terminal
    echo   2. Click File ^> Open Data Folder
    echo   3. Navigate to MQL4\Experts\Advisors
    echo   4. Copy TradeVisor.ex4 from: %INSTALL_DIR%
    echo.
    echo For MetaTrader 5:
    echo   1. Open MT5 terminal
    echo   2. Click File ^> Open Data Folder
    echo   3. Navigate to MQL5\Experts\Advisors
    echo   4. Copy TradeVisor.ex5 from: %INSTALL_DIR%
    echo.
    echo For help, visit: https://thetradevisor.com/help
    echo.
    goto :end_warning
)

:end_error
echo.
echo Installation failed. Please contact support at hello@thetradevisor.com
echo.
pause
exit /b 1

:end_warning
echo.
pause
exit /b 0

:end_success
echo.
echo Installation completed successfully!
echo.
timeout /t 10
exit /b 0
