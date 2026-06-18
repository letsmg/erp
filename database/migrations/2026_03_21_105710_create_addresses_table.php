<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');

            $table->string('zip_code', 10);
            $table->string('zip_code_hash', 64)->nullable();
            $table->text('zip_code_encrypted')->nullable();

            $table->string('street');
            $table->string('street_hash', 64)->nullable();
            $table->text('street_encrypted')->nullable();

            $table->string('number', 10);
            $table->string('neighborhood');
            $table->string('neighborhood_hash', 64)->nullable();
            $table->text('neighborhood_encrypted')->nullable();

            $table->string('city');
            $table->string('city_hash', 64)->nullable();
            $table->text('city_encrypted')->nullable();

            $table->char('state', 2);
            $table->string('complement')->nullable();
            $table->boolean('is_delivery_address')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};