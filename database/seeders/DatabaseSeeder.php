<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissaoSeeder::class,
            UtilizadorInicialSeeder::class,
            SystemUserSeeder::class,

            SystemConversaSeeder::class,

            AutoReplySeeder::class,
            TipoUtilizadorSeeder::class,
        ]);
    }
}
