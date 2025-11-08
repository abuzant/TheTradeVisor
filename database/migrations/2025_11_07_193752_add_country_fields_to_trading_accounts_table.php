<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trading_accounts', function (Blueprint $table) {
            $table->string('country_code', 2)->nullable()->after('account_type');
            $table->string('country_name', 100)->nullable()->after('country_code');
            $table->string('last_ip', 45)->nullable()->after('country_name');
            $table->timestamp('last_seen_at')->nullable()->after('last_ip');
            
            $table->index('country_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trading_accounts', function (Blueprint $table) {
            $table->dropIndex(['country_code']);
            $table->dropColumn(['country_code', 'country_name', 'last_ip', 'last_seen_at']);
        });
    }
};
