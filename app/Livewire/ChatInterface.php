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
use App\Events\NovaMensagemEnviada;

class ChatInterface extends Component
{
    public ?Conversa $conversaAtiva = null;
    public Collection $mensagens;
    public Collection $conversas;
    public Collection $outrosUtilizadores;
    public string $novaMensagem = '';

    protected $listeners = [
        'mensagemRecebidaRealtime' => 'mensagemRecebidaRealtime',
    ];

    public function mount(Request $request): void
    {
        $this->carregarPropriedadesIniciais();

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

        $this->conversas = $utilizador->conversasParticipadas()
            ->with(['ultimaMensagem.emissor', 'participantes', 'grupo'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $this->outrosUtilizadores = User::where('id', '!=', $utilizador->id)
            ->orderBy('nome')
            ->get();
    }

    public function selecionarConversa(int $conversaId): void
    {
        $utilizador = Auth::user();

        $conversa = $this->conversas->firstWhere('id', $conversaId);

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

            // Diz ao JS para subscrever o canal desta conversa
            $this->dispatch('conversa-selecionada', conversaId: $this->conversaAtiva->id);
            // Scroll para o fundo
            $this->dispatch('scroll-chat-para-fundo');
        } else {
            $this->mensagens = new Collection();
        }
    }

    public function iniciarConversaDM(int $outroUtilizadorId): void
    {
        $utilizadorAtual = Auth::user();

        $conversa = $utilizadorAtual->conversasParticipadas()
            ->where('tipo', 'dm')
            ->whereHas('participantes', function ($query) use ($outroUtilizadorId) {
                $query->where('utilizador_id', $outroUtilizadorId);
            }, '=', 1)
            ->whereHas('participantes', null, '=', 2)
            ->first();

        if (! $conversa) {
            $conversa = Conversa::create(['tipo' => 'dm']);
            $conversa->participantes()->attach([$utilizadorAtual->id, $outroUtilizadorId]);

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

        $participa = $utilizador->conversasParticipadas()
            ->where('conversas.id', $this->conversaAtiva->id)
            ->exists();

        if (! $participa) {
            abort(403);
        }

        $novaMsg = null;

        DB::transaction(function () use ($utilizador, &$novaMsg) {
            $novaMsg = $this->conversaAtiva->mensagens()->create([
                'emissor_id' => $utilizador->id,
                'conteudo'   => $this->novaMensagem,
            ]);

            $this->conversaAtiva->update(['ultima_mensagem_id' => $novaMsg->id]);
        });

        // Atualiza UI do remetente (Livewire)
        $this->mensagens = $this->conversaAtiva->mensagens()
            ->with('emissor')
            ->orderBy('created_at', 'asc')
            ->get();

        $this->recarregarBarraLateral();

        // Broadcast para os outros utilizadores (Realtime)
        // toOthers() evita duplicar no remetente
        broadcast(new NovaMensagemEnviada($novaMsg))->toOthers();

        $this->novaMensagem = '';
        $this->dispatch('limpar-input-chat');
        $this->dispatch('scroll-chat-para-fundo');
    }

    /**
     * Recebe uma mensagem do Realtime (vinda do JS/Echo)
     */
    public function mensagemRecebidaRealtime(array $payload): void
    {
        if (! $this->conversaAtiva) {
            return;
        }

        $mensagem = $payload['mensagem'] ?? null;
        if (! $mensagem) {
            return;
        }

        // Só interessa se for da conversa aberta
        if ((int)($mensagem['conversa_id'] ?? 0) !== (int)$this->conversaAtiva->id) {
            // Mesmo assim, convém atualizar a barra lateral (última mensagem)
            $this->recarregarBarraLateral();
            return;
        }

        // Evitar duplicados
        $existe = $this->mensagens->firstWhere('id', $mensagem['id'] ?? null);
        if ($existe) {
            return;
        }

        // Recarregar mensagens com relações (mais seguro do que “inventar” modelo aqui)
        $this->mensagens = $this->conversaAtiva->mensagens()
            ->with('emissor')
            ->orderBy('created_at', 'asc')
            ->get();

        $this->recarregarBarraLateral();
        $this->dispatch('scroll-chat-para-fundo');
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
