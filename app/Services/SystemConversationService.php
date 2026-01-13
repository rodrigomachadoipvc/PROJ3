<?php

namespace App\Services;

use App\Models\Conversa;
use App\Models\Mensagem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SystemConversationService
{
    public const SYSTEM_NAME  = 'Sistema';
    public const SYSTEM_EMAIL = 'sistema@communico.pt';

    public const WELCOME_MESSAGE = "Olá! Sou o Sistema do Communico.\n\nPodes pedir ajuda com comandos.\nEscreve: @sistema lista de ajudas";

    /**
     * Garante que o utilizador tem uma DM com o Sistema e uma mensagem inicial.
     * É idempotente (não duplica conversas nem a mensagem inicial).
     */
    public function ensureForUser(User $user): ?Conversa
    {
        if ($user->is_system) {
            return null;
        }

        $systemUser = $this->getOrCreateSystemUser();

        return DB::transaction(function () use ($user, $systemUser) {

            // 1) Encontrar DM existente (exactamente 2 participantes) entre user e sistema
            $conversa = $user->conversasParticipadas()
                ->where('tipo', 'dm')
                ->whereHas('participantes', function ($q) use ($systemUser) {
                    $q->where('users.id', $systemUser->id);
                }, '>=', 1)
                ->whereHas('participantes', null, '=', 2)
                ->first();

            // 2) Se não existir, cria
            if (! $conversa) {
                $conversa = Conversa::create(['tipo' => 'dm']);

                $conversa->participantes()->attach([
                    $user->id => [
                        'entrou_em' => now(),
                        'saiu_em'   => null,
                    ],
                    $systemUser->id => [
                        'entrou_em' => now(),
                        'saiu_em'   => null,
                    ],
                ]);
            } else {
                // 3) Reativar pivots se alguém "saiu"
                $this->ensurePivotAtivo($conversa, $user->id);
                $this->ensurePivotAtivo($conversa, $systemUser->id);
            }

            // 4) Mensagem inicial (apenas se não houver mensagens)
            $temMensagens = Mensagem::where('conversa_id', $conversa->id)->exists();

            if (! $temMensagens) {
                $msg = $conversa->mensagens()->create([
                    'emissor_id' => $systemUser->id,
                    'conteudo'   => self::WELCOME_MESSAGE,
                ]);

                $conversa->update([
                    'ultima_mensagem_id' => $msg->id,
                ]);
            } else {
                // Se por algum motivo não existir ultima_mensagem_id, corrige
                if (empty($conversa->ultima_mensagem_id)) {
                    $ultima = Mensagem::where('conversa_id', $conversa->id)
                        ->orderByDesc('created_at')
                        ->first();

                    if ($ultima) {
                        $conversa->update(['ultima_mensagem_id' => $ultima->id]);
                    }
                }
            }

            return $conversa;
        });
    }

    private function ensurePivotAtivo(Conversa $conversa, int $userId): void
    {
        $pivotRow = $conversa->participantes()->where('utilizador_id', $userId)->first();

        if ($pivotRow) {
            $updates = [];

            if ($pivotRow->pivot->saiu_em !== null) {
                $updates['saiu_em'] = null;
            }
            if ($pivotRow->pivot->entrou_em === null) {
                $updates['entrou_em'] = now();
            }

            if (!empty($updates)) {
                $conversa->participantes()->updateExistingPivot($userId, $updates);
            }
        } else {
            $conversa->participantes()->attach($userId, [
                'entrou_em' => now(),
                'saiu_em'   => null,
            ]);
        }
    }

    /**
     * Garante que existe um utilizador "Sistema".
     * Idempotente e seguro para produção.
     */
    public function getOrCreateSystemUser(): User
    {
        return User::firstOrCreate(
            ['email' => self::SYSTEM_EMAIL],
            [
                'nome'              => self::SYSTEM_NAME,
                'password'          => Hash::make(Str::random(32)),
                'status'            => 'ativo',
                'email_verified_at' => now(),
                'is_system'         => true,
            ]
        );
    }
}
