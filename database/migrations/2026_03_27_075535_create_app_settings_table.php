<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->default('ICB CT - Absensi Guru');
            $table->string('app_logo')->nullable();
            $table->string('app_favicon')->nullable();
            $table->string('app_timezone')->default('Asia/Jakarta');
            $table->string('app_language')->default('id');
            
            // Attendance Settings
            $table->time('attendance_start_time')->default('07:30');
            $table->time('attendance_end_time')->default('08:00');
            $table->integer('attendance_late_grace_period')->default(15); // minutes
            $table->boolean('location_required')->default(true);
            $table->boolean('photo_required')->default(true);
            $table->decimal('location_latitude', 10, 8)->nullable(); // Office location
            $table->decimal('location_longitude', 11, 8)->nullable();
            $table->integer('location_radius')->default(100); // meters
            
            // Notification Settings
            $table->boolean('email_notification')->default(true);
            $table->boolean('late_notification')->default(true);
            $table->string('admin_email')->nullable();
            
            // Appearance
            $table->string('primary_color')->default('#0F172A');
            $table->string('accent_color')->default('#FACC15');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};