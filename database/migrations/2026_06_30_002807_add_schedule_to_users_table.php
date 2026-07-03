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
    Schema::table('users', function (Blueprint $table) {
        // Tambah kolom jam masuk dan jam pulang
        $table->time('start_time')->nullable()->after('role');
        $table->time('end_time')->nullable()->after('start_time');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['start_time', 'end_time']);
    });
}
};
