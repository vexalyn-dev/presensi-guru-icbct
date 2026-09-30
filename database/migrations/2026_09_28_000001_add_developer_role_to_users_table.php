<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin','guru','operator','guru_piket','developer') NOT NULL DEFAULT 'guru'");

        // Update developer account role
        $devEmail = env('DEV_EMAIL', 'dev@vexalyndev.my.id');
        DB::table('users')
            ->where('email', $devEmail)
            ->update(['role' => 'developer']);
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin','guru','operator','guru_piket') NOT NULL DEFAULT 'guru'");
    }
};
