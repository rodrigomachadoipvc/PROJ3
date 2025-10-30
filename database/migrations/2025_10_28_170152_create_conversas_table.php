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
        Schema::create('conversas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['dm', 'grupo'])->default('dm'); // dm = Direct Message
            $table->foreignId('grupo_id')->nullable()->constrained('grupos')->onDelete('cascade'); // Liga a grupos se tipo='grupo'
            $table->string('nome')->nullable(); // Nome opcional (útil para grupos ou DMs específicas)
            $table->foreignId('ultima_mensagem_id')->nullable()->index();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversas');
    }
};
