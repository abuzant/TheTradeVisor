<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('history_upload_progress', function (Blueprint $table) {
            $table->date('last_day_uploaded')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('history_upload_progress', function (Blueprint $table) {
            $table->date('last_day_uploaded')->nullable(false)->change();
        });
    }
};
