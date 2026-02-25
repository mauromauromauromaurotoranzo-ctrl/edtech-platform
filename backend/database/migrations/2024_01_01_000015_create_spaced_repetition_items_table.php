<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spaced_repetition_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('content_chunk_id')->constrained()->onDelete('cascade');
            $table->float('easiness_factor')->default(2.5);
            $table->integer('repetition_count')->default(0);
            $table->integer('interval_days')->default(0);
            $table->timestamp('next_review_at')->nullable();
            $table->timestamp('last_reviewed_at')->nullable();
            $table->json('review_history')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'content_chunk_id']);
            $table->index(['student_id', 'next_review_at']); // For finding due items
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spaced_repetition_items');
    }
};
