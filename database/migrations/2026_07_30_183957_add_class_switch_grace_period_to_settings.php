<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tambah ke app_settings jika kolom belum ada
        if (Schema::hasTable('app_settings') && !Schema::hasColumn('app_settings', 'class_switch_grace_period')) {
            Schema::table('app_settings', function (Blueprint $table) {
                $table->integer('class_switch_grace_period')->default(5)->after('attendance_late_grace_period');
            });
        }

        // Tambah ke settings key-value table jika belum ada
        if (Schema::hasTable('settings')) {
            \App\Models\Setting::set('class_switch_grace_period', 5, 'number', 'attendance');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('app_settings') && Schema::hasColumn('app_settings', 'class_switch_grace_period')) {
            Schema::table('app_settings', function (Blueprint $table) {
                $table->dropColumn('class_switch_grace_period');
            });
        }
    }
};
