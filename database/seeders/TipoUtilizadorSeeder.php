<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoUtilizador;

class TipoUtilizadorSeeder extends Seeder
{
    public function run(): void
    {
        // Tipo base (admin) - se já existir, não duplica
        TipoUtilizador::firstOrCreate(
            ['nome' => 'Administrador'],
            ['descricao' => 'Acesso total ao backoffice e permissões.']
        );

        // Tipo default (obrigatório)
        TipoUtilizador::firstOrCreate(
            ['nome' => 'Utilizador'],
            ['descricao' => 'Utilizador padrão da aplicação.']
        );
    }
}
