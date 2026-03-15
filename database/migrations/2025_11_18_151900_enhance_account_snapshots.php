<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add user_id column
        Schema::table('account_snapshots', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
        });

        // Backfill user_id from trading_accounts
        DB::statement('
            UPDATE account_snapshots 
            SET user_id = (
                SELECT user_id 
                FROM trading_accounts 
                WHERE trading_accounts.id = account_snapshots.trading_account_id
            )
            WHERE user_id IS NULL
        ');

        // Add foreign key and indexes
        Schema::table('account_snapshots', function (Blueprint $table) {
            // Make user_id NOT NULL after backfill
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            
            // Add foreign key
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            // Add indexes for performance
            $table->index(['user_id', 'snapshot_time'], 'idx_user_time');
            $table->index(['trading_account_id', 'snapshot_time'], 'idx_account_time');
            $table->index('snapshot_time', 'idx_snapshot_time');
            $table->index(['user_id', 'created_at'], 'idx_user_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_snapshots', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('idx_user_time');
            $table->dropIndex('idx_account_time');
            $table->dropIndex('idx_snapshot_time');
            $table->dropIndex('idx_user_created');
            
            // Drop foreign key
            $table->dropForeign(['user_id']);
            
            // Drop column
            $table->dropColumn('user_id');
        });
    }
};
