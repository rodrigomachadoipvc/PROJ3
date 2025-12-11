<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Renomear a coluna 'nome' para 'name'
            $table->renameColumn('nome', 'name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Reverter caso seja preciso fazer rollback
            $table->renameColumn('name', 'nome');
        });
    }
};
