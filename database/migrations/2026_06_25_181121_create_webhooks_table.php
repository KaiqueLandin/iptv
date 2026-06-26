<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('event'); // invoice.paid, payment.completed, order.activated, etc
            $table->string('url');
            $table->string('secret')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('headers')->nullable();
            $table->integer('timeout')->default(30); // seconds
            $table->integer('max_retries')->default(3);
            $table->timestamps();

            $table->index('event');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};
