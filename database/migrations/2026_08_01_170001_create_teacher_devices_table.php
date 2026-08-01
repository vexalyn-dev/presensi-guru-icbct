<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('teacher_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('device_name');
            $table->string('device_token');
            $table->string('user_agent')->nullable();
            $table->string('os')->nullable();
            $table->string('browser')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'device_token']);
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void { Schema::dropIfExists('teacher_devices'); }
};
