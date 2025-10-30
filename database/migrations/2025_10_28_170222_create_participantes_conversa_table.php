<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('participantes_conversa', function (Blueprint $table) {
            $table->foreignId('conversa_id')->constrained('conversas')->onDelete('cascade');
            $table->foreignId('utilizador_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('entrou_em')->useCurrent();
            $table->timestamp('saiu_em')->nullable(); // Para histórico de entrada/saída
            $table->primary(['conversa_id', 'utilizador_id']); // Chave composta
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participantes_conversa');
    }
};
