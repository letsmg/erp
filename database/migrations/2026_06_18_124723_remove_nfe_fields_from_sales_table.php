<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'cUF',
                'natOp',
                'mod',
                'serie',
                'nNF',
                'dhEmi',
                'tpNF',
                'idDest',
                'chNFe',
                'vBC',
                'vICMS',
                'vIPI',
                'vPIS',
                'vCOFINS',
                'vFrete',
                'vSeg',
                'vDesc',
                'vNF',
                'modFrete',
                'tPag',
                'vPag',
                'infCpl',
                'nProt',
                'digVal',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->char('cUF', 2)->nullable();
            $table->string('natOp', 60)->nullable();
            $table->char('mod', 2)->default('55');
            $table->string('serie', 3)->default('001');
            $table->integer('nNF')->nullable();
            $table->timestamp('dhEmi')->nullable();
            $table->char('tpNF', 1)->default('1');
            $table->char('idDest', 1)->nullable();
            $table->string('chNFe', 44)->nullable();
            $table->decimal('vBC', 15, 2)->default(0);
            $table->decimal('vICMS', 15, 2)->default(0);
            $table->decimal('vIPI', 15, 2)->default(0);
            $table->decimal('vPIS', 15, 2)->default(0);
            $table->decimal('vCOFINS', 15, 2)->default(0);
            $table->decimal('vFrete', 15, 2)->default(0);
            $table->decimal('vSeg', 15, 2)->default(0);
            $table->decimal('vDesc', 15, 2)->default(0);
            $table->decimal('vNF', 15, 2)->default(0);
            $table->char('modFrete', 1)->default('0');
            $table->char('tPag', 2)->default('01');
            $table->decimal('vPag', 15, 2)->default(0);
            $table->text('infCpl')->nullable();
            $table->string('nProt', 15)->nullable();
            $table->string('digVal', 28)->nullable();
        });
    }
};