<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('knowledge_base_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['quiz', 'puzzle', 'scenario', 'flashcard', 'code', 'matching']);
            $table->string('title');
            $table->text('content');
            $table->string('correct_answer')->nullable();
            $table->json('options')->nullable();
            $table->text('explanation')->nullable();
            $table->integer('points')->default(10);
            $table->timestamp('scheduled_for');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->text('student_answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->integer('points_earned')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['student_id', 'scheduled_for']);
            $table->index(['student_id', 'answered_at']);
            $table->index(['knowledge_base_id', 'type']);
            $table->index('scheduled_for');
            $table->index(['sent_at', 'scheduled_for']); // For finding pending challenges
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_challenges');
    }
};
