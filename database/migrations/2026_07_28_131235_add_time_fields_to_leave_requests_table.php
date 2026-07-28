<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            // Jam mulai izin (misal: 08:00)
            $table->time('start_time')->nullable()->after('start_date');
            // Jam selesai izin (misal: 12:00)
            $table->time('end_time')->nullable()->after('end_date');
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });
    }
};
