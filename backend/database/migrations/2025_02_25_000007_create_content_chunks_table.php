<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_chunks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('knowledge_base_id')->constrained('knowledge_bases')->onDelete('cascade');
            $table->text('content');
            $table->json('metadata')->nullable(); // source_type, page_num, section, etc.
            $table->timestamps();

            $table->index('knowledge_base_id');
        });

        // Add vector extension for pgvector
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');
        
        // Add embedding column as vector(1536) for OpenAI embeddings
        DB::statement('ALTER TABLE content_chunks ADD COLUMN embedding vector(1536)');
        
        // Create index for similarity search
        DB::statement('CREATE INDEX ON content_chunks USING ivfflat (embedding vector_cosine_ops)');
    }

    public function down(): void
    {
        Schema::dropIfExists('content_chunks');
    }
};
