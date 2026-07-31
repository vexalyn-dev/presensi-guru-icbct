<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type'); // scan_in, scan_out, login, settings_change, dll
            $table->string('category')->index(); // attendance, auth, settings, teacher, system
            $table->text('description');
            $table->string('subject_type')->nullable(); // morphs
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('properties')->nullable(); // IP, device, location, dll
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Index untuk performa
            $table->index(['type', 'created_at']);
            $table->index(['category', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};