<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->string('domain_name')->unique();
            $table->string('registrar')->nullable(); // RegistroBR, GoDaddy, Namecheap, etc
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled', 'transferred'])->default('pending');
            $table->date('registration_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->json('nameservers')->nullable();
            $table->json('dns_records')->nullable();
            $table->boolean('privacy_protection')->default(false);
            $table->json('whois_data')->nullable();
            $table->timestamps();

            $table->index('domain_name');
            $table->index(['user_id', 'status']);
            $table->index('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
