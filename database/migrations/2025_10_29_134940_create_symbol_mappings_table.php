<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up(): void
{
    Schema::create('symbol_mappings', function (Blueprint $table) {
        $table->id();
        $table->string('raw_symbol', 50)->unique();
        $table->string('normalized_symbol', 20); // Remove ->index() from here
        $table->string('broker_suffix')->nullable();
        $table->boolean('is_verified')->default(false);
        $table->timestamps();
        
        // Only define the index once here
        $table->index('normalized_symbol');
    });
}

    public function down(): void
    {
        Schema::dropIfExists('symbol_mappings');
    }
};
