<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_otps', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('otp');             // bcrypt hash dari 6-digit OTP
            $table->timestamp('expires_at');  // berlaku 10 menit
            $table->boolean('used')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_otps');
    }
};
