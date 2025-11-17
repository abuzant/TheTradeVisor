<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digest_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('trading_account_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('frequency'); // daily, weekly
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'trading_account_id', 'frequency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digest_subscriptions');
    }
};
