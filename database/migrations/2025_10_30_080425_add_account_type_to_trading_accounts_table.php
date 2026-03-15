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
            $table->string('account_type', 20)->default('real')->after('broker_server');
            // Types: real (0), demo (1), contest (2)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trading_accounts', function (Blueprint $table) {
            //
            $table->dropColumn('account_type');
        });
    }
};
