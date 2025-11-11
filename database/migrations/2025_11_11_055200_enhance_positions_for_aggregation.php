<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Enhances positions table to support MT5 position aggregation
     * and proper tracking of position lifecycle.
     */
    public function up(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            // MT5 position identifier (different from ticket)
            $table->string('position_identifier', 50)->nullable()->after('identifier');
            
            // Entry type for the position
            $table->string('entry_type', 20)->nullable()->after('position_identifier');
            
            // Close time for closed positions
            $table->timestamp('close_time')->nullable()->after('update_time');
            
            // Close price for closed positions
            $table->decimal('close_price', 15, 5)->nullable()->after('close_time');
            
            // Total volume that entered the position
            $table->decimal('total_volume_in', 15, 2)->default(0)->after('volume');
            
            // Total volume that exited the position
            $table->decimal('total_volume_out', 15, 2)->default(0)->after('total_volume_in');
            
            // Number of deals that make up this position
            $table->integer('deal_count')->default(0)->after('total_volume_out');
            
            // Platform type for this position (MT4/MT5)
            $table->string('platform_type', 10)->nullable()->after('deal_count');
            
            // Add indexes for performance
            $table->index('position_identifier');
            $table->index(['trading_account_id', 'position_identifier']);
            $table->index(['platform_type', 'is_open']);
            $table->index('close_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropIndex(['positions_position_identifier_index']);
            $table->dropIndex(['positions_trading_account_id_position_identifier_index']);
            $table->dropIndex(['positions_platform_type_is_open_index']);
            $table->dropIndex(['positions_close_time_index']);
            
            $table->dropColumn([
                'position_identifier',
                'entry_type',
                'close_time',
                'close_price',
                'total_volume_in',
                'total_volume_out',
                'deal_count',
                'platform_type'
            ]);
        });
    }
};
