<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, clean up any duplicate accounts
        // Keep the oldest account (lowest ID) for each user+broker+account combination
        DB::statement("
            DELETE FROM trading_accounts
            WHERE id IN (
                SELECT id FROM (
                    SELECT id,
                           ROW_NUMBER() OVER (
                               PARTITION BY user_id, broker_server, COALESCE(account_number, account_hash)
                               ORDER BY id ASC
                           ) AS rn
                    FROM trading_accounts
                ) t
                WHERE t.rn > 1
            )
        ");

        // Add unique constraint for non-anonymized accounts (account_number is present)
        // This prevents duplicate accounts with same user_id + broker_server + account_number
        DB::statement("
            CREATE UNIQUE INDEX unique_user_broker_account 
            ON trading_accounts (user_id, broker_server, account_number)
            WHERE account_number IS NOT NULL
        ");

        // Add unique constraint for anonymized accounts (account_hash is present)
        // This prevents duplicate accounts with same user_id + broker_server + account_hash
        DB::statement("
            CREATE UNIQUE INDEX unique_user_broker_hash 
            ON trading_accounts (user_id, broker_server, account_hash)
            WHERE account_hash IS NOT NULL AND account_hash != ''
        ");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS unique_user_broker_account");
        DB::statement("DROP INDEX IF EXISTS unique_user_broker_hash");
    }
};
