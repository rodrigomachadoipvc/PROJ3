<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AutoReply;

class AutoReplySeeder extends Seeder
{
    public function run(): void
    {
        $replies = [
            [
                'trigger'  => 'horário',
                'response' => 'O nosso horário de funcionamento é das 09:00 às 18:00, de segunda a sexta.',
                'priority' => 1,
            ],
            [
                'trigger'  => 'ajuda',
                'response' => 'Pode descrever melhor a sua questão? Estamos aqui para ajudar.',
                'priority' => 2,
            ],
            [
                'trigger'  => 'contacto',
                'response' => 'Pode contactar a equipa através do email institucional ou desta conversa.',
                'priority' => 3,
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

