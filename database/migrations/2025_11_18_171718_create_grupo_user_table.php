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
    Schema::create('grupo_user', function (Blueprint $table) {
        $table->id();
        // Chaves Estrangeiras
        $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

        // Campos de controlo (opcional, mas útil)
        $table->boolean('is_admin')->default(false); // Se é admin do grupo
        $table->timestamps();

        // Garantir que não duplicamos (o mesmo user no mesmo grupo 2 vezes)
        $table->unique(['grupo_id', 'user_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupo_user');
    }
};
