<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // X IPA 1, XI IPS 2, dll
            $table->string('grade'); // X, XI, XII
            $table->string('major')->nullable(); // IPA, IPS, Bahasa, dll
            $table->integer('year')->default(2024); // Tahun ajaran
            $table->integer('capacity')->default(36);
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null'); // Wali kelas
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};