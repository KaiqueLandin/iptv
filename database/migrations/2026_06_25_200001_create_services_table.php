<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');

            // Provisioning lifecycle (mirrors WHMCS tblhosting).
            $table->string('status')->default('pending'); // pending, active, suspended, terminated, failed
            $table->string('provider')->nullable(); // which provisioning driver created it (xtream, simulated, ...)

            // The delivered "code"/credentials for the IPTV line.
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('activation_code')->nullable();
            $table->text('access_url')->nullable();      // M3U / portal URL
            $table->json('provision_data')->nullable();  // raw response/extra fields from the panel

            $table->date('starts_at')->nullable();
            $table->date('expires_at')->nullable();

            // Delivery tracking (email/whatsapp).
            $table->timestamp('delivered_at')->nullable();
            $table->json('delivery_channels')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('service_number');
            $table->index(['user_id', 'status']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
