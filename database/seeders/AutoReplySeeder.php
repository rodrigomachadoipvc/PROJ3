<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AutoReply;

class AutoReplySeeder extends Seeder
{
    public function run(): void
    {
        $replies = [
            [
                'trigger'  => '@sistema lista de ajudas',
                'response' => '@sistema horário; @sistema localização',
                'priority' => 1,
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
