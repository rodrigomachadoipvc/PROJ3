<?php

namespace App\Policies;

use App\Models\Anexo;
use App\Models\User;

class AnexoPolicy
{
    /**
     * Só participantes da conversa podem ver/descarregar anexos.
     */
    public function view(User $user, Anexo $anexo): bool
    {
        // Defensive: se faltar relação, nega
        if (!$anexo->mensagem) {
            return false;
        }

        // Assumindo que Mensagem tem conversa() (normal no teu modelo)
        $conversa = $anexo->mensagem->conversa ?? null;
        if (!$conversa) {
            return false;
        }

        // participantes() deve devolver users participantes (pivot participantes_conversa)
        return $conversa->participantes()
            ->where('users.id', $user->id)
            ->exists();
    }
}
