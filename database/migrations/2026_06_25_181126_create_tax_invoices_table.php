<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique(); // NF number
            $table->string('nfe_key')->nullable(); // Chave NFe
            $table->string('nfe_series')->nullable();
            $table->string('nfe_protocol')->nullable();
            $table->text('nfe_xml')->nullable();
            $table->string('pdf_url')->nullable();
            $table->enum('status', ['pending', 'issued', 'cancelled', 'error'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('invoice_id');
            $table->index('invoice_number');
            $table->index('nfe_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_invoices');
    }
};
