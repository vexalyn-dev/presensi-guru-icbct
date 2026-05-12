<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'qr_code')) {
                $table->string('qr_code')->nullable()->after('photo');
            }
            if (!Schema::hasColumn('users', 'qr_token')) {
                $table->string('qr_token')->unique()->nullable()->after('qr_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['qr_code', 'qr_token']);
        });
    }
};