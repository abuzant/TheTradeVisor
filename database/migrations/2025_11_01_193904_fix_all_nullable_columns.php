
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Fix deals table
        Schema::table('deals', function (Blueprint $table) {
            $table->string('symbol', 20)->nullable()->change();
        });
        
        // Fix history_upload_progress table
        Schema::table('history_upload_progress', function (Blueprint $table) {
            $table->date('last_day_uploaded')->nullable()->change();
        });
    }
    
    public function down(): void {
        Schema::table('deals', function (Blueprint $table) {
            $table->string('symbol', 20)->nullable(false)->change();
        });
        
        Schema::table('history_upload_progress', function (Blueprint $table) {
            $table->date('last_day_uploaded')->nullable(false)->change();
        });
    }
};
