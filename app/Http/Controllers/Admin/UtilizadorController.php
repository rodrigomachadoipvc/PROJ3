<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TipoUtilizador; 
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UtilizadorController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasPermissionTo('editar-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        $utilizadores = User::with('tipoUtilizador')
            ->orderBy('nome')
            ->paginate(15);

        return view('admin.utilizadores.index', compact('utilizadores'));
    }

    public function create()
    {
        if (!Auth::user()->hasPermissionTo('criar-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        $tiposUtilizador = TipoUtilizador::orderBy('nome')->get();

        return view('admin.utilizadores.create', compact('tiposUtilizador'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermissionTo('criar-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],
            'tipo_utilizador_id' => ['required', 'exists:tipos_utilizadores,id'],
            'status' => ['required', 'in:ativo,inativo'],
        ], [
            'password.regex' =>
                'A password deve conter pelo menos uma letra maiúscula, um número e um carácter especial.',
        ]);

        $utilizador = User::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo_utilizador_id' => $request->tipo_utilizador_id,
            'status' => $request->status,
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.utilizadores.index')
            ->with('success', 'Utilizador "' . $utilizador->nome . '" criado com sucesso!');
    }

    public function edit(string $id)
    {
        if (!Auth::user()->hasPermissionTo('editar-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        $utilizador = User::findOrFail($id);
        $tiposUtilizador = TipoUtilizador::orderBy('nome')->get();

        return view('admin.utilizadores.edit', compact('utilizador', 'tiposUtilizador'));
    }

    public function update(Request $request, string $id)
    {
        if (!Auth::user()->hasPermissionTo('editar-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        $utilizador = User::findOrFail($id);

        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:' . User::class . ',email,' . $utilizador->id,
            ],
            'password' => [
                'nullable',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],
            'tipo_utilizador_id' => ['required', 'exists:tipos_utilizadores,id'],
            'status' => ['required', 'in:ativo,inativo'],
        ], [
            'password.regex' =>
                'A password deve conter pelo menos uma letra maiúscula, um número e um carácter especial.',
        ]);

        $dadosUpdate = [
            'nome' => $request->nome,
            'email' => $request->email,
            'tipo_utilizador_id' => $request->tipo_utilizador_id,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $dadosUpdate['password'] = Hash::make($request->password);
        }

        $utilizador->update($dadosUpdate);

        return redirect()
            ->route('admin.utilizadores.index')
            ->with('success', 'Utilizador "' . $utilizador->nome . '" atualizado com sucesso!');
    }

    public function destroy(string $id)
    {
        if (!Auth::user()->hasPermissionTo('eliminar-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        $utilizador = User::findOrFail($id);

        if ($utilizador->id === Auth::id()) {
            return redirect()
                ->route('admin.utilizadores.index')
                ->with('error', 'Não pode eliminar a sua própria conta.');
        }

        if ($utilizador->email === 'admin@communico.dev') {
            return redirect()
                ->route('admin.utilizadores.index')
                ->with('error', 'O utilizador Administrador principal não pode ser eliminado.');
        }

        $nomeUtilizador = $utilizador->nome;

        $utilizador->delete();

        return redirect()
            ->route('admin.utilizadores.index')
            ->with('success', 'Utilizador "' . $nomeUtilizador . '" eliminado com sucesso.');
    }
}
