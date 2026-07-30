<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('class_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('class_attendances', 'is_short_class')) {
                $table->boolean('is_short_class')->default(false)->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('class_attendances', function (Blueprint $table) {
            if (Schema::hasColumn('class_attendances', 'is_short_class')) {
                $table->dropColumn('is_short_class');
            }
        });
    }
};
