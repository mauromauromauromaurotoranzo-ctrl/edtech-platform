<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('knowledge_base_id')->constrained('knowledge_bases')->onDelete('cascade');
            $table->enum('status', ['pending', 'active', 'cancelled', 'expired'])->default('pending');
            $table->integer('amount'); // stored in cents
            $table->string('currency', 3)->default('USD');
            $table->string('interval')->default('month');
            $table->timestamp('current_period_starts_at');
            $table->timestamp('current_period_ends_at');
            $table->string('payment_provider_subscription_id')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index('student_id');
            $table->index('knowledge_base_id');
            $table->index('status');
            $table->index('current_period_ends_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
