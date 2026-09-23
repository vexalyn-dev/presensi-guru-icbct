<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subject_teacher', function (Blueprint $table) {
            $table->integer('hours_per_week')->default(2)->after('teacher_id');
            $table->boolean('is_active')->default(true)->after('hours_per_week');
        });
    }

    public function down(): void
    {
        Schema::table('subject_teacher', function (Blueprint $table) {
            $table->dropColumn(['hours_per_week', 'is_active']);
        });
    }
};
