<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
    {
         public function up(): void
    {
        // Adicionar FKs à tabela tipo_utilizador_permissoes
        Schema::table('tipo_utilizador_permissoes', function (Blueprint $table) {
            // Referencia a tabela 'tipos_utilizadores' (já corrigida no modelo)
            $table->foreign('tipo_utilizador_id')->references('id')->on('tipos_utilizadores')->onDelete('cascade');
            
            // Referencia a tabela 'permissoes'
            $table->foreign('permissao_id')->references('id')->on('permissoes')->onDelete('cascade');
        });

        // Adicionar FKs à tabela utilizador_grupo
        Schema::table('utilizador_grupo', function (Blueprint $table) {
            $table->foreign('utilizador_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('cascade');
        });
    }

        public function down(): void
        {
            Schema::table('tipo_utilizador_permissoes', function (Blueprint $table) {
                $table->dropForeign(['tipo_utilizador_id', 'permissao_id']);
            });

            Schema::table('utilizador_grupo', function (Blueprint $table) {
                $table->dropForeign(['utilizador_id', 'grupo_id']);
            });
        }
    };
