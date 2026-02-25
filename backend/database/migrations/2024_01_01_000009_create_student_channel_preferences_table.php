<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_channel_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->json('priority_order');
            $table->string('whatsapp_number')->nullable();
            $table->string('telegram_chat_id')->nullable();
            $table->string('email_address')->nullable();
            $table->boolean('notifications_enabled')->default(true);
            $table->timestamps();
            
            $table->unique('student_id');
            $table->index('whatsapp_number');
            $table->index('telegram_chat_id');
            $table->index('email_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_channel_preferences');
    }
};
