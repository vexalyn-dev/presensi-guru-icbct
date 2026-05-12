<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('cascade');
            $table->string('semester')->default('Ganjil');
            $table->string('academic_year')->default('2024/2025');
            $table->integer('hours_per_week')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(
                ['user_id', 'subject_id', 'class_id', 'academic_year'],
                'ts_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_subjects');
    }
};