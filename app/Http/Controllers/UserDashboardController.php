<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $recentConversas = $user->conversasAtivas()
            ->with(['ultimaMensagem.emissor', 'grupo', 'participantes'])
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        $recentGrupos = $user->gruposAtivos()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('dashboard.user', [
            'user'            => $user,
            'recentConversas' => $recentConversas,
            'recentGrupos'    => $recentGrupos,
        ]);
    }
}
