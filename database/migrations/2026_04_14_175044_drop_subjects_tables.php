<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Drop foreign keys first
        Schema::table('teacher_subjects', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['class_id']);
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['class_id']);
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
        });

        // Drop tables
        Schema::dropIfExists('teacher_subjects');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('subjects');
    }

    public function down(): void
    {
        // Rollback if needed
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('category')->default('Umum');
            $table->integer('credits')->default(2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ... recreate other tables if needed
    }
};