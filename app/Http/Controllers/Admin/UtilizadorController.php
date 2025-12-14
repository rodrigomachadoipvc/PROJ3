<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TipoUtilizador; 
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UtilizadorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Verifica se o utilizador tem permissão para editar utilizadores (geralmente Admin)
        if (!Auth::user()->hasPermissionTo('editar-utilizadores')) {
             abort(403, 'Acesso não autorizado.');
        }

        // Obtém todos os utilizadores, carregando a relação com TipoUtilizador
        // Ordena por nome e pagina os resultados
        $utilizadores = \App\Models\User::with('tipoUtilizador')
                                        ->orderBy('nome')
                                        ->paginate(15); // Mostrar 15 utilizadores por página

        return view('admin.utilizadores.index', compact('utilizadores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Verifica se o utilizador tem permissão para criar
        if (!Auth::user()->hasPermissionTo('criar-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        // 1. Buscar todos os Tipos de Utilizador disponíveis (ordenados por nome)
        // Excluir o Administrador se não quisermos que seja selecionável? (Opcional)
        $tiposUtilizador = TipoUtilizador::orderBy('nome')->get();
        
        // 2. Passar os tipos para a view
        return view('admin.utilizadores.create', compact('tiposUtilizador'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Verifica se o utilizador tem permissão para criar
        if (!Auth::user()->hasPermissionTo('criar-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        // 1. Validação dos dados
        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
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

        // 2. Criação do Utilizador
        $utilizador = User::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo_utilizador_id' => $request->tipo_utilizador_id,
            'status' => $request->status,
            'email_verified_at' => now(), // Assume verificado ao ser criado pelo Admin
        ]);

        // 3. Redirecionar com mensagem de sucesso
        return redirect()->route('admin.utilizadores.index')
                         ->with('success', 'Utilizador "' . $utilizador->nome . '" criado com sucesso!');
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
    public function edit(string $id) // Recebe ID, não o Model para evitar binding issues
    {
        // Verifica se o utilizador tem permissão para editar
        if (!Auth::user()->hasPermissionTo('editar-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        // 1. Encontra o utilizador OU falha (404)
        $utilizador = User::findOrFail($id);

        // 2. Buscar todos os Tipos de Utilizador disponíveis
        $tiposUtilizador = TipoUtilizador::orderBy('nome')->get();

        // 3. Passar os dados para a view de edição
        return view('admin.utilizadores.edit', compact('utilizador', 'tiposUtilizador'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) // Recebe ID
    {
        // Verifica permissão
        if (!Auth::user()->hasPermissionTo('editar-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        $utilizador = User::findOrFail($id); // Encontra o utilizador

        // 1. Validação
        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email,'.$utilizador->id], // Exclui o email atual
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // Password é opcional
            'tipo_utilizador_id' => ['required', 'exists:tipos_utilizadores,id'],
            'status' => ['required', 'in:ativo,inativo'],
        ]);

        // 2. Prepara os dados para atualização
        $dadosUpdate = [
            'nome' => $request->nome,
            'email' => $request->email,
            'tipo_utilizador_id' => $request->tipo_utilizador_id,
            'status' => $request->status,
        ];

        // 3. Atualiza a password APENAS se foi fornecida
        if ($request->filled('password')) {
            $dadosUpdate['password'] = Hash::make($request->password);
        }

        // 4. Atualiza o utilizador
        $utilizador->update($dadosUpdate);

        // 5. Redireciona
        return redirect()->route('admin.utilizadores.index')
                         ->with('success', 'Utilizador "' . $utilizador->nome . '" atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) // Recebe ID
    {
        // Verifica permissão
        if (!Auth::user()->hasPermissionTo('eliminar-utilizadores')) {
            abort(403, 'Acesso não autorizado.');
        }

        $utilizador = User::findOrFail($id); // Encontra o utilizador

        // Regra de segurança 1: Não permitir auto-eliminação
        if ($utilizador->id === Auth::id()) {
             return redirect()->route('admin.utilizadores.index')
                             ->with('error', 'Não pode eliminar a sua própria conta.');
        }

        // Regra de segurança 2: Não permitir eliminar o Administrador principal (ID 1 ou email específico)
        // Adaptar esta regra conforme necessário (ex: verificar pelo email)
        if ($utilizador->email === 'admin@communico.dev') {
             return redirect()->route('admin.utilizadores.index')
                             ->with('error', 'O utilizador Administrador principal não pode ser eliminado.');
        }

        $nomeUtilizador = $utilizador->nome;

        // Elimina o utilizador
        $utilizador->delete();

        // Redireciona
        return redirect()->route('admin.utilizadores.index')
                         ->with('success', 'Utilizador "' . $nomeUtilizador . '" eliminado com sucesso.');
    }
}
