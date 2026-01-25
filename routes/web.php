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
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Controllers\Admin\EstatisticasController; 


/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('user.dashboard');
    }

    return view('welcome');
});

require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| Rotas Autenticadas
|--------------------------------------------------------------------------
|
| Requisito: após login, TODOS (user e admin) devem ser redirecionados para
| /user/dashboard.
|
| A área /admin mantém-se protegida pelo middleware EnsureUserIsAdmin.
|
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // ROTA DE COMPATIBILIDADE (/dashboard) → encaminha sempre para o painel de utilizador.
    Route::get('/dashboard', function () {
        return redirect()->route('user.dashboard');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Dashboard do Utilizador Normal
    |--------------------------------------------------------------------------
    */
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('user.dashboard');
        
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

Route::middleware(['auth', 'verified', EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::resource('tipos-utilizadores', TipoUtilizadorController::class)
            ->parameters(['tipos-utilizadores' => 'tipo_utilizador']);

        Route::resource('utilizadores', UtilizadorController::class);
        Route::resource('grupos', GrupoController::class);

        Route::post('grupos/{grupo}/membros', [GrupoController::class, 'adicionarMembro'])
            ->name('grupos.membros.store');

        Route::delete('grupos/{grupo}/membros/{user}', [GrupoController::class, 'removerMembro'])
            ->name('grupos.membros.destroy');

        Route::get('grupos/{grupo}/membros-json', function (\App\Models\Grupo $grupo) {
            return $grupo->membros()
                ->select('users.id', 'users.nome', 'users.email')
                ->get();
        })->name('grupos.membros.json');

        Route::resource('auto-replies', AutoReplyController::class)->except(['show']);

        // ✅ Estatísticas
        Route::get('estatisticas', [EstatisticasController::class, 'index'])->name('estatisticas.index');
        Route::get('estatisticas/export/csv', [EstatisticasController::class, 'exportCsv'])->name('estatisticas.export.csv');
        Route::get('estatisticas/export/excel', [EstatisticasController::class, 'exportExcel'])->name('estatisticas.export.excel');
        Route::get('estatisticas/export/pdf', [EstatisticasController::class, 'exportPdf'])->name('estatisticas.export.pdf');
    });
