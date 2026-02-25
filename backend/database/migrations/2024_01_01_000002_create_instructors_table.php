<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('expertise_areas')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->string('stripe_account_id')->nullable();
            $table->timestamps();
            
            $table->index('verification_status');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructors');
    }
};
