<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_utilizador_permissoes', function (Blueprint $table) {
            // Apenas definimos as colunas, sem foreign keys (FK)
            $table->unsignedBigInteger('tipo_utilizador_id');
            $table->unsignedBigInteger('permissao_id');
            $table->primary(['tipo_utilizador_id', 'permissao_id']);
        });

        // Adicionamos as FKs numa migração separada (ou aqui, mas é mais seguro ter FKs após todas as tabelas criadas)
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_utilizador_permissoes');
    }
};
