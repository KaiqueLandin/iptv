<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('credit_wallets')->onDelete('cascade');
            $table->enum('type', ['credit', 'debit', 'refund', 'adjustment']);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->unsignedBigInteger('whmcs_order_id')->nullable();
            $table->unsignedBigInteger('whmcs_service_id')->nullable();
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('wallet_id');
            $table->index('whmcs_order_id');
            $table->index('whmcs_service_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_transactions');
    }
};
