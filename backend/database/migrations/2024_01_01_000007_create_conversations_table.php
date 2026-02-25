<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('knowledge_base_id')->constrained()->onDelete('cascade');
            $table->json('messages');
            $table->json('chunks_referenced')->nullable();
            $table->float('engagement_score')->nullable();
            $table->timestamps();
            
            $table->index('student_id');
            $table->index('knowledge_base_id');
            $table->index(['student_id', 'knowledge_base_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
