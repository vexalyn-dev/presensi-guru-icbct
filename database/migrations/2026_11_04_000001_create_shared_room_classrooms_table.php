<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('shared_room_classrooms')) {
            return;
        }

        Schema::create('shared_room_classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shared_classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['shared_classroom_id', 'classroom_id'], 'shared_room_class_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shared_room_classrooms');
    }
};
