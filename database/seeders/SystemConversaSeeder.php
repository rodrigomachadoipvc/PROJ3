<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Services\SystemConversationService;

class SystemConversaSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(SystemConversationService::class);

        User::where('is_system', false)
            ->chunk(200, function ($users) use ($service) {
                foreach ($users as $user) {
                    $service->ensureForUser($user);
                }
            });
    }
}
