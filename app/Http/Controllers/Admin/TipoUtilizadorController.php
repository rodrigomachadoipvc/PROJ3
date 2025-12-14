<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permissao;
use App\Models\TipoUtilizador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TipoUtilizadorController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermissionTo('gerir-tipos-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        $tipos = TipoUtilizador::with('permissoes')->paginate(10);

        return view('admin.tipos-utilizadores.index', compact('tipos'));
    }

    public function create()
    {
        if (!Auth::user()->hasPermissionTo('criar-tipos-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        $permissoes = Permissao::orderBy('nome')->get();

        return view('admin.tipos-utilizadores.create', compact('permissoes'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermissionTo('criar-tipos-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:tipos_utilizadores,nome'],
            'descricao' => ['nullable', 'string', 'max:500'],
            'permissoes' => ['nullable', 'array'],
            'permissoes.*' => ['exists:permissoes,id'],
        ]);

        $tipo = TipoUtilizador::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
        ]);

        if ($request->filled('permissoes')) {
            $tipo->permissoes()->sync($request->permissoes);
        }

        return redirect()
            ->route('admin.tipos-utilizadores.index')
            ->with('success', 'Tipo de utilizador "' . $tipo->nome . '" criado com sucesso!');
    }

    public function edit(string $id)
    {
        if (!Auth::user()->hasPermissionTo('editar-tipos-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        $tipo = TipoUtilizador::findOrFail($id);
        $permissoes = Permissao::orderBy('nome')->get();
        $permissoesAtuais = $tipo->permissoes->pluck('id')->toArray();

        return view('admin.tipos-utilizadores.edit', compact('tipo', 'permissoes', 'permissoesAtuais'));
    }

    public function update(Request $request, string $id)
    {
        if (!Auth::user()->hasPermissionTo('editar-tipos-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        $tipo = TipoUtilizador::findOrFail($id);

        $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:tipos_utilizadores,nome,' . $tipo->id],
            'descricao' => ['nullable', 'string', 'max:500'],
            'permissoes' => ['nullable', 'array'],
            'permissoes.*' => ['exists:permissoes,id'],
        ]);

        $tipo->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
        ]);

        $tipo->permissoes()->sync($request->permissoes ?? []);

        return redirect()
            ->route('admin.tipos-utilizadores.index')
            ->with('success', 'Tipo de utilizador "' . $tipo->nome . '" atualizado com sucesso!');
    }

    public function destroy(string $id)
    {
        if (!Auth::user()->hasPermissionTo('editar-tipos-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        $tipo = TipoUtilizador::findOrFail($id);

        if ($tipo->nome === 'Administrador') {
            return redirect()
                ->route('admin.tipos-utilizadores.index')
                ->with('error', 'O Tipo de Utilizador "Administrador" não pode ser eliminado.');
        }

        $nomeTipo = $tipo->nome;

        $tipo->permissoes()->detach();
        $tipo->delete();

        return redirect()
            ->route('admin.tipos-utilizadores.index')
            ->with('success', 'Tipo de utilizador "' . $nomeTipo . '" eliminado com sucesso.');
    }
}
