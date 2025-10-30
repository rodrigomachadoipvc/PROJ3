<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissaoSeeder extends Seeder
{
    /**
     * Executa o seeder da base de dados (Cria todas as permissões).
     */
    public function run(): void
    {
        $permissoes = [
            // CRUD de Grupos
            ['nome' => 'criar-grupos', 'descricao' => 'Permite adicionar novos grupos.'],
            ['nome' => 'editar-grupos', 'descricao' => 'Permite editar nome/descrição dos grupos.'],
            ['nome' => 'eliminar-grupos', 'descricao' => 'Permite apagar grupos.'],
            ['nome' => 'gerir-membros-grupos', 'descricao' => 'Permite adicionar/remover membros de grupos.'],
            
            // CRUD de Tipos/Roles
            ['nome' => 'criar-tipos-utilizadores', 'descricao' => 'Permite adicionar novos tipos de utilizadores (roles).'],
            ['nome' => 'editar-tipos-utilizadores', 'descricao' => 'Permite modificar roles/permissões associadas.'],
            ['nome' => 'gerir-tipos-utilizadores', 'descricao' => 'Permite listar, visualizar e gerir todos os tipos de utilizador.'], // Adicionado para o Controller

            // CRUD de Utilizadores
            ['nome' => 'criar-utilizadores', 'descricao' => 'Permite adicionar novos utilizadores.'],
            ['nome' => 'editar-utilizadores', 'descricao' => 'Permite editar dados de outros utilizadores.'],
            ['nome' => 'eliminar-utilizadores', 'descricao' => 'Permite desativar ou apagar utilizadores.'],

            // Exportação e Estatísticas
            ['nome' => 'exportar-dados', 'descricao' => 'Permite exportar relatórios para Excel/PDF.'],
            ['nome' => 'ver-estatisticas', 'descricao' => 'Permite aceder aos painéis de estatísticas.'],

            // Automação/ML
            ['nome' => 'configurar-respostas-automaticas', 'descricao' => 'Permite configurar e treinar o modelo de Machine Learning.'],
        ];

        // Inserir permissões
        foreach ($permissoes as $permissao) {
            DB::table('permissoes')->updateOrInsert(
                ['nome' => $permissao['nome']],
                ['descricao' => $permissao['descricao'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}