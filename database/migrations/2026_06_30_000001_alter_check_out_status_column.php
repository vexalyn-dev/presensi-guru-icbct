<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function shouldAlterColumn(): bool
    {
        return Schema::hasColumn('attendances', 'check_out_status')
            && DB::getDriverName() !== 'sqlite';
    }

    public function up(): void
    {
        if ($this->shouldAlterColumn()) {
            DB::statement('ALTER TABLE `attendances` MODIFY COLUMN `check_out_status` VARCHAR(50) NULL');
        }
    }

    public function down(): void
    {
        if ($this->shouldAlterColumn()) {
            DB::statement("ALTER TABLE `attendances` MODIFY COLUMN `check_out_status` ENUM('on_time', 'late', 'early') NULL");
        }
    }
};
