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
        Schema::create('mensagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversa_id')->constrained('conversas')->onDelete('cascade');
            $table->foreignId('emissor_id')->constrained('users')->onDelete('cascade'); // Quem enviou
            $table->text('conteudo')->nullable(); // Texto da mensagem
            $table->foreignId('resposta_a_id')->nullable()->constrained('mensagens')->nullOnDelete(); // Para respostas diretas
            $table->boolean('vista')->default(false); // Para recibos de leitura
            // $table->foreignId('tipo_ml_id')->nullable(); // Para futura integração ML
            $table->timestamps();
            $table->softDeletes(); // Permitir "apagar para mim"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensagems');
    }
};
