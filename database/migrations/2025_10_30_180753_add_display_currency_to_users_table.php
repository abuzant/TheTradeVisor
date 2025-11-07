<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('users', 'display_currency')) {
                $table->string('display_currency', 3)->default('USD')->after('max_accounts');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'display_currency')) {
                $table->dropColumn('display_currency');
            }
        });
    }
};
