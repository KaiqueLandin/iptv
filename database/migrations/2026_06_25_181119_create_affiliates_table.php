<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('referral_code')->unique();
            $table->decimal('commission_rate', 5, 2)->default(10.00); // percentage
            $table->decimal('total_earned', 10, 2)->default(0);
            $table->decimal('pending_balance', 10, 2)->default(0);
            $table->decimal('paid_balance', 10, 2)->default(0);
            $table->integer('total_referrals')->default(0);
            $table->string('payment_method')->nullable();
            $table->json('payment_details')->nullable(); // PIX, bank account, etc
            $table->boolean('is_active')->default(true);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('referral_code');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
