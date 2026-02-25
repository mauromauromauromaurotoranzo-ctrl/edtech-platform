<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_voices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
            $table->string('voice_id'); // ElevenLabs voice ID
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('settings');
            $table->string('sample_url')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index('instructor_id');
            $table->index('voice_id');
            $table->unique(['instructor_id', 'is_default'])->where('is_default', true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_voices');
    }
};
