<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin','guru','operator','guru_piket','developer') NOT NULL DEFAULT 'guru'");
        }

        $devEmail = env('DEV_EMAIL');
        if ($devEmail) {
            DB::table('users')
                ->where('email', $devEmail)
                ->update(['role' => 'developer']);
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin','guru','operator','guru_piket') NOT NULL DEFAULT 'guru'");
        }
    }
};
