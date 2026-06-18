<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // Login via email_hash
            $table->string('password');

            // Nomes (first_name e last_name criptografados)
            $table->string('first_name_hash', 64)->nullable();
            $table->text('first_name_encrypted')->nullable();
            $table->string('last_name_hash', 64)->nullable();
            $table->text('last_name_encrypted')->nullable();
            $table->string('display_name')->nullable();

            // Email (hash para busca/login, encrypted para exibição/disparo)
            $table->string('email_hash', 64)->nullable()->unique();
            $table->text('email_encrypted')->nullable();

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            $table->string('name')->nullable();
            $table->string('email')->nullable();

            $table->enum('document_type', ['CPF', 'CNPJ']);
            $table->string('document_number');
            $table->string('document_hash', 64)->nullable()->unique();
            $table->text('document_encrypted')->nullable();

            $table->string('state_registration')->nullable();
            $table->string('municipal_registration')->nullable();
            $table->tinyInteger('contributor_type')->nullable();

            $table->string('phone1', 20);
            $table->string('phone1_hash', 64)->nullable();
            $table->text('phone1_encrypted')->nullable();
            $table->string('contact1');

            $table->string('phone2', 20)->nullable();
            $table->string('phone2_hash', 64)->nullable();
            $table->text('phone2_encrypted')->nullable();
            $table->string('contact2')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};