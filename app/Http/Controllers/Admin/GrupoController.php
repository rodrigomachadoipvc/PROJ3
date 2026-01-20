<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\User;
use App\Models\Conversa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GrupoController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->hasPermissionTo('editar-grupos')) {
            abort(403, 'Acesso não autorizado.');
        }

        $q = trim((string) $request->query('q', ''));

        $query = Grupo::with('criador', 'membros')
            ->orderBy('nome');

        if ($q !== '') {
            $query->where(function ($outer) use ($q) {
                $outer->where('nome', 'like', '%' . $q . '%')
                    ->orWhere('descricao', 'like', '%' . $q . '%');

                if (ctype_digit($q)) {
                    $outer->orWhere('id', (int) $q);
                }

                $outer->orWhereHas('criador', function ($c) use ($q) {
                    $c->where('nome', 'like', '%' . $q . '%')
                      ->orWhere('email', 'like', '%' . $q . '%');
                });
            });
        }

        $grupos = $query
            ->paginate(15)
            ->withQueryString();

        return view('admin.grupos.index', compact('grupos'));
    }

    public function create()
    {
        $utilizadores = User::orderBy('nome')->get();
        return view('admin.grupos.create', compact('utilizadores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        $grupo = Grupo::create([
            'nome'       => $request->nome,
            'descricao'  => $request->descricao,
            'criador_id' => auth()->id(),
        ]);

        // Cria ou obtém conversa associada ao grupo
        $conversa = Conversa::firstOrCreate(
            ['grupo_id' => $grupo->id],
            [
                'tipo' => 'grupo',
                'nome' => $grupo->nome,
            ]
        );

        // Adiciona o criador como participante da conversa
        $conversa->participantes()->syncWithoutDetaching([auth()->id()]);

        return redirect()->route('admin.grupos.index')->with('success', 'Grupo criado com sucesso.');
    }

    public function edit(Grupo $grupo)
    {
        return view('admin.grupos.edit', compact('grupo'));
    }

    public function update(Request $request, Grupo $grupo)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        $grupo->update($request->only('nome', 'descricao'));

        // Atualiza nome da conversa associada ao grupo
        if ($grupo->conversa) {
            $grupo->conversa->update(['nome' => $grupo->nome]);
        }

        return redirect()->route('admin.grupos.index')->with('success', 'Grupo atualizado com sucesso.');
    }

    public function destroy(Grupo $grupo)
    {
        $grupo->delete();
        return redirect()->route('admin.grupos.index')->with('success', 'Grupo removido.');
    }

    public function adicionarMembro(Request $request, Grupo $grupo)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        if ($grupo->membros()->where('users.id', $request->user_id)->exists()) {
            return back()->with('error', 'O utilizador já pertence ao grupo.');
        }

        // Adicionar ao grupo
        $grupo->membros()->attach($request->user_id);

        // Criar a conversa do grupo caso não exista
        $conversa = Conversa::firstOrCreate(
            ['grupo_id' => $grupo->id],
            [
                'tipo' => 'grupo',
                'nome' => $grupo->nome,
            ]
        );

        // Adicionar o membro à conversa
        $conversa->participantes()->syncWithoutDetaching([$request->user_id]);

        return back()->with('success', 'Membro adicionado ao grupo.');
    }

    public function removerMembro(Grupo $grupo, User $user)
    {
        // Remover do grupo
        $grupo->membros()->detach($user->id);

        // Remover também da conversa
        if ($grupo->conversa) {
            $grupo->conversa->participantes()->detach($user->id);
        }

        return back()->with('success', 'Membro removido com sucesso.');
    }
}
