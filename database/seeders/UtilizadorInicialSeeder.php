<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UtilizadorInicialSeeder extends Seeder
{
    /**
     * Executa o seeder da base de dados (Cria o Admin e associa Permissões).
     */
    public function run(): void
    {
        // 1. Cria o Tipo de Utilizador 'Administrador'
        DB::table('tipos_utilizadores')->updateOrInsert(
            ['nome' => 'Administrador'],
            ['descricao' => 'Acesso total ao sistema de gestão e funcionalidades.', 'created_at' => now(), 'updated_at' => now()]
        );
        $tipoUtilizadorId = DB::table('tipos_utilizadores')->where('nome', 'Administrador')->value('id');

        // 2. Associa TODAS as permissões ao Administrador
        if ($tipoUtilizadorId) {
            $permissoes = DB::table('permissoes')->pluck('id');
            
            $adminPermissions = $permissoes->map(function ($permissaoId) use ($tipoUtilizadorId) {
                return [
                    'tipo_utilizador_id' => $tipoUtilizadorId,
                    'permissao_id' => $permissaoId,
                ];
            })->toArray();
            
            DB::table('tipo_utilizador_permissoes')->insertOrIgnore($adminPermissions);

            // 3. Cria o Utilizador Administrador Padrão
            DB::table('users')->updateOrInsert(
                ['email' => 'admin@communico.dev'],
                [
                    'tipo_utilizador_id' => $tipoUtilizadorId,
                    'nome' => 'Admin Comunico',
                    'password' => Hash::make('password'), 
                    'status' => 'ativo',
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}