<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL ENUM tidak bisa diubah via Blueprint change() dengan mudah
        // Gunakan raw SQL untuk modify enum
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin','guru','operator','guru_piket') NOT NULL DEFAULT 'guru'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin','guru') NOT NULL DEFAULT 'guru'");
    }
};
