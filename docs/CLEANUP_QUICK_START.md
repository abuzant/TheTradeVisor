# Inactive Accounts Cleanup - Quick Start

## What It Does

Automatically deletes trading accounts and all their data after **180 days of inactivity**.

## Status

✅ **ACTIVE** - Runs daily at 3:00 AM automatically

## Quick Commands

### Preview What Would Be Deleted (Safe)
```bash
php artisan accounts:cleanup-inactive --dry-run
```

### Actually Delete Inactive Accounts
```bash
php artisan accounts:cleanup-inactive
```
*Requires confirmation*

### Custom Period (e.g., 90 days)
```bash
php artisan accounts:cleanup-inactive --days=90
```

## What Gets Deleted

- Trading account record
- All positions (open & closed)
- All deals
- All orders
- History upload progress
- Any associated files

## Inactivity Criteria

Account is deleted if:
- `last_sync_at` > 180 days old (no data from MT4/MT5)
- OR `last_sync_at` is NULL (never synced)
- AND `created_at` > 180 days old (protects new accounts)

## Schedule

```
Daily at 3:00 AM
Next run: Check with: php artisan schedule:list
```

## Logs

### View Recent Deletions
```bash
tail -100 /www/storage/logs/laravel.log | grep "Inactive account deleted"
```

### View Cleanup Status
```bash
tail -100 /www/storage/logs/laravel.log | grep "cleanup"
```

## Safety Features

✅ Transaction-protected (rollback on error)  
✅ Confirmation required for manual runs  
✅ Dry-run mode available  
✅ Full logging of all deletions  
✅ No overlap protection  
✅ New accounts protected  

## User Communication

Users are informed on the pricing page FAQ:

> **What happens to inactive accounts?**
> 
> Account data is automatically deleted after 180 days of inactivity for all account types.

## Monitoring

Check if schedule is running:
```bash
php artisan schedule:list
```

Should show:
```
0 3 * * * php artisan accounts:cleanup-inactive . Next Due: X hours from now
```

## Testing

1. **Dry run** (safe):
   ```bash
   php artisan accounts:cleanup-inactive --dry-run
   ```

2. **Short period test** (preview):
   ```bash
   php artisan accounts:cleanup-inactive --days=30 --dry-run
   ```

3. **Check logs**:
   ```bash
   tail -f /www/storage/logs/laravel.log
   ```

## Files

- **Command**: `/www/app/Console/Commands/CleanupInactiveAccounts.php`
- **Schedule**: `/www/routes/console.php`
- **Docs**: `/www/docs/INACTIVE_ACCOUNTS_CLEANUP.md`

## Support

For detailed documentation, see: `/www/docs/INACTIVE_ACCOUNTS_CLEANUP.md`

---

**Status**: ✅ Deployed and Active  
**Last Updated**: November 13, 2025  
**Next Scheduled Run**: Check with `php artisan schedule:list`
