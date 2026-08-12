<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('developer_updates', function (Blueprint $table) {
            $table->id();
            $table->string('version', 20);
            $table->string('title', 200);
            $table->text('content'); // markdown / html
            $table->string('type', 20)->default('feature'); // feature, fix, update, hotfix
            $table->boolean('show_modal')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developer_updates');
    }
};
