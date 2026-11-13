<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambah kolom room_id ke tabel teaching_schedules.
 * room_id merujuk ke classrooms.type = 'shared' (Lab, Aula, Ruang Bersama).
 * Berbeda dari classroom_id yang merujuk ke kelas reguler.
 * Nullable — jadwal mengajar di kelas reguler tidak wajib punya ruangan khusus.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teaching_schedules', function (Blueprint $table) {
            $table->foreignId('room_id')
                ->nullable()
                ->after('classroom_id')
                ->constrained('classrooms')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('teaching_schedules', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
        });
    }
};
