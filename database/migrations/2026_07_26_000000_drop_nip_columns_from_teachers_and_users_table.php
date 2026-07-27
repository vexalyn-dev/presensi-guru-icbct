<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            if (Schema::hasColumn('teachers', 'nip')) {
                $table->dropUnique(['nip']);
                $table->dropColumn('nip');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nip')) {
                $table->dropUnique(['nip']);
                $table->dropColumn('nip');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            if (!Schema::hasColumn('teachers', 'nip')) {
                $table->string('nip')->unique()->nullable()->after('user_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nip')) {
                $table->string('nip', 18)->nullable()->unique()->after('email');
            }
        });
    }
};
