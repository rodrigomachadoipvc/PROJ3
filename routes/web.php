<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\TipoUtilizadorController;
use App\Http\Controllers\Admin\UtilizadorController;
use App\Http\Controllers\Admin\GrupoController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Admin\AutoReplyController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use App\Models\Anexo;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| Rotas Autenticadas (Dashboard dinâmico)
|--------------------------------------------------------------------------
|
| Se o utilizador for ADMIN → admin.dashboard
| Se for utilizador normal → dashboard.user
|
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // ROTA PRINCIPAL DE DASHBOARD (decide automaticamente)
    Route::get('/dashboard', function () {

        $user = Auth::user();

        if ($user->tipoUtilizador?->nome === 'Administrador') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.dashboard');

    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Dashboard do Utilizador Normal
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [UserDashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');
        
    /*
    |--------------------------------------------------------------------------
    | Perfil do Utilizador
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::delete('/profile/avatar/remove', [ProfileController::class, 'removeAvatar'])
        ->name('profile.avatar.remove');

    /*
    |--------------------------------------------------------------------------
    | Chat
    |--------------------------------------------------------------------------
    */
    Route::get('/chat', [ChatController::class, 'index'])
        ->name('chat.index');

    Route::get('/anexos/{anexo}', function (Anexo $anexo) {

        Gate::authorize('view', $anexo);

        if (! Storage::disk('public')->exists($anexo->caminho_storage)) {
            abort(404);
        }

        // Imagens: mostrar inline (serve para <img src="...">)
        if (str_starts_with($anexo->mime_type, 'image/')) {
            $path = Storage::disk('public')->path($anexo->caminho_storage);

            return response()->file($path, [
                'Content-Type' => $anexo->mime_type,
                'Content-Disposition' => 'inline; filename="' . $anexo->nome_original . '"',
            ]);
        }

        // Outros: força download
        return Storage::disk('public')->download(
            $anexo->caminho_storage,
            $anexo->nome_original
        );

    })->name('anexos.ver');
});


/*
|--------------------------------------------------------------------------
| ROTAS DA ÁREA ADMINISTRATIVA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // DASHBOARD ADMIN
        Route::get('/dashboard', function () {

            if (Auth::user()->tipoUtilizador?->nome !== 'Administrador') {
                abort(403, 'Acesso Não Autorizado. Área exclusiva a administradores.');
            }

            return view('admin.dashboard');

        })->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | CRUDs Administração
        |--------------------------------------------------------------------------
        */

        Route::resource('tipos-utilizadores', TipoUtilizadorController::class)
            ->parameters(['tipos-utilizadores' => 'tipo_utilizador']);

        Route::resource('utilizadores', UtilizadorController::class);

        Route::resource('grupos', GrupoController::class);

        /*
        |--------------------------------------------------------------------------
        | Membros do Grupo
        |--------------------------------------------------------------------------
        */
        Route::post('grupos/{grupo}/membros', [GrupoController::class, 'adicionarMembro'])
            ->name('grupos.membros.store');

        Route::delete('grupos/{grupo}/membros/{user}', [GrupoController::class, 'removerMembro'])
            ->name('grupos.membros.destroy');

        /*
        |--------------------------------------------------------------------------
        | JSON para carregamento dinâmico
        |--------------------------------------------------------------------------
        */
        Route::get('grupos/{grupo}/membros-json', function (\App\Models\Grupo $grupo) {
            return $grupo->membros()
                ->select('users.id', 'users.nome', 'users.email')
                ->get();
        })->name('grupos.membros.json');

        Route::resource('auto-replies', AutoReplyController::class)
            ->except(['show']);

    });