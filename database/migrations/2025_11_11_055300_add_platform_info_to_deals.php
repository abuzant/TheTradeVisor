<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds platform information to deals table for better categorization
     * and filtering of deal types.
     */
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            // Platform type for this deal (MT4/MT5)
            $table->string('platform_type', 10)->nullable()->after('magic');
            
            // Add index for filtering
            $table->index('platform_type');
            $table->index(['platform_type', 'entry']);
        });
        
        // Note: deal_category column already exists from previous migration
        // We'll use it to differentiate between 'trade', 'balance', 'credit', etc.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropIndex(['deals_platform_type_index']);
            $table->dropIndex(['deals_platform_type_entry_index']);
            $table->dropColumn('platform_type');
        });
    }
};
