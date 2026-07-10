<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDefaultScheduleToUsersTable extends Migration
{
    public function up(): void
    {
        $hasCheckIn = count(DB::select("SHOW COLUMNS FROM users LIKE 'default_check_in'")) > 0;
        $hasCheckOut = count(DB::select("SHOW COLUMNS FROM users LIKE 'default_check_out'")) > 0;

        if (! $hasCheckIn || ! $hasCheckOut) {
            Schema::table('users', function (Blueprint $table) use ($hasCheckIn, $hasCheckOut): void {
                if (! $hasCheckIn) {
                    $table->time('default_check_in')->nullable()->after('password');
                }

                if (! $hasCheckOut) {
                    $table->time('default_check_out')->nullable()->after('default_check_in');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (count(DB::select("SHOW COLUMNS FROM users LIKE 'default_check_in'")) > 0) {
                $table->dropColumn('default_check_in');
            }

            if (count(DB::select("SHOW COLUMNS FROM users LIKE 'default_check_out'")) > 0) {
                $table->dropColumn('default_check_out');
            }
        });
    }
}