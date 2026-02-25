<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_base_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->json('embedding_vector')->nullable();
            $table->string('source_type')->default('text');
            $table->integer('page_num')->nullable();
            $table->string('section')->nullable();
            $table->text('context_window')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('knowledge_base_id');
            $table->index('source_type');
            
            // For pgvector extension (if using PostgreSQL with pgvector)
            // $table->vector('embedding_vector', 1536)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_chunks');
    }
};
