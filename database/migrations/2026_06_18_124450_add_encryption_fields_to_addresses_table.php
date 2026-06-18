<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Street hash + encrypted
            $table->string('street_hash', 64)->nullable()->after('street');
            $table->text('street_encrypted')->nullable()->after('street_hash');

            // Neighborhood hash + encrypted
            $table->string('neighborhood_hash', 64)->nullable()->after('neighborhood');
            $table->text('neighborhood_encrypted')->nullable()->after('neighborhood_hash');

            // City hash + encrypted
            $table->string('city_hash', 64)->nullable()->after('city');
            $table->text('city_encrypted')->nullable()->after('city_hash');

            // Zip code hash + encrypted
            $table->string('zip_code_hash', 64)->nullable()->after('zip_code');
            $table->text('zip_code_encrypted')->nullable()->after('zip_code_hash');
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn([
                'street_hash',
                'street_encrypted',
                'neighborhood_hash',
                'neighborhood_encrypted',
                'city_hash',
                'city_encrypted',
                'zip_code_hash',
                'zip_code_encrypted',
            ]);
        });
    }
};