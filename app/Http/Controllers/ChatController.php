<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversa; 
use App\Models\User;

class ChatController extends Controller
{
    /**
     * Exibe a interface principal do chat.
     */
    public function index()
    {
        $utilizador = Auth::user();

        // Carrega as conversas em que o utilizador participa
        $conversas = $utilizador->conversasParticipadas()
                                ->with(['ultimaMensagem.emissor']) 
                                ->orderBy('created_at', 'desc')
                                ->get();
                                
        $outrosUtilizadores = User::where('id', '!=', $utilizador->id)
                                  ->orderBy('nome')
                                  ->get();

        // Passa conversas e outros utilizadores para a view
        return view('chat.index', compact('conversas', 'outrosUtilizadores'));
    }

    public function iniciarConversaDM(Request $request, $outroUtilizadorId)
    {
        $utilizadorAtual = Auth::user();
        $outroUtilizador = User::findOrFail($outroUtilizadorId);

        // 1. Verifica se já existe uma conversa DM entre os dois
        // Procura uma conversa do tipo 'dm' onde AMBOS participam
        $conversa = $utilizadorAtual->conversasParticipadas()
            ->where('tipo', 'dm')
            ->whereHas('participantes', function ($query) use ($outroUtilizadorId) {
                $query->where('utilizador_id', $outroUtilizadorId);
            }, '=', 1) 
            ->whereHas('participantes', null, '=', 2) 
            ->first();

        // 2. Se não existir, cria a conversa
        if (!$conversa) {
            $conversa = Conversa::create(['tipo' => 'dm']);
            
            // Adiciona ambos os utilizadores como participantes
            $conversa->participantes()->attach([$utilizadorAtual->id, $outroUtilizador->id]);
        }

        // 3. Redireciona para a interface do chat, selecionando a conversa
        return redirect()->route('chat.index')->with('conversa_ativa_id', $conversa->id); 
    }

}