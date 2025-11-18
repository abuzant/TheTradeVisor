# Setup File Deployment Guide

## Overview

The TradeVisor-Setup.exe installer is securely stored and served through Laravel's storage system, ensuring it never gets committed to GitHub while remaining easily downloadable by users.

## File Location

**Storage Path**: `/var/www/thetradevisor.com/storage/setup.exe`

**Size**: ~1.5 MB

**Access**: Served via Laravel route (not directly accessible)

## Security Configuration

### .gitignore Protection

Added to `.gitignore` to prevent accidental commits:

```gitignore
# EA Installer - Keep Private
/storage/app/downloads/*.exe
TradeVisor-Setup.exe
*-Setup.exe
```

This ensures:
- ✅ Setup file never gets committed to GitHub
- ✅ Repository stays clean and lightweight
- ✅ Proprietary installer remains private

## Download System

### Route Configuration

**Download Page**: `/download` (public)
**Download File**: `/download/setup` (public)

Routes defined in `/www/routes/web.php`:
```php
Route::get('/download', [PublicController::class, 'download'])->name('download');
Route::get('/download/setup', [PublicController::class, 'downloadSetup'])->name('download.setup');
```

### Controller Method

Location: `/www/app/Http/Controllers/PublicController.php`

```php
public function downloadSetup()
{
    $filePath = storage_path('setup.exe');
    
    if (!file_exists($filePath)) {
        abort(404, 'Setup file not found');
    }
    
    // Track download in logs
    \Log::info('EA Setup downloaded', [
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'user_id' => auth()->id() ?? 'guest'
    ]);
    
    return response()->download($filePath, 'TradeVisor-Setup.exe', [
        'Content-Type' => 'application/octet-stream',
        'Content-Disposition' => 'attachment; filename="TradeVisor-Setup.exe"',
    ]);
}
```

### Features

1. **Secure Serving**: File served through Laravel, not direct web access
2. **Download Tracking**: Logs every download with IP, user agent, and user ID
3. **Proper Headers**: Correct MIME type and filename
4. **Error Handling**: Returns 404 if file missing
5. **Google Analytics**: Frontend tracking on download button click

## Download Page Integration

**View**: `/www/resources/views/public/download.blade.php`

Download button uses the route:
```blade
<a href="{{ route('download.setup') }}" 
   onclick="gtag('event', 'download', {
       'event_category': 'EA',
       'event_label': 'TradeVisor Setup',
       'value': 1
   });">
    Download TradeVisor-Setup.exe
</a>
```

## Updating the Setup File

When you need to update the installer:

### Option 1: Direct Upload (Current Method)
```bash
# Upload via SCP
scp TradeVisor-Setup.exe tradeadmin@thetradevisor.com:/var/www/thetradevisor.com/storage/setup.exe

# Set correct permissions
ssh tradeadmin@thetradevisor.com
cd /var/www/thetradevisor.com/storage
chmod 664 setup.exe
chown tradeadmin:www-data setup.exe
```

### Option 2: Via Admin Panel (Future Enhancement)
Could add an admin upload form to replace the file without SSH access.

### Option 3: Deployment Script
```bash
#!/bin/bash
# deploy-setup.sh

SETUP_FILE="$1"
SERVER="tradeadmin@thetradevisor.com"
DEST="/var/www/thetradevisor.com/storage/setup.exe"

if [ -z "$SETUP_FILE" ]; then
    echo "Usage: ./deploy-setup.sh <path-to-setup.exe>"
    exit 1
fi

echo "Uploading $SETUP_FILE to production..."
scp "$SETUP_FILE" "$SERVER:$DEST"

echo "Setting permissions..."
ssh "$SERVER" "chmod 664 $DEST && chown tradeadmin:www-data $DEST"

echo "✅ Setup file deployed successfully!"
```

## Download Analytics

### Logging

Every download is logged to Laravel logs:
```
[2025-11-17 11:45:00] local.INFO: EA Setup downloaded {
    "ip": "192.168.1.100",
    "user_agent": "Mozilla/5.0...",
    "user_id": "guest"
}
```

