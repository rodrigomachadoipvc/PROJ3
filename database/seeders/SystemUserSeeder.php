<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SystemUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'sistema@communico.local'],
            [
                'nome' => 'Sistema',
                'password' => Hash::make(str()->random(32)),
                'is_system' => true,
                'active' => true,
            ]
        );
    }
}