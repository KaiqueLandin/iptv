<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('server_modules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // cPanel, Plesk, DirectAdmin, Custom
            $table->string('module_key')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(false);
            $table->string('server_hostname')->nullable();
            $table->integer('server_port')->nullable();
            $table->boolean('use_ssl')->default(true);
            $table->json('credentials')->nullable(); // API keys, username, password (encrypted)
            $table->json('settings')->nullable();
            $table->string('provision_script')->nullable(); // Path to custom provisioning script
            $table->timestamps();

            $table->index('module_key');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_modules');
    }
};
