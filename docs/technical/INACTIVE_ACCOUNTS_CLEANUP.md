# Inactive Accounts Cleanup System

## Overview

Automated system to delete trading accounts and all their associated data after 180 days of inactivity. This ensures data hygiene, optimal database performance, and compliance with the data retention policy stated on the pricing page.

## How It Works

### Inactivity Detection

An account is considered inactive when:
1. **`last_sync_at`** is older than 180 days (no data received from MT4/MT5)
2. **OR** `last_sync_at` is NULL (account created but never synced)
3. **AND** `created_at` is older than 180 days (to protect newly created accounts)

### What Gets Deleted

When an inactive account is deleted, the following data is removed:

1. **Trading Account** - The account record itself
2. **Positions** - All open and closed positions
3. **Deals** - All deal history
4. **Orders** - All order history
5. **History Upload Progress** - Upload tracking data
6. **Related Files** - Any associated filesystem data

All deletions are performed within database transactions to ensure data integrity.

## Command Usage

### Manual Execution

#### Dry Run (Preview Only)
```bash
php artisan accounts:cleanup-inactive --dry-run
```

Shows which accounts would be deleted without actually deleting them. Safe to run anytime.

#### Actual Deletion (Default 180 Days)
```bash
php artisan accounts:cleanup-inactive
```

Deletes accounts inactive for more than 180 days. Requires confirmation.

#### Custom Inactivity Period
```bash
php artisan accounts:cleanup-inactive --days=90
```

Deletes accounts inactive for more than 90 days (or any custom number).

#### Dry Run with Custom Period
```bash
php artisan accounts:cleanup-inactive --days=90 --dry-run
```

Preview what would be deleted with a custom inactivity period.

### Command Options

| Option | Description | Default |
|--------|-------------|---------|
| `--days=N` | Number of days of inactivity before deletion | 180 |
| `--dry-run` | Preview mode - no actual deletion | false |

## Automated Schedule

The cleanup runs automatically every day at **3:00 AM** server time.

### Schedule Configuration

Located in `/www/routes/console.php`:

```php
Schedule::command('accounts:cleanup-inactive')
    ->dailyAt('03:00')
    ->name('cleanup-inactive-accounts')
    ->withoutOverlapping()
    ->onFailure(function () {
        \Log::error('Inactive accounts cleanup failed');
    })
    ->onSuccess(function () {
        \Log::info('Inactive accounts cleanup completed successfully');
    });
```

### Cron Setup

Ensure Laravel's scheduler is running via cron:

```bash
* * * * * cd /www && php artisan schedule:run >> /dev/null 2>&1
```

This should already be configured on the production server.

## Logging

### Success Logs

Each deleted account is logged with full details:

```
[INFO] Inactive account deleted
- account_id: 123
- user_email: user@example.com
- account_number: 12345678
- broker: Broker Name
- last_sync: 2025-05-15 10:30:00
- positions_deleted: 45
- deals_deleted: 120
- orders_deleted: 15
- days_inactive: 180
```

### Error Logs

Failed deletions are logged with error details:

```
[ERROR] Failed to delete inactive account
- account_id: 123
- error: Database connection timeout
- trace: [full stack trace]
```

### Schedule Logs

Daily schedule execution is logged:

```
[INFO] Inactive accounts cleanup completed successfully
[ERROR] Inactive accounts cleanup failed
```

## Safety Features

### 1. Transaction Protection
All deletions happen within database transactions. If any part fails, the entire operation rolls back.

### 2. Confirmation Required
Manual execution requires explicit confirmation before deletion.

### 3. Dry Run Mode
Preview mode allows safe testing without data loss.

### 4. Detailed Logging
Every deletion is logged for audit purposes.

### 5. No Overlap Protection
`withoutOverlapping()` prevents multiple cleanup jobs from running simultaneously.

### 6. Creation Date Check
Newly created accounts (< 180 days old) are never deleted, even if `last_sync_at` is NULL.

## Command Output

### Dry Run Example

```
🔍 Searching for accounts inactive for more than 180 days...
Found 3 inactive accounts:

+----+------------------+-------------+----------+---------------------+------------+
| ID | User             | Broker      | Account  | Last Sync           | Created    |
+----+------------------+-------------+----------+---------------------+------------+
| 45 | user@example.com | FXCM        | 12345678 | 2025-01-15 10:30:00 | 2024-12-01 |
| 67 | test@test.com    | IC Markets  | 87654321 | Never               | 2024-11-20 |
| 89 | old@account.com  | Pepperstone | 11223344 | 2025-02-10 14:20:00 | 2024-10-15 |
+----+------------------+-------------+----------+---------------------+------------+

🔸 DRY RUN - No data will be deleted
Run without --dry-run to actually delete these accounts
```

