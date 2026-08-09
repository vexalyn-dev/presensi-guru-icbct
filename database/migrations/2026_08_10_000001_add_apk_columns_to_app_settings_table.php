<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('app_settings', 'apk_file')) {
                $table->string('apk_file')->nullable()->after('maintenance_message');
            }
            if (!Schema::hasColumn('app_settings', 'apk_name')) {
                $table->string('apk_name')->nullable()->after('apk_file');
            }
            if (!Schema::hasColumn('app_settings', 'apk_version')) {
                $table->string('apk_version')->nullable()->after('apk_name');
            }
            if (!Schema::hasColumn('app_settings', 'apk_min_android')) {
                $table->string('apk_min_android')->nullable()->after('apk_version');
            }
            if (!Schema::hasColumn('app_settings', 'apk_size')) {
                $table->unsignedBigInteger('apk_size')->nullable()->after('apk_min_android');
            }
            if (!Schema::hasColumn('app_settings', 'apk_uploaded_at')) {
                $table->timestamp('apk_uploaded_at')->nullable()->after('apk_size');
            }
            if (!Schema::hasColumn('app_settings', 'apk_changelog')) {
                $table->string('apk_changelog', 1000)->nullable()->after('apk_uploaded_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $cols = ['apk_file','apk_name','apk_version','apk_min_android','apk_size','apk_uploaded_at','apk_changelog'];
            $existing = array_filter($cols, fn($c) => Schema::hasColumn('app_settings', $c));
            if (!empty($existing)) {
                $table->dropColumn(array_values($existing));
            }
        });
    }
};
