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
         Schema::table('attendances', function (Blueprint $table) {
            // add separate GPS columns for check-in and check-out
            if (!Schema::hasColumn('attendances', 'check_in_latitude')) {
                $table->decimal('check_in_latitude', 10, 7)->nullable()->after('check_in');
            }
            if (!Schema::hasColumn('attendances', 'check_in_longitude')) {
                $table->decimal('check_in_longitude', 10, 7)->nullable()->after('check_in_latitude');
            }
            if (!Schema::hasColumn('attendances', 'check_out_latitude')) {
                $table->decimal('check_out_latitude', 10, 7)->nullable()->after('check_out');
            }
            if (!Schema::hasColumn('attendances', 'check_out_longitude')) {
                $table->decimal('check_out_longitude', 10, 7)->nullable()->after('check_out_latitude');
            }
        });
    }

    /**
     * Reverse the migrations.                  
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'check_in_latitude')) {
                $table->dropColumn('check_in_latitude');
            }
            if (Schema::hasColumn('attendances', 'check_in_longitude')) {
                $table->dropColumn('check_in_longitude');
            }
            if (Schema::hasColumn('attendances', 'check_out_latitude')) {
                $table->dropColumn('check_out_latitude');
            }
            if (Schema::hasColumn('attendances', 'check_out_longitude')) {
                $table->dropColumn('check_out_longitude');
            }
        });
    }
};
