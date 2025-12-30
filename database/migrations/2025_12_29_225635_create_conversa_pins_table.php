<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conversa_pins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conversa_id')->constrained('conversas')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'conversa_id']);
            $table->index(['user_id', 'conversa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversa_pins');
    }
};
