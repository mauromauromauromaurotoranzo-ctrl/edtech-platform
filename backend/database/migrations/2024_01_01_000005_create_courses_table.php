<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_base_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');
            $table->boolean('self_paced')->default(true);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->json('modules');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();
            
            $table->index('knowledge_base_id');
            $table->index('status');
            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
