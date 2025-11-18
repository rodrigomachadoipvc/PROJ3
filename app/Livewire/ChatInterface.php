<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Conversa;
use App\Models\Mensagem;
use Illuminate\Database\Eloquent\Collection;
// 'App\Events\NovaMensagemEnviada' já não é necessário

class ChatInterface extends Component
{
    public ?Conversa $conversaAtiva = null; 
    public Collection $mensagens; 
    public $outrosUtilizadores = [];
    public $conversas = [];
    public string $novaMensagem = ''; 

    // O 'getListeners()' e 'handleNovaMensagem()' foram removidos

    public function mount(Request $request)
    {
        $this->carregarPropriedadesIniciais();
        $conversaAtivaId = $request->session()->get('conversa_ativa_id');
        if ($conversaAtivaId) {
            $this->selecionarConversa($conversaAtivaId);
        }
        $request->session()->forget('conversa_ativa_id');
    }

    public function carregarPropriedadesIniciais()
    {
        $utilizador = Auth::user();
        $this->mensagens = new Collection(); 

        $this->conversas = $utilizador->conversasParticipadas()
                                ->with(['ultimaMensagem.emissor', 'participantes'])
                                ->orderBy('updated_at', 'desc')
                                ->get();
                                
        $this->outrosUtilizadores = User::where('id', '!=', $utilizador->id)
                                          ->orderBy('nome')
                                          ->get();
    }

    public function selecionarConversa($conversaId)
    {
        $utilizador = Auth::user();
        $conversa = $this->conversas->find($conversaId);

        if ($conversa) {
             $this->conversaAtiva = $conversa;
        } else {
            $this->conversaAtiva = $utilizador->conversasParticipadas()
                                            ->with(['participantes'])
                                            ->find($conversaId);
        }

        if ($this->conversaAtiva) {
            $this->mensagens = $this->conversaAtiva->mensagens()
                                       ->with('emissor')
                                       ->orderBy('created_at', 'asc')
                                       ->get();
        }
    }

    public function iniciarConversaDM($outroUtilizadorId)
    {
        $utilizadorAtual = Auth::user();
        $conversa = $utilizadorAtual->conversasParticipadas()
            ->where('tipo', 'dm')
            ->whereHas('participantes', function ($query) use ($outroUtilizadorId) {
                $query->where('utilizador_id', $outroUtilizadorId);
            }, '=', 1) 
            ->whereHas('participantes', null, '=', 2) 
            ->first();

        if (!$conversa) {
            $conversa = Conversa::create(['tipo' => 'dm']);
            $conversa->participantes()->attach([$utilizadorAtual->id, $outroUtilizadorId]);
            $this->carregarPropriedadesIniciais(); 
        }
        $this->selecionarConversa($conversa->id);
    }

    public function enviarMensagem()
    {
        $utilizador = Auth::user();
        if (empty($this->novaMensagem) || !$this->conversaAtiva) return;
        if (!$utilizador->conversasParticipadas->contains($this->conversaAtiva)) abort(403);

        $mensagem = DB::transaction(function () use ($utilizador) {
            $novaMsg = $this->conversaAtiva->mensagens()->create([
                'emissor_id' => $utilizador->id,
                'conteudo' => $this->novaMensagem,
            ]);
            $this->conversaAtiva->update(['ultima_mensagem_id' => $novaMsg->id]);
            return $novaMsg;
        });

        $this->mensagens->push($mensagem->load('emissor')); 
        $this->novaMensagem = '';
        $this->dispatch('limpar-input-chat');
        $this->recarregarBarraLateral();

        // A linha 'broadcast(new NovaMensagemEnviada($mensagem))->toOthers();' foi REMOVIDA.
    }

    public function render()
    {
        return view('livewire.chat-interface');
    }
}