<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ticket_id')->unique()->nullable(); // ID dari Vexalyn
            $table->enum('type', ['bug', 'feature', 'maintenance', 'question'])->default('bug');
            $table->string('title');
            $table->text('description');
            $table->string('category')->nullable();   // UI, Login, Presensi, dll
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['new', 'review', 'in_progress', 'testing', 'completed', 'rejected', 'on_hold'])->default('new');
            $table->json('metadata')->nullable();     // browser, os, device, url, dll
            $table->json('attachments')->nullable();  // array of file URLs
            $table->json('extra_fields')->nullable(); // steps_to_reproduce, expected, actual, dll
            $table->timestamp('vexalyn_sent_at')->nullable();
            $table->text('vexalyn_response')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
