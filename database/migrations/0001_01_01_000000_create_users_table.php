<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Login
            $table->string('username')->unique();
            $table->string('password');

            // Nomes (first_name e last_name criptografados, display_name em texto puro)
            $table->string('first_name_hash', 64)->nullable();
            $table->text('first_name_encrypted')->nullable();
            $table->string('last_name_hash', 64)->nullable();
            $table->text('last_name_encrypted')->nullable();
            $table->string('display_name');

            // Email (hash para busca, encrypted para exibição/disparo)
            $table->string('email_hash', 64)->unique();
            $table->text('email_encrypted');

            // Controle de acesso
            $table->integer('access_level')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('last_login_ip', 45)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};