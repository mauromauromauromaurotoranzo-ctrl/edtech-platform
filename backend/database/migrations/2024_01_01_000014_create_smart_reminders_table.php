<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smart_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('knowledge_base_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['spaced_repetition', 'inactivity', 'exam_prep', 'streak_maintenance', 'custom', 'content_suggestion']);
            $table->string('title');
            $table->text('message');
            $table->timestamp('scheduled_at');
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern')->nullable(); // daily, weekly, monthly
            $table->float('priority')->default(1.0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('sent_at')->nullable();
            $table->integer('send_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['student_id', 'is_active']);
            $table->index(['scheduled_at', 'is_active']); // For finding due reminders
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_reminders');
    }
};
