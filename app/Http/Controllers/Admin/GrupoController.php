<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\Grupo;
use App\Models\User;

class GrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Verifica se o utilizador tem permissão (usaremos 'editar-grupos' como base por agora)
        if (!Auth::user()->hasPermissionTo('editar-grupos')) {
             abort(403, 'Acesso não autorizado.');
        }

        // Obtém todos os grupos, carregando a relação com o criador
        // Ordena por nome e pagina os resultados
        $grupos = \App\Models\Grupo::with('criador') // Carrega quem criou o grupo
                                    ->orderBy('nome')
                                    ->paginate(15); 

        return view('admin.grupos.index', compact('grupos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Verifica se o utilizador tem permissão para criar grupos
        if (!Auth::user()->hasPermissionTo('criar-grupos')) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('admin.grupos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Verifica se o utilizador tem permissão para criar
        if (!Auth::user()->hasPermissionTo('criar-grupos')) {
            abort(403, 'Acesso não autorizado.');
        }

        // 1. Validação dos dados
        $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:grupos,nome'],
            'descricao' => ['nullable', 'string', 'max:1000'],
        ]);

        // 2. Criação do Grupo, associando o criador (utilizador logado)
        $grupo = Grupo::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'criador_id' => Auth::id(), // Atribui o ID do utilizador autenticado
        ]);

        // TO-DO: Adicionar o criador como membro inicial do grupo? (Próximo passo)

        // 3. Redirecionar com mensagem de sucesso
        return redirect()->route('admin.grupos.index')
                         ->with('success', 'Grupo "' . $grupo->nome . '" criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) // Recebe ID
    {
        // Verifica se o utilizador tem permissão para editar grupos
        if (!Auth::user()->hasPermissionTo('editar-grupos')) {
            abort(403, 'Acesso não autorizado.');
        }

        // 1. Encontra o grupo OU falha (404)
        $grupo = Grupo::findOrFail($id);
        
        // 2. Passar os dados para a view de edição
        return view('admin.grupos.edit', compact('grupo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) // Recebe ID
    {
        // Verifica permissão
        if (!Auth::user()->hasPermissionTo('editar-grupos')) {
            abort(403, 'Acesso não autorizado.');
        }

        $grupo = Grupo::findOrFail($id); // Encontra o grupo

        // 1. Validação (Exclui o nome atual da regra unique)
        $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:grupos,nome,'.$grupo->id],
            'descricao' => ['nullable', 'string', 'max:1000'],
        ]);

        // 2. Atualiza o grupo
        $grupo->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            // O criador_id não é alterado aqui
        ]);

        // 3. Redireciona
        return redirect()->route('admin.grupos.index')
                         ->with('success', 'Grupo "' . $grupo->nome . '" atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) // Recebe ID
    {
        // Verifica permissão
        if (!Auth::user()->hasPermissionTo('eliminar-grupos')) {
            abort(403, 'Acesso não autorizado.');
        }

        $grupo = Grupo::findOrFail($id); // Encontra o grupo

        // TO-DO: Lógica adicional antes de eliminar?
        // - Desassociar membros?
        // - Arquivar mensagens? Ou eliminar?
        // Por agora, vamos apenas eliminar o grupo.

        $nomeGrupo = $grupo->nome;

        // Elimina o grupo
        $grupo->delete();

        // Redireciona
        return redirect()->route('admin.grupos.index')
                         ->with('success', 'Grupo "' . $nomeGrupo . '" eliminado com sucesso.');
    }

    public function adicionarMembro(Request $request, Grupo $grupo)
    {
        // Verifica se o utilizador tem permissão para editar grupos
        if (!Auth::user()->hasPermissionTo('editar-grupos')) {
            abort(403, 'Acesso não autorizado.');
        }
        
        $request->validate(['user_id' => 'required|exists:users,id']);
        $user = User::findOrFail($request->user_id); // Carregamos o modelo

        try {
            $grupo->membros()->attach($user->id);
            return back()->with('success', 'Membro ' . $user->nome . ' adicionado ao grupo ' . $grupo->nome . ' com sucesso.');
        } catch (\Exception $e) {
            // Captura o erro se o utilizador já for membro
            return back()->with('error', 'O utilizador já é membro deste grupo.');
        }
    }

    public function removerMembro(Grupo $grupo, $user)
    {
        // Verifica se o utilizador tem permissão para editar grupos
        if (!Auth::user()->hasPermissionTo('editar-grupos')) {
            abort(403, 'Acesso não autorizado.');
        }

        $userModel = User::findOrFail($user); 

        // Tenta remover (detach) o utilizador
        if ($grupo->membros()->detach($userModel->id) > 0) {
            return back()->with('success', 'Membro ' . $userModel->nome . ' removido do grupo com sucesso.');
        }

        return back()->with('error', 'O utilizador não é membro deste grupo ou ocorreu um erro.');
    }

}
