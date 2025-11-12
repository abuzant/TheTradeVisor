# Storage Permissions Setup - Proper Group Access

## Summary

Configured proper group permissions for `/www/storage/logs` so that both `www-data` (nginx/php-fpm) and `tradeadmin` (CLI/artisan) can read and write logs.

---

## Problem

Laravel logs need to be accessible by:
- **www-data**: Nginx and PHP-FPM (web requests)
- **tradeadmin**: CLI commands (artisan, cron jobs, manual scripts)

Without proper group permissions, you get "Permission denied" errors.

---

## Solution

### 1. Add tradeadmin to www-data Group

```bash
sudo usermod -a -G www-data tradeadmin
```

**Verify**:
```bash
groups tradeadmin
# Output: tradeadmin : tradeadmin sudo www-data users
```

### 2. Set Proper Ownership

```bash
sudo chown -R www-data:www-data /www/storage/logs
```

**What this does**:
- Owner: `www-data` (primary user)
- Group: `www-data` (shared group)
- Both www-data and tradeadmin can access

### 3. Set Group Permissions

```bash
sudo chmod -R 775 /www/storage/logs
```

**Permission breakdown**:
- `7` (owner): read + write + execute
- `7` (group): read + write + execute
- `5` (others): read + execute

### 4. Set SGID Bit

```bash
sudo chmod g+s /www/storage/logs
```

**What this does**:
- New files inherit the group (`www-data`)
- Ensures all new log files are group-writable
- Prevents permission issues on new files

---

## Result

### Directory Permissions
```bash
drwxrwsr-x+ 2 www-data www-data 4096 Nov 12 15:13 /www/storage/logs/
```

**Breakdown**:
- `d`: Directory
- `rwx`: Owner (www-data) can read, write, execute
- `rws`: Group (www-data) can read, write, execute + SGID
- `r-x`: Others can read and execute
- `+`: Has ACL (Access Control List)

### File Permissions
```bash
-rwxrwxr-x+ 1 www-data www-data 78 Nov 12 15:13 laravel.log
```

**Breakdown**:
- `rwx`: Owner can read, write, execute
- `rwx`: Group can read, write, execute
- `r-x`: Others can read and execute

---

## Who Can Access What

| User | Group | Can Read | Can Write | Use Case |
|------|-------|----------|-----------|----------|
| **www-data** | www-data | ✅ | ✅ | Nginx, PHP-FPM web requests |
| **tradeadmin** | www-data | ✅ | ✅ | CLI, artisan, cron, manual |
| **root** | - | ✅ | ✅ | System administration |
| **others** | - | ✅ | ❌ | Read-only (security) |

---

## Benefits

✅ **No permission errors** - Both users can write  
✅ **Automatic inheritance** - New files get correct permissions  
✅ **Secure** - Others can't write  
✅ **Standard practice** - Follows Laravel best practices  
✅ **Works with cron** - Scheduled tasks can log  
✅ **Works with web** - Web requests can log  

---

## Testing

### Test as tradeadmin (CLI)
```bash
cd /www && php artisan tinker --execute="Log::error('Test from CLI'); echo 'OK';"
tail -1 /www/storage/logs/laravel.log
```

### Test as www-data (web)
Visit any page that logs errors - it should work without permission issues.

### Test new file creation
```bash
touch /www/storage/logs/test.log
ls -l /www/storage/logs/test.log
# Should show: -rw-rw-r-- 1 tradeadmin www-data
```

The file automatically gets `www-data` group due to SGID bit!

---

## Apply to Other Storage Directories

The same permissions should be applied to all storage directories:

```bash
# Apply to entire storage directory
sudo chown -R www-data:www-data /www/storage
sudo chmod -R 775 /www/storage
sudo find /www/storage -type d -exec chmod g+s {} \;
```

**Directories to ensure**:
- `/www/storage/logs` - Log files
- `/www/storage/framework/cache` - Cache files
- `/www/storage/framework/sessions` - Session files
- `/www/storage/framework/views` - Compiled views
- `/www/storage/app` - Uploaded files

---

## Troubleshooting

### Permission Denied Error

**Check group membership**:
```bash
groups tradeadmin
# Should include: www-data
```

**If not in group**:
```bash
sudo usermod -a -G www-data tradeadmin
# Log out and back in for changes to take effect
```

### New Files Wrong Permissions

**Check SGID bit**:
```bash
ls -ld /www/storage/logs
# Should show: drwxrwsr-x (note the 's' in group permissions)
```

**If missing**:
```bash
sudo chmod g+s /www/storage/logs
```

### Files Created with Wrong Group

**Re-apply permissions**:
```bash
sudo chown -R www-data:www-data /www/storage/logs
sudo chmod -R 775 /www/storage/logs
sudo chmod g+s /www/storage/logs
```

---

## Security Considerations

### Why 775 and not 777?

**775 (recommended)**:
- Owner and group can read/write/execute
- Others can only read/execute
- Secure - prevents unauthorized writes

**777 (NOT recommended)**:
- Everyone can read/write/execute
- Security risk - any user can modify logs
- Can be exploited

### Why SGID?

**With SGID (`g+s`)**:
- New files inherit parent directory's group
- Consistent permissions automatically
- No manual fixes needed

**Without SGID**:
- New files get creator's primary group
- Inconsistent permissions
- Manual fixes required

---

## Automation

### Set Permissions on Deployment

Add to deployment script:
```bash
#!/bin/bash
# Fix storage permissions
sudo chown -R www-data:www-data /www/storage
sudo chmod -R 775 /www/storage
sudo find /www/storage -type d -exec chmod g+s {} \;
```

### Check Permissions Script

```bash
#!/bin/bash
# Check if permissions are correct
if [ "$(stat -c %a /www/storage/logs)" != "2775" ]; then
    echo "WARNING: /www/storage/logs permissions incorrect"
    echo "Run: sudo chmod 2775 /www/storage/logs"
fi
```

---

## Laravel Best Practices

### After Deployment
```bash
# Standard Laravel permissions
cd /www
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
sudo find storage -type d -exec chmod g+s {} \;
```

### After Composer Install
```bash
# Ensure vendor permissions
sudo chown -R www-data:www-data vendor
sudo chmod -R 755 vendor
```

---

## Summary

✅ **Group configured** - tradeadmin in www-data group  
✅ **Ownership set** - www-data:www-data  
✅ **Permissions set** - 775 (rwxrwxr-x)  
✅ **SGID enabled** - New files inherit group  
✅ **Tested** - Both users can write  
✅ **Secure** - Others can't write  

**No more permission errors!** 🎉

---

## Quick Reference

```bash
# Add user to www-data group
sudo usermod -a -G www-data USERNAME

# Fix storage permissions
sudo chown -R www-data:www-data /www/storage
sudo chmod -R 775 /www/storage
sudo find /www/storage -type d -exec chmod g+s {} \;

# Verify
ls -la /www/storage/logs/
groups USERNAME
```

---

**Status**: ✅ Configured and working  
**Security**: ✅ Secure (775, not 777)  
**Compatibility**: ✅ Works with nginx, php-fpm, CLI, cron


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

