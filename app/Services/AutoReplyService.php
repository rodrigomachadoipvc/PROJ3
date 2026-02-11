<?php

namespace App\Services;

use App\Events\NovaMensagemEnviada;
use App\Models\AutoReply;
use App\Models\Mensagem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AutoReplyService
{
    public function handle(Mensagem $mensagem): ?Mensagem
    {
        if ($mensagem->emissor?->is_system) {
            return null;
        }

        $texto = mb_strtolower(trim($mensagem->conteudo ?? ''));
        if ($texto === '') {
            return null;
        }

        $resposta = $this->matchRules($texto);
        if (! $resposta) {
            return null;
        }

        $systemUser = User::where('is_system', true)->first();
        if (! $systemUser) {
            return null;
        }

        $mensagemSistema = $mensagem->conversa->mensagens()->create([
            'emissor_id' => $systemUser->id,
            'conteudo' => $resposta,
        ]);

        DB::afterCommit(function () use ($mensagemSistema) {
            broadcast(new NovaMensagemEnviada($mensagemSistema));
        });

        return $mensagemSistema;
    }

    protected function matchRules(string $texto): ?string
    {
        $rules = AutoReply::where('active', true)
            ->orderBy('priority')
            ->get();

        foreach ($rules as $rule) {
            if (str_contains($texto, mb_strtolower($rule->trigger))) {
                return $rule->response;
            }
        }

        return null;
    }
}
