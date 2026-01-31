<?php

namespace App\Observers;

use App\Models\Mensagem;

class MensagemObserver
{
    public function created(Mensagem $mensagem): void
    {
        $conversa = $mensagem->conversa;
        if (! $conversa) {
            return;
        }

        // Simplificado: a nova mensagem é sempre a última (sequencial, mesmo com AutoReply)
        $conversa->updateQuietly(['ultima_mensagem_id' => $mensagem->id]);
    }
}