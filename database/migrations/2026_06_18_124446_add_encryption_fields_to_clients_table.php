<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Document hash + encrypted (CPF/CNPJ)
            $table->string('document_hash', 64)->nullable()->unique()->after('document_number');
            $table->text('document_encrypted')->nullable()->after('document_hash');

            // Phone hash + encrypted
            $table->string('phone1_hash', 64)->nullable()->after('phone1');
            $table->text('phone1_encrypted')->nullable()->after('phone1_hash');
            $table->string('phone2_hash', 64)->nullable()->after('phone2');
            $table->text('phone2_encrypted')->nullable()->after('phone2_hash');

            // Name fields (first_name + last_name + display_name)
            $table->string('first_name')->nullable()->after('user_id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('display_name')->nullable()->after('last_name');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'document_hash',
                'document_encrypted',
                'phone1_hash',
                'phone1_encrypted',
                'phone2_hash',
                'phone2_encrypted',
                'first_name',
                'last_name',
                'display_name',
            ]);
        });
    }
};