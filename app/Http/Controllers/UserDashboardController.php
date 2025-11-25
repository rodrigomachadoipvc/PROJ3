<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    /**
     * Dashboard para utilizadores normais.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Carrega as conversas em que participa
        $recentConversas = $user->conversasParticipadas()
            ->with(['ultimaMensagem.emissor', 'grupo'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Carrega os grupos onde pertence
        $recentGrupos = $user->grupos()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.user', [
            'user'            => $user,
            'recentConversas' => $recentConversas,
            'recentGrupos'    => $recentGrupos,
        ]);
    }
}
