<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\TipoUtilizadorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\Admin\UtilizadorController;
use App\Http\Controllers\Admin\GrupoController;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Rotas Públicas e de Autenticação (Breeze)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Rotas de utilizador autenticado (Chat, Profile, etc.)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Rotas do Chat ---
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
});

Route::delete('/profile/avatar/remove', [ProfileController::class, 'removeAvatar'])
    ->middleware(['auth'])
    ->name('profile.avatar.remove');


require __DIR__.'/auth.php';



/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS (Área de Administração)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // Redireciona o dashboard padrão para a nossa área administrativa
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');


    // ÁREA ADMINISTRATIVA
    Route::prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', function () {

            if (Auth::user()->tipoUtilizador?->nome !== 'Administrador') {
                abort(403, 'Acesso Não Autorizado. Tipo de utilizador insuficiente.');
            }

            return view('admin.dashboard');
        })->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | CRUDs Admin
        |--------------------------------------------------------------------------
        */

        Route::resource('tipos-utilizadores', TipoUtilizadorController::class)->parameters([
            'tipos-utilizadores' => 'tipo_utilizador',
        ]);

        Route::resource('utilizadores', UtilizadorController::class);
        Route::resource('grupos', GrupoController::class);


        /*
        |--------------------------------------------------------------------------
        | Gestão de Membros de Grupo
        |--------------------------------------------------------------------------
        */

        Route::post('grupos/{grupo}/membros', 
            [GrupoController::class, 'adicionarMembro']
        )->name('grupos.membros.store');

        Route::delete('grupos/{grupo}/membros/{user}', 
            [GrupoController::class, 'removerMembro']
        )->name('grupos.membros.destroy');


        /*
        |--------------------------------------------------------------------------
        | Rota JSON para carregar membros dinamicamente (USADA PELO ALPINE)
        |--------------------------------------------------------------------------
        */

        Route::get('grupos/{grupo}/membros-json', function (\App\Models\Grupo $grupo) {
            return $grupo->membros()
                ->select('users.id', 'users.nome', 'users.email')
                ->get();
        })->name('grupos.membros.json');


    });
});
