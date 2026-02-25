<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('knowledge_base_id')->constrained()->onDelete('cascade');
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->integer('total_points')->default(0);
            $table->integer('challenges_completed')->default(0);
            $table->integer('challenges_correct')->default(0);
            $table->timestamp('last_challenge_date')->nullable();
            $table->json('achievements')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'knowledge_base_id']);
            $table->index(['knowledge_base_id', 'total_points']); // For leaderboard
            $table->index(['student_id', 'current_streak']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_progress');
    }
};