### Actual Deletion Example

```
🔍 Searching for accounts inactive for more than 180 days...
Found 3 inactive accounts:

[Table showing accounts...]

Do you want to delete these accounts and all their data? (yes/no) [no]:
> yes

✅ Deleted account #45 (user@example.com - 12345678)
✅ Deleted account #67 (test@test.com - 87654321)
✅ Deleted account #89 (old@account.com - 11223344)

📊 Summary:
   Total found: 3
   Successfully deleted: 3

✅ Cleanup completed
```

### Error Example

```
🔍 Searching for accounts inactive for more than 180 days...
Found 2 inactive accounts:

[Table showing accounts...]

Do you want to delete these accounts and all their data? (yes/no) [no]:
> yes

✅ Deleted account #45 (user@example.com - 12345678)
❌ Failed to delete account #67: Foreign key constraint violation

📊 Summary:
   Total found: 2
   Successfully deleted: 1
   Failed: 1

Errors:
   - Failed to delete account #67: Foreign key constraint violation

✅ Cleanup completed
```

## Database Schema

The `trading_accounts` table includes these relevant fields:

```sql
last_sync_at TIMESTAMP NULL     -- Last time data was received from MT4/MT5
created_at   TIMESTAMP NOT NULL -- Account creation date
is_active    BOOLEAN DEFAULT TRUE
is_paused    BOOLEAN DEFAULT FALSE
```

## Monitoring

### Check Last Cleanup

```bash
tail -f /www/storage/logs/laravel.log | grep "Inactive accounts cleanup"
```

### View Deleted Accounts

```bash
grep "Inactive account deleted" /www/storage/logs/laravel.log
```

### Check Schedule Status

```bash
php artisan schedule:list
```

Should show:
```
0 3 * * *  cleanup-inactive-accounts  php artisan accounts:cleanup-inactive
```

## Testing

### 1. Test Dry Run
```bash
php artisan accounts:cleanup-inactive --dry-run
```

### 2. Test with Short Period (Preview)
```bash
php artisan accounts:cleanup-inactive --days=1 --dry-run
```

### 3. Check Logs
```bash
tail -100 /www/storage/logs/laravel.log
```

### 4. Verify Schedule
```bash
php artisan schedule:test
```

## Performance Considerations

- **Batch Processing**: Accounts are processed one at a time with transactions
- **No Timeout**: Command has no timeout, suitable for large datasets
- **Off-Peak Hours**: Scheduled at 3:00 AM to minimize impact
- **Indexed Fields**: `last_sync_at` and `created_at` are indexed for fast queries
- **Cascade Deletes**: Foreign key cascades handle related data efficiently

## User Communication

Users are informed about the 180-day policy on the pricing page:

> **What happens to inactive accounts?**
> 
> Account data is automatically deleted after 180 days of inactivity for all account types. This helps us maintain optimal performance and security for active users.

## Compliance

This system ensures:
- ✅ Data retention policy compliance
- ✅ GDPR right to erasure (automated)
- ✅ Database optimization
- ✅ Storage cost reduction
- ✅ Security (old unused data removed)

## Troubleshooting

### Command Not Found
```bash
php artisan list | grep cleanup
```

If not listed, clear cache:
```bash
php artisan config:clear
php artisan cache:clear
```

### Schedule Not Running

Check cron:
```bash
crontab -l | grep schedule
```

Should show:
```
* * * * * cd /www && php artisan schedule:run >> /dev/null 2>&1
```

### No Accounts Deleted

This is normal if all accounts are active. Test with shorter period:
```bash
php artisan accounts:cleanup-inactive --days=30 --dry-run
```

## Files Created/Modified

### New Files
- `/www/app/Console/Commands/CleanupInactiveAccounts.php` - Command implementation

### Modified Files
- `/www/routes/console.php` - Added scheduled task
- `/www/resources/views/public/pricing.blade.php` - Added policy to FAQ

## Future Enhancements

Potential improvements:
- [ ] Email notification to users before deletion (7-day warning)
- [ ] Option to export data before deletion
- [ ] Admin dashboard to view pending deletions
- [ ] Configurable retention period per subscription tier
- [ ] Archive instead of delete (move to cold storage)

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
