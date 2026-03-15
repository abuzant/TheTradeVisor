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
        Schema::create('enterprise_api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enterprise_broker_id')->constrained()->onDelete('cascade');
            $table->string('key', 255)->unique();
            $table->string('name', 255); // e.g., "Production API", "Dev API"
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->index('enterprise_broker_id');
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enterprise_api_keys');
    }
};