View logs:
```bash
tail -f /var/www/thetradevisor.com/storage/logs/laravel.log | grep "EA Setup"
```

### Google Analytics

Frontend tracking captures:
- Event: `download`
- Category: `EA`
- Label: `TradeVisor Setup`
- Value: `1`

View in Google Analytics:
- Events → download → EA → TradeVisor Setup

### Statistics Query

Get download count from logs:
```bash
grep "EA Setup downloaded" /var/www/thetradevisor.com/storage/logs/laravel.log | wc -l
```

## File Verification

Check if file exists and is accessible:

```bash
# Check file exists
ls -lh /var/www/thetradevisor.com/storage/setup.exe

# Check permissions (should be 664)
stat /var/www/thetradevisor.com/storage/setup.exe

# Test download route
curl -I https://thetradevisor.com/download/setup
# Should return: HTTP/2 200
# Content-Type: application/octet-stream
# Content-Disposition: attachment; filename="TradeVisor-Setup.exe"
```

## Troubleshooting

### File Not Found (404)

**Symptoms**: Download button returns 404

**Solutions**:
1. Check file exists: `ls -l /var/www/thetradevisor.com/storage/setup.exe`
2. Check permissions: `chmod 664 /var/www/thetradevisor.com/storage/setup.exe`
3. Check ownership: `chown tradeadmin:www-data /var/www/thetradevisor.com/storage/setup.exe`

### Permission Denied

**Symptoms**: 500 error or "Permission denied" in logs

**Solutions**:
```bash
cd /var/www/thetradevisor.com/storage
chmod 664 setup.exe
chown tradeadmin:www-data setup.exe
```

### File Corrupted

**Symptoms**: Download completes but installer won't run

**Solutions**:
1. Re-upload the file
2. Verify file integrity: `md5sum setup.exe`
3. Compare with original file hash

### Route Not Working

**Symptoms**: Route returns 404

**Solutions**:
```bash
# Clear route cache
php artisan route:clear

# Verify route exists
php artisan route:list --name=download

# Check web server config
sudo nginx -t
```

## Best Practices

### Version Control
- ✅ Never commit .exe files to Git
- ✅ Keep installer in storage directory
- ✅ Document version in separate file

### Security
- ✅ Serve through Laravel (not direct access)
- ✅ Log all downloads
- ✅ Monitor for unusual download patterns
- ✅ Consider rate limiting if needed

### Deployment
- ✅ Test installer before uploading
- ✅ Keep backup of previous version
- ✅ Document changes in changelog
- ✅ Notify users of updates

### Monitoring
- ✅ Track download counts
- ✅ Monitor error logs
- ✅ Check disk space regularly
- ✅ Review download analytics

## Future Enhancements

### Version Management
- Store multiple versions: `setup-v1.0.0.exe`, `setup-v1.1.0.exe`
- Allow users to download specific versions
- Show version history on download page

### Admin Upload Interface
- Create admin page for uploading new installer
- Automatic backup of previous version
- Version number tracking
- Changelog management

### Download Statistics Dashboard
- Show download count per day/week/month
- Geographic distribution
- User vs guest downloads
- Conversion rate (visits to downloads)

### CDN Integration
- Serve large files through CDN
- Reduce server bandwidth
- Faster downloads globally
- Cache invalidation on update

### Automatic Updates
- EA checks for new version on startup
- Notify users of available updates
- One-click update from within MT4/MT5
- Changelog display

## Support

### For Users
- Download Page: https://thetradevisor.com/download
- Documentation: https://thetradevisor.com/docs
- Support: hello@thetradevisor.com

### For Developers
- This Document: `/www/docs/technical/SETUP_FILE_DEPLOYMENT.md`
- Controller: `/www/app/Http/Controllers/PublicController.php`
- Routes: `/www/routes/web.php`
- View: `/www/resources/views/public/download.blade.php`

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
