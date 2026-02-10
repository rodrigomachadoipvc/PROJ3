<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AutoReply;

class AutoReplySeeder extends Seeder
{
    public function run(): void
    {
        /**
         * NOTA IMPORTANTE (prioridades):
         * - O AutoReplyService faz orderBy('priority') ASC e usa str_contains().
         * - Logo: números MAIS BAIXOS = regras avaliadas primeiro.
         * - Comandos mais específicos (ex.: "horário detalhe") têm priority mais baixa
         *   do que comandos genéricos (ex.: "horário"), para evitar matches errados.
         *
         * NOTA UI (CORREÇÃO DEFINITIVA):
         * - As respostas DEVEM usar "\n" (quebras reais).
         * - A renderização é tratada na view via nl2br(e(...)) para garantir quebras
         *   mesmo que o CSS colapse whitespace e para manter segurança (sem XSS).
         */

        $menuAjudas =
            "Ajudas disponíveis (escreve exactamente o comando):\n\n" .
            "• Horário — @sistema horário\n" .
            "• Contactos — @sistema contactos\n" .
            "• Regras — @sistema regras\n" .
            "• Anexos — @sistema anexos\n" .
            "• Criar grupo — @sistema criar grupo\n" .
            "• Gerir membros — @sistema gerir membros\n" .
            "• Privacidade — @sistema privacidade\n" .
            "• Reportar — @sistema reportar\n\n" .
            "Dica: também podes usar @sistema ajuda <tema> (ex.: @sistema ajuda anexos).";

        $regrasChat =
            "Regras do chat:\n" .
            "1) Respeito e linguagem adequada.\n" .
            "2) Não partilhar dados sensíveis (passwords/códigos).\n" .
            "3) Evitar spam (mensagens repetidas).\n" .
            "4) Usar @sistema reportar para problemas.";

        $anexos =
            "Anexos:\n" .
            "• Até 5 anexos por mensagem.\n" .
            "• Máximo: 10MB por ficheiro e 25MB no total.\n" .
            "• Tipos: jpg, jpeg, png, webp, pdf, zip, rar.";

        $criarGrupo =
            "Criar grupo:\n" .
            "1) Abre o menu de grupos.\n" .
            "2) Clica em “Criar grupo”.\n" .
            "3) Define nome e (opcional) foto.\n" .
            "Depois podes adicionar membros em @sistema gerir membros.";

        $gerirMembros =
            "Gerir membros:\n" .
            "• Adicionar: abre o grupo → “Adicionar membro”.\n" .
            "• Remover: abre membros → “Remover”.\n" .
            "• Roles: member / moderator (o criador é sempre moderador).";

        $privacidade =
            "Privacidade:\n" .
            "• Evita partilhar dados pessoais sensíveis em chats de grupo.\n" .
            "• Em caso de incidente, usa @sistema reportar.";

        $reportar =
            "Reportar problema:\n" .
            "Envia uma mensagem com:\n" .
            "• O que aconteceu (passos)\n" .
            "• O que esperavas\n" .
            "• Print/erro (se existir)\n" .
            "• Hora aproximada e conversa/grupo";

        $contactos =
            "Contactos úteis:\n" .
            "• RH: admin@communico.dev\n" .
            "• IT: sistema@communico.pt\n" .
            "• Urgências: 112 (Portugal).";

        $contactosComDica = $contactos . "\n\nPara mais detalhe: @sistema contactos detalhe";

        $contactosDetalhe =
            "Contactos (detalhe):\n" .
            "• IT: sistema@communico.pt\n" .
            "• RH: admin@communico.dev\n" .
            "• Urgências: 112 (Portugal) (apenas incidentes críticos).";

        $horarioResumo =
            "Horário (resumo):\n" .
            "• Dias úteis: 09:00–18:00\n" .
            "• Pausa: 13:00–14:00\n" .
            "Para detalhe: @sistema horário detalhe";

        $horarioDetalhe =
            "Horário (detalhe):\n" .
            "• Atendimento normal: 09:00–13:00 e 14:00–18:00\n" .
            "• Fora de horas: usa @sistema reportar e descreve a urgência.";

        $ajudaGenerica =
            "Ajuda por tema:\n" .
            "• @sistema ajuda anexos\n" .
            "• @sistema ajuda regras\n" .
            "• @sistema ajuda contactos\n" .
            "• @sistema ajuda reportar\n\n" .
            "Menu completo: @sistema lista de ajudas";

        $replies = [
            // =========================
            // 1) MAIS ESPECÍFICOS (10-19)
            // =========================
            [
                'trigger'  => '@sistema horário detalhe',
                'response' => $horarioDetalhe,
                'priority' => 10,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema horario detalhe',
                'response' => $horarioDetalhe,
                'priority' => 10,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema contactos detalhe',
                'response' => $contactosDetalhe,
                'priority' => 11,
                'active'   => true,
            ],

            // Ajuda por tema (router)
            [
                'trigger'  => '@sistema ajuda anexos',
                'response' => $anexos,
                'priority' => 12,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema ajuda regras',
                'response' => $regrasChat,
                'priority' => 12,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema ajuda criar grupo',
                'response' => $criarGrupo,
                'priority' => 12,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema ajuda gerir membros',
                'response' => $gerirMembros,
                'priority' => 12,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema ajuda privacidade',
                'response' => $privacidade,
                'priority' => 12,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema ajuda contactos',
                'response' => $contactos,
                'priority' => 12,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema ajuda reportar',
                'response' => $reportar,
                'priority' => 12,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema ajuda horário',
                'response' => $horarioResumo,
                'priority' => 12,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema ajuda horario',
                'response' => $horarioResumo,
                'priority' => 12,
                'active'   => true,
            ],

            // =========================
            // 2) COMANDOS BASE (20-29)
            // =========================
            [
                'trigger'  => '@sistema horário',
                'response' => $horarioResumo,
                'priority' => 20,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema horario',
                'response' => $horarioResumo,
                'priority' => 20,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema contactos',
                'response' => $contactosComDica,
                'priority' => 21,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema regras',
                'response' => $regrasChat,
                'priority' => 22,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema anexos',
                'response' => $anexos,
                'priority' => 23,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema criar grupo',
                'response' => $criarGrupo,
                'priority' => 24,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema gerir membros',
                'response' => $gerirMembros,
                'priority' => 25,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema privacidade',
                'response' => $privacidade,
                'priority' => 27,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema reportar',
                'response' => $reportar,
                'priority' => 28,
                'active'   => true,
            ],

            // =========================
            // 3) MENUS / ENTRADAS GERAIS (30-49)
            // =========================
            [
                'trigger'  => '@sistema lista de ajudas',
                'response' => $menuAjudas,
                'priority' => 30,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema ajudas',
                'response' => $menuAjudas,
                'priority' => 31,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema menu',
                'response' => $menuAjudas,
                'priority' => 32,
                'active'   => true,
            ],
            [
                'trigger'  => '@sistema ajuda',
                'response' => $ajudaGenerica,
                'priority' => 40,
                'active'   => true,
            ],

            // =========================
            // 4) FALLBACK (último)
            // =========================
            [
                'trigger'  => '@sistema',
                'response' => "Comando não reconhecido.\nEscreve @sistema lista de ajudas",
                'priority' => 9999,
                'active'   => true,
            ],
        ];

        foreach ($replies as $reply) {
            AutoReply::updateOrCreate(
                ['trigger' => $reply['trigger']],
                $reply
            );
        }
    }
}
