<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utilizador_grupo', function (Blueprint $table) {
            $table->unsignedBigInteger('utilizador_id');
            $table->unsignedBigInteger('grupo_id');
            $table->string('funcao_no_grupo', 50)->default('Membro');
            $table->primary(['utilizador_id', 'grupo_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utilizador_grupo');
    }
};
