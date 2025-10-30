<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adicionar a FK de utilizadores AQUI, depois de 'tipos_utilizadores' existir
            $table->foreign('tipo_utilizador_id')->references('id')->on('tipos_utilizadores')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tipo_utilizador_id']);
        });
    }
};