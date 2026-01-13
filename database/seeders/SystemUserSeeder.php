<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\SystemConversationService;

class SystemUserSeeder extends Seeder
{
    public function run(): void
    {
        app(SystemConversationService::class)->getOrCreateSystemUser();
    }
}
