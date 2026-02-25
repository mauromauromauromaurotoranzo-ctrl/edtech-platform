<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('public_access')->default(false);
            $table->string('pricing_model')->nullable();
            $table->integer('total_chunks')->default(0);
            $table->timestamp('last_indexed_at')->nullable();
            $table->timestamps();
            
            $table->index('instructor_id');
            $table->index('status');
            $table->index('slug');
            $table->index('public_access');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_bases');
    }
};
