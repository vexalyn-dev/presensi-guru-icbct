<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('date');
            $table->index('status');
            $table->index(['user_id', 'date']);
        });

        Schema::table('class_attendances', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('date');
            $table->index('classroom_id');
            $table->index(['user_id', 'date']);
            $table->index(['classroom_id', 'date']);
        });

        Schema::table('teacher_schedules', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('day_of_week');
            $table->index(['user_id', 'day_of_week']);
        });

        Schema::table('teaching_schedules', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('day_of_week');
            $table->index(['user_id', 'day_of_week']);
        });

        Schema::table('scan_logs', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('scanned_at');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id', 'date']);
        });

        Schema::table('class_attendances', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['date']);
            $table->dropIndex(['classroom_id']);
            $table->dropIndex(['user_id', 'date']);
            $table->dropIndex(['classroom_id', 'date']);
        });

        Schema::table('teacher_schedules', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['day_of_week']);
            $table->dropIndex(['user_id', 'day_of_week']);
        });

        Schema::table('teaching_schedules', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['day_of_week']);
            $table->dropIndex(['user_id', 'day_of_week']);
        });

        Schema::table('scan_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['scanned_at']);
        });
    }
};
