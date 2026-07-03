<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('attendances', 'check_out')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->time('check_out')->nullable()->after('check_in');
            });
        }

        if (!Schema::hasColumn('attendances', 'check_out_status')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->string('check_out_status', 50)->nullable()->after('check_out');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('attendances', 'check_out_status')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('check_out_status');
            });
        }

        if (Schema::hasColumn('attendances', 'check_out')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('check_out');
            });
        }
    }
};