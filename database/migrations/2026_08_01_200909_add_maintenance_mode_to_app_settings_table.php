<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->boolean('maintenance_mode')->default(false)->after('accent_color');
            $table->text('maintenance_message')->nullable()->after('maintenance_mode');
        });
    }
    public function down(): void {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['maintenance_mode', 'maintenance_message']);
        });
    }
};
