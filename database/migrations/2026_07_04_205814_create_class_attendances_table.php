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
        Schema::create('class_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->foreignId('teaching_schedule_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date');
            $table->integer('period'); // Jam pelajaran ke-berapa
            $table->time('check_in_time')->nullable(); // Masuk kelas
            $table->time('check_out_time')->nullable(); // Keluar kelas
            $table->enum('status', ['Hadir', 'Terlambat', 'Tidak Mengajar'])->default('Hadir');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'classroom_id', 'date', 'period'], 'class_att_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_attendances');
    }
};
