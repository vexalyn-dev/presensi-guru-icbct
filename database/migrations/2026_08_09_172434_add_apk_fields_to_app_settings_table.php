<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->string('apk_file')->nullable()->after('maintenance_message');
            $table->string('apk_name')->nullable()->after('apk_file');
            $table->string('apk_version')->nullable()->after('apk_name');
            $table->string('apk_min_android')->nullable()->after('apk_version');
            $table->unsignedBigInteger('apk_size')->nullable()->after('apk_min_android'); // bytes
            $table->timestamp('apk_uploaded_at')->nullable()->after('apk_size');
            $table->string('apk_changelog')->nullable()->after('apk_uploaded_at');
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn([
                'apk_file', 'apk_name', 'apk_version',
                'apk_min_android', 'apk_size', 'apk_uploaded_at', 'apk_changelog',
            ]);
        });
    }
};
