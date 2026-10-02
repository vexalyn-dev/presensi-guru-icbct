<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('app_settings', 'ios_file')) {
                $table->string('ios_file')->nullable()->after('apk_changelog');
            }
            if (!Schema::hasColumn('app_settings', 'ios_name')) {
                $table->string('ios_name')->nullable()->after('ios_file');
            }
            if (!Schema::hasColumn('app_settings', 'ios_version')) {
                $table->string('ios_version')->nullable()->after('ios_name');
            }
            if (!Schema::hasColumn('app_settings', 'ios_min_version')) {
                $table->string('ios_min_version')->nullable()->after('ios_version');
            }
            if (!Schema::hasColumn('app_settings', 'ios_size')) {
                $table->unsignedBigInteger('ios_size')->nullable()->after('ios_min_version');
            }
            if (!Schema::hasColumn('app_settings', 'ios_uploaded_at')) {
                $table->timestamp('ios_uploaded_at')->nullable()->after('ios_size');
            }
            if (!Schema::hasColumn('app_settings', 'ios_changelog')) {
                $table->string('ios_changelog', 1000)->nullable()->after('ios_uploaded_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $cols = ['ios_file', 'ios_name', 'ios_version', 'ios_min_version', 'ios_size', 'ios_uploaded_at', 'ios_changelog'];
            $existing = array_filter($cols, fn($c) => Schema::hasColumn('app_settings', $c));
            if (!empty($existing)) {
                $table->dropColumn(array_values($existing));
            }
        });
    }
};
