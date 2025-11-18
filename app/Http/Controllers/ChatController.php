<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Conversa;
use App\Models\Mensagem; 

class ChatController extends Controller
{
    /**
     * Exibe a interface principal do chat.
     * Carrega as conversas, outros utilizadores, e a conversa ativa (se selecionada).
     */
    public function index(Request $request)
    {
        
        return view('chat.index');
    }

    /**
     * Inicia ou recupera uma conversa DM com outro utilizador.
     */
    public function iniciarConversaDM(Request $request, $outroUtilizadorId)
    {
        $utilizadorAtual = Auth::user();
        $outroUtilizador = User::findOrFail($outroUtilizadorId);

        $conversa = $utilizadorAtual->conversasParticipadas()
            ->where('tipo', 'dm')
            ->whereHas('participantes', function ($query) use ($outroUtilizadorId) {
                $query->where('utilizador_id', $outroUtilizadorId);
            }, '=', 1) 
            ->whereHas('participantes', null, '=', 2) 
            ->first();

        if (!$conversa) {
            $conversa = Conversa::create(['tipo' => 'dm']);
            $conversa->participantes()->attach([$utilizadorAtual->id, $outroUtilizador->id]);
        }

        return redirect()->route('chat.index')
                         ->with('conversa_ativa_id', $conversa->id); 
    }
}