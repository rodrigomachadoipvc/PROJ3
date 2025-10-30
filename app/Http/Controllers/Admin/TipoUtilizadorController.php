<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permissao;
use App\Models\TipoUtilizador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class TipoUtilizadorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Verifica se o utilizador tem a permissão para gerir tipos de utilizadores
        if (!Auth::user()->hasPermissionTo('gerir-tipos-utilizadores')) {
            abort(403, 'Acesso não autorizado. Necessita da permissão: gerir-tipos-utilizadores.');
        }

        // Obtém todos os Tipos de Utilizador com as suas permissões carregadas
        $tipos = TipoUtilizador::with('permissoes')->paginate(10);

        return view('admin.tipos-utilizadores.index', compact('tipos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Verifica se o utilizador tem permissão para criar
        if (!Auth::user()->hasPermissionTo('criar-tipos-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        // 1. Buscar todas as permissões disponíveis (ordenadas por nome)
        $permissoes = Permissao::orderBy('nome')->get();
        
        // 2. Passar as permissões para a view
        return view('admin.tipos-utilizadores.create', compact('permissoes'));
    }

    public function store(Request $request)
    {
        // Verifica se o utilizador tem permissão para criar
        if (!Auth::user()->hasPermissionTo('criar-tipos-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        // 1. Validação dos dados
        $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:tipos_utilizadores,nome'],
            'descricao' => ['nullable', 'string', 'max:500'],
            'permissoes' => ['nullable', 'array'], // Permissões é um array opcional
            'permissoes.*' => ['exists:permissoes,id'], // Cada item deve existir na tabela 'permissoes'
        ]);

        // 2. Criação do Tipo de Utilizador
        $tipo = TipoUtilizador::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
        ]);

        // 3. Associa as permissões (sync é ideal para relações N:N)
        // Se o array estiver vazio, nenhuma permissão é associada.
        if ($request->has('permissoes')) {
            $tipo->permissoes()->sync($request->permissoes);
        }

        // 4. Redirecionar com mensagem de sucesso
        return redirect()->route('admin.tipos-utilizadores.index')
                         ->with('success', 'Tipo de utilizador "' . $tipo->nome . '" criado e permissões associadas com sucesso!');
    }

    public function edit(string $id) // Recebe ID (string), não o Model
    {
        if (!Auth::user()->hasPermissionTo('editar-tipos-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        // 1. Encontra o tipo OU falha (404)
        $tipo = TipoUtilizador::findOrFail($id);

        // 2. Obter todas as permissões disponíveis
        $permissoes = Permissao::orderBy('nome')->get();

        // 3. Obter os IDs das permissões atualmente associadas
        $permissoesAtuais = $tipo->permissoes->pluck('id')->toArray();

        // 4. Passar os dados para a view de edição
        return view('admin.tipos-utilizadores.edit', compact('tipo', 'permissoes', 'permissoesAtuais'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) // Recebe ID (string)
    {
        if (!Auth::user()->hasPermissionTo('editar-tipos-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        $tipo = TipoUtilizador::findOrFail($id); // Encontra o Model manualmente

        // 1. Validação dos dados (Excluindo o nome atual para a regra unique)
        $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:tipos_utilizadores,nome,' . $tipo->id],
            'descricao' => ['nullable', 'string', 'max:500'],
            'permissoes' => ['nullable', 'array'],
            'permissoes.*' => ['exists:permissoes,id'],
        ]);

        // 2. Atualização e Sincronização
        $tipo->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
        ]);
        
        $permissoesParaSincronizar = $request->has('permissoes') ? $request->permissoes : [];
        $tipo->permissoes()->sync($permissoesParaSincronizar);

        // 3. Redirecionar com mensagem de sucesso
        return redirect()->route('admin.tipos-utilizadores.index')
                         ->with('success', 'Tipo de utilizador "' . $tipo->nome . '" atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) 
    {
        // CORRIGIDO: O Administrador TEM a permissão de editar, vamos usá-la.
        if (!Auth::user()->hasPermissionTo('editar-tipos-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }
        
        $tipo = TipoUtilizador::findOrFail($id); 

        // Regra de segurança: Não permitir eliminar o Tipo Administrador
        if ($tipo->nome === 'Administrador') {
            return redirect()->route('admin.tipos-utilizadores.index')
                             ->with('error', 'O Tipo de Utilizador "Administrador" não pode ser eliminado por questões de segurança.');
        }
        
        $nomeTipo = $tipo->nome;

        $tipo->permissoes()->detach();
        $tipo->delete();

        return redirect()->route('admin.tipos-utilizadores.index')
                         ->with('success', 'Tipo de utilizador "' . $nomeTipo . '" eliminado com sucesso.');
    }
    // ... (restantes métodos: store, show, edit, update, destroy serão implementados)
}