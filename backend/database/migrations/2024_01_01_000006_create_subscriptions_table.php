<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('knowledge_base_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'cancelled', 'expired'])->default('active');
            $table->timestamp('current_period_starts_at');
            $table->timestamp('current_period_ends_at');
            $table->json('payment_provider_data')->nullable();
            $table->timestamps();
            
            $table->index('student_id');
            $table->index('knowledge_base_id');
            $table->index('course_id');
            $table->index('status');
            $table->index('current_period_ends_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
