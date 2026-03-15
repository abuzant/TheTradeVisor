<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->timestamp('open_time')->nullable()->change();
        });
        
        Schema::table('deals', function (Blueprint $table) {
            $table->timestamp('time')->nullable()->change();
        });
        
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('time_setup')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->timestamp('open_time')->nullable(false)->change();
        });
        
        Schema::table('deals', function (Blueprint $table) {
            $table->timestamp('time')->nullable(false)->change();
        });
        
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('time_setup')->nullable(false)->change();
        });
    }
};
