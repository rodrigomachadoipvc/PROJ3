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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/iniciar-dm/{outroUtilizadorId}', [ChatController::class, 'iniciarConversaDM'])
         ->name('chat.iniciar-dm')
         ->where('outroUtilizadorId', '[0-9]+');
});

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

        // Dashboard/Home da Administração
        Route::get('/dashboard', function () {
            // Verifica se o utilizador é Administrador antes de carregar a view
            if (Auth::user()->tipoUtilizador->nome !== 'Administrador') {
                abort(403, 'Acesso Não Autorizado. Tipo de utilizador insuficiente.');
            }
            return view('admin.dashboard');
        })->name('dashboard');

        // CRUD de Tipos de Utilizadores (Roles)
        Route::resource('tipos-utilizadores', TipoUtilizadorController::class)->parameters([
            'tipos-utilizadores' => 'tipo_utilizador', 
        ]);

        Route::resource('utilizadores', UtilizadorController::class);
        Route::resource('grupos', GrupoController::class);

    });
});