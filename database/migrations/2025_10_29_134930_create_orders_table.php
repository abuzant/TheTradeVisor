<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trading_account_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('ticket')->index();
            $table->string('symbol', 20)->index();
            $table->string('comment')->nullable();
            $table->string('external_id')->nullable();
            $table->string('type', 30); // buy_limit, sell_limit, buy_stop, etc.
            $table->string('state', 30)->nullable(); // placed, filled, canceled, etc.
            $table->enum('reason', ['client', 'mobile', 'web', 'expert', 'sl', 'tp', 'so', 'unknown'])->default('unknown');
            $table->decimal('volume_initial', 15, 2);
            $table->decimal('volume_current', 15, 2);
            $table->decimal('price_open', 15, 5);
            $table->decimal('price_current', 15, 5)->nullable();
            $table->decimal('price_stoplimit', 15, 5)->nullable();
            $table->decimal('sl', 15, 5)->nullable();
            $table->decimal('tp', 15, 5)->nullable();
            $table->timestamp('time_setup')->index();
            $table->bigInteger('time_setup_msc')->nullable();
            $table->timestamp('time_done')->nullable();
            $table->bigInteger('time_done_msc')->nullable();
            $table->timestamp('expiration')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->unsignedBigInteger('position_by_id')->nullable();
            $table->bigInteger('magic')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            
            $table->index(['trading_account_id', 'is_active']);
            $table->index(['symbol', 'type']);
            $table->index(['trading_account_id', 'ticket']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
