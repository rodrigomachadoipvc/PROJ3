<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill: preenche ultima_mensagem_id apenas onde está NULL e há mensagens.
        DB::statement(<<<SQL
UPDATE conversas c
SET c.ultima_mensagem_id = (
    SELECT m.id
    FROM mensagens m
    WHERE m.conversa_id = c.id
    ORDER BY m.created_at DESC, m.id DESC
    LIMIT 1
)
WHERE c.ultima_mensagem_id IS NULL
  AND EXISTS (
    SELECT 1 FROM mensagens m2 WHERE m2.conversa_id = c.id
  )
SQL);
    }

    public function down(): void
    {
        // Não desfazemos automaticamente: é um backfill (irreversível de forma segura).
        // Se quiseres reverter, faz manualmente com critério (não recomendo).
    }
};
