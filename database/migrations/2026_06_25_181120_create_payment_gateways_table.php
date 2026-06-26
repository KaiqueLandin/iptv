<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Stripe, PayPal, MercadoPago, PagSeguro, etc
            $table->string('gateway_key')->unique(); // stripe, paypal, mercadopago
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_sandbox')->default(true);
            $table->json('credentials')->nullable(); // API keys, secrets, etc (encrypted)
            $table->json('settings')->nullable(); // Additional settings
            $table->decimal('fee_fixed', 10, 2)->default(0);
            $table->decimal('fee_percentage', 5, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('gateway_key');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
