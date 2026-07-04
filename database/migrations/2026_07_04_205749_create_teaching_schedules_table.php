<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teaching_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Guru
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade'); // Kelas
            $table->foreignId('subject_id')->nullable()->constrained()->onDelete('set null'); // Mapel
            $table->tinyInteger('day_of_week'); // 0=Minggu ... 6=Sabtu
            $table->integer('period'); // Jam pelajaran ke-berapa (1, 2, 3, ...)
            $table->time('start_time');
            $table->time('end_time');
            $table->string('academic_year')->nullable(); // "2026/2027"
            $table->string('semester')->nullable(); // "Ganjil" / "Genap"
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'classroom_id', 'day_of_week', 'period'], 'teaching_scheds_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_schedules');
    }
};
