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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "X-A", "XI-IPA-1"
            $table->string('code')->unique(); // "XA-001"
            $table->string('building')->nullable(); // "Gedung A"
            $table->integer('floor')->nullable(); // Lantai 2
            $table->string('qr_token')->unique(); // Token untuk QR
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
