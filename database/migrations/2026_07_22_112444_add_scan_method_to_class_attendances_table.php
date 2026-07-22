<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('class_attendances', function (Blueprint $table) {
            // Tambah scan_method jika belum ada
            if (!Schema::hasColumn('class_attendances', 'scan_method')) {
                $table->string('scan_method', 50)->nullable()->after('status');
            }
            // Pastikan notes juga ada (di migration awal sudah ada, tapi jaga-jaga)
            if (!Schema::hasColumn('class_attendances', 'notes')) {
                $table->text('notes')->nullable()->after('scan_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('class_attendances', function (Blueprint $table) {
            $table->dropColumn(['scan_method']);
        });
    }
};
