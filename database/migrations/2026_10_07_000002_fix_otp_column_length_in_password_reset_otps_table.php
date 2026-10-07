<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Perbesar kolom otp dari varchar(6) menjadi varchar(255)
     * agar bisa menampung bcrypt hash (~60 karakter).
     */
    public function up(): void
    {
        Schema::table('password_reset_otps', function (Blueprint $table) {
            $table->string('otp')->change(); // default 255
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_otps', function (Blueprint $table) {
            $table->string('otp', 6)->change();
        });
    }
};
