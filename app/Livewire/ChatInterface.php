<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Conversa;
use Illuminate\Database\Eloquent\Collection;

class ChatInterface extends Component
{
    public ?Conversa $conversaAtiva = null;
    public Collection $mensagens;
    public Collection $conversas;
    public Collection $outrosUtilizadores;
    public string $novaMensagem = '';

    public function mount(Request $request): void
    {
        $this->carregarPropriedadesIniciais();

        // Se vier um ID de conversa da sessão (ex.: iniciar DM via controller)
        $conversaAtivaId = $request->session()->get('conversa_ativa_id');
        if ($conversaAtivaId) {
            $this->selecionarConversa($conversaAtivaId);
            $request->session()->forget('conversa_ativa_id');
        }
    }

    protected function carregarPropriedadesIniciais(): void
    {
        $utilizador = Auth::user();

        $this->mensagens = new Collection();

        // Conversas em que o utilizador participa (DM + grupos, já preparado para grupos)
        $this->conversas = $utilizador->conversasParticipadas()
            ->with(['ultimaMensagem.emissor', 'participantes', 'grupo'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Lista de outros utilizadores para iniciar DM
        $this->outrosUtilizadores = User::where('id', '!=', $utilizador->id)
            ->orderBy('nome')
            ->get();
    }

    public function selecionarConversa(int $conversaId): void
    {
        $utilizador = Auth::user();

        // Primeiro tenta encontrar na coleção já carregada
        $conversa = $this->conversas->firstWhere('id', $conversaId);

        // Se não estiver na coleção (por ex. acabou de ser criada), carrega da BD
        if (! $conversa) {
            $conversa = $utilizador->conversasParticipadas()
                ->with(['participantes', 'grupo'])
                ->find($conversaId);
        }

        $this->conversaAtiva = $conversa;

        if ($this->conversaAtiva) {
            $this->mensagens = $this->conversaAtiva->mensagens()
                ->with('emissor')
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $this->mensagens = new Collection();
        }
    }

    public function iniciarConversaDM(int $outroUtilizadorId): void
    {
        $utilizadorAtual = Auth::user();

        // Procura DM existente entre estes 2 utilizadores
        $conversa = $utilizadorAtual->conversasParticipadas()
            ->where('tipo', 'dm')
            ->whereHas('participantes', function ($query) use ($outroUtilizadorId) {
                $query->where('utilizador_id', $outroUtilizadorId);
            }, '=', 1)
            ->whereHas('participantes', null, '=', 2)
            ->first();

        // Se não existir, cria uma nova conversa DM
        if (! $conversa) {
            $conversa = Conversa::create(['tipo' => 'dm']);
            $conversa->participantes()->attach([$utilizadorAtual->id, $outroUtilizadorId]);

            // Atualiza a barra lateral com a nova conversa
            $this->recarregarBarraLateral();
        }

        $this->selecionarConversa($conversa->id);
    }

    public function enviarMensagem(): void
    {
        $utilizador = Auth::user();

        if (! $this->conversaAtiva || trim($this->novaMensagem) === '') {
            return;
        }

        // Segurança: garantir que o utilizador participa nesta conversa
        $participa = $utilizador->conversasParticipadas()
            ->where('conversas.id', $this->conversaAtiva->id)
            ->exists();

        if (! $participa) {
            abort(403);
        }

        DB::transaction(function () use ($utilizador) {
            $novaMsg = $this->conversaAtiva->mensagens()->create([
                'emissor_id' => $utilizador->id,
                'conteudo'   => $this->novaMensagem,
            ]);

            // Atualizar a referência à última mensagem na conversa
            $this->conversaAtiva->update(['ultima_mensagem_id' => $novaMsg->id]);
        });

        // Recarrega a lista de mensagens para forçar o Livewire a atualizar o DOM
        $this->mensagens = $this->conversaAtiva->mensagens()
            ->with('emissor')
            ->orderBy('created_at', 'asc')
            ->get();

        // Atualiza a barra lateral (última mensagem / timestamps)
        $this->recarregarBarraLateral();

        // Limpa o input
        $this->novaMensagem = '';
        $this->dispatch('limpar-input-chat');
    }

    public function recarregarBarraLateral(): void
    {
        $utilizador = Auth::user();

        $this->conversas = $utilizador->conversasParticipadas()
            ->with(['ultimaMensagem.emissor', 'participantes', 'grupo'])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.chat-interface');
    }
}
