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
        // Dashboard passa parâmetros de query para abrir diretamente uma conversa/grupo:
        //  - /chat?c=<conversa_id>
        //  - /chat?g=<grupo_id>
        // O Livewire ChatInterface já implementa o "open" via session('conversa_ativa_id').
        // Aqui apenas fazemos o "wiring" mínimo para converter query -> sessão.

        // Se vierem parâmetros de query (c/g), limpamos a URL via redirect para /chat
        // depois de colocarmos (se possível) a conversa ativa na sessão.
        $temQueryC = $request->query->has('c');
        $temQueryG = $request->query->has('g');

        $conversaId = (int) $request->query('c', 0);

        if ($conversaId <= 0) {
            $grupoId = (int) $request->query('g', 0);

            if ($grupoId > 0) {
                $conversaId = (int) Conversa::query()
                    ->where('tipo', 'grupo')
                    ->where('grupo_id', $grupoId)
                    ->value('id');
            }
        }

        if ($conversaId > 0) {
            // Usa a mesma chave que o ChatInterface::mount já consome.
            $request->session()->put('conversa_ativa_id', $conversaId);
        }

        // ✅ URL limpa: /chat (sem ?c=... ou ?g=...)
        if ($temQueryC || $temQueryG) {
            return redirect()->route('chat.index');
        }

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

        if ($conversa) {
            foreach ([$utilizadorAtual->id, $outroUtilizadorId] as $id) {
                $pivot = $conversa->participantes()->where('utilizador_id', $id)->first()->pivot;
                $entrou_em = $pivot->entrou_em ?? now();
                $conversa->participantes()->updateExistingPivot($id, [
                    'entrou_em' => $entrou_em,
                    'saiu_em' => null,
                ]);
            }
        } else {
            $conversa = Conversa::create(['tipo' => 'dm']);
            $conversa->participantes()->attach([
                $utilizadorAtual->id => ['entrou_em' => now(), 'saiu_em' => null],
                $outroUtilizador->id => ['entrou_em' => now(), 'saiu_em' => null],
            ]);
        }

        return redirect()->route('chat.index')
                         ->with('conversa_ativa_id', $conversa->id); 
    }
}
