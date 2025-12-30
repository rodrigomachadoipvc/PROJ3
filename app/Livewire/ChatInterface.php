<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Conversa;
use App\Models\Mensagem;
use App\Models\Anexo;
use Illuminate\Database\Eloquent\Collection;

class ChatInterface extends Component
{
    use WithFileUploads;

    public ?Conversa $conversaAtiva = null;
    public Collection $mensagens;
    public Collection $conversas;
    public Collection $outrosUtilizadores;
    
    public bool $mostrarMembrosGrupo = false;
    public ?Collection $membrosGrupo = null;

    public bool $mostrarNovaConversa = false;
    public string $searchUser = '';
    public Collection $utilizadoresFiltrados;

    public string $searchConversa = '';

    public string $filtroConversas = 'todas';

    public ?int $highlightMessageId = null;

    protected $listeners = [
        'mensagemRecebidaRealtime' => 'onMensagemRecebidaRealtime',
    ];

    public function mount(Request $request): void
    {
        $this->filtroConversas = 'todas';
        $this->carregarPropriedadesIniciais();
        $this->utilizadoresFiltrados = new Collection();

        $conversaAtivaId = $request->session()->get('conversa_ativa_id');
        if ($conversaAtivaId) {
            $this->selecionarConversa((int) $conversaAtivaId);
            $request->session()->forget('conversa_ativa_id');
        }
    }

    public string $novaMensagem = '';
    public array $uploads = [];

    protected function carregarPropriedadesIniciais(): void
    {
        $utilizador = Auth::user();

        $this->mensagens = new Collection();

        $this->conversas = $utilizador->conversasParticipadas()
            ->with(['ultimaMensagem.emissor', 'participantes', 'grupo'])
            ->withCount([
                'pins as is_pinned' => function ($q) {
                    $q->where('user_id', auth()->id());
                }
            ])
            ->orderByDesc('is_pinned')
            ->orderBy('updated_at', 'desc')
            ->get();

        $this->outrosUtilizadores = User::where('id', '!=', $utilizador->id)
            ->orderBy('nome')
            ->get();
    }

    public function selecionarConversa(int $conversaId, ?int $scrollToMessageId = null): void
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
                ->with(['emissor', 'anexos'])
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $this->mensagens = new Collection();
        }

        $this->novaMensagem = '';
        $this->uploads = [];
        $this->dispatch('reset-upload-input');

        $this->mostrarMembrosGrupo = false;
        $this->membrosGrupo = null;

        $this->highlightMessageId = $scrollToMessageId;

        if ($scrollToMessageId) {
            $this->dispatch('scroll-to-message', messageId: $scrollToMessageId);
        }

        session()->put('conversa_ativa_id', $conversaId);
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
            $this->fecharNovaConversa();
            $this->recarregarBarraLateral();
        }

        // chamada única
        $this->selecionarConversa($conversa->id);
    }

    public function updatedUploads(): void
    {
        $this->validate([
            'uploads' => ['array', 'max:5'],
            'uploads.*' => [
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,webp,pdf,zip,rar',
            ],
        ], [
            'uploads.max' => 'Máximo de 5 anexos por mensagem.',
            'uploads.*.max' => 'Um dos ficheiros excede 10MB.',
            'uploads.*.mimes' => 'Tipo de ficheiro não permitido.',
        ]);

        $totalBytes = 0;
        foreach ($this->uploads as $file) {
            $totalBytes += (int) $file->getSize();
        }

        if ($totalBytes > 25 * 1024 * 1024) {
            $this->addError('uploads', 'O total de anexos por mensagem não pode exceder 25MB.');
        }
    }

    public function enviarMensagem(): void
    {
        $utilizador = Auth::user();

        if (! $this->conversaAtiva) {
            return;
        }

        $temTexto = trim($this->novaMensagem) !== '';
        $temUploads = !empty($this->uploads);

        if (! $temTexto && ! $temUploads) {
            return;
        }

        $participa = $utilizador->conversasParticipadas()
            ->where('conversas.id', $this->conversaAtiva->id)
            ->exists();

        if (! $participa) {
            abort(403);
        }

        $this->validate([
            'uploads' => ['array', 'max:5'],
            'uploads.*' => [
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,webp,pdf,zip,rar',
            ],
        ]);

        $totalBytes = 0;
        foreach ($this->uploads as $file) {
            $totalBytes += (int) $file->getSize();
        }
        if ($totalBytes > 25 * 1024 * 1024) {
            $this->addError('uploads', 'O total de anexos por mensagem não pode exceder 25MB.');
            return;
        }

        DB::transaction(function () use ($utilizador, $temTexto) {
            $novaMsg = $this->conversaAtiva->mensagens()->create([
                'emissor_id' => $utilizador->id,
                'conteudo'   => $temTexto ? $this->novaMensagem : null,
            ]);

            app(\App\Services\AutoReplyService::class)->handle($novaMsg);

            if (!empty($this->uploads)) {
                foreach ($this->uploads as $file) {
                    $original = $file->getClientOriginalName();
                    $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
                    $filename = (string) Str::uuid() . '.' . $ext;
                    $path = "anexos/conversas/{$this->conversaAtiva->id}/{$filename}";

                    Storage::disk('public')->putFileAs(
                        dirname($path),
                        $file,
                        basename($path)
                    );

                    $novaMsg->anexos()->create([
                        'nome_original'   => $original,
                        'caminho_storage' => $path,
                        'mime_type'       => $file->getMimeType(),
                        'tamanho_bytes'   => $file->getSize(),
                    ]);
                }
            }

            $this->conversaAtiva->update(['ultima_mensagem_id' => $novaMsg->id]);
        });

        $this->mensagens = $this->conversaAtiva->mensagens()
            ->with(['emissor', 'anexos'])
            ->orderBy('created_at', 'asc')
            ->get();

        $this->recarregarBarraLateral();

        $this->novaMensagem = '';
        $this->uploads = [];
        $this->dispatch('reset-upload-input');
        $this->dispatch('limpar-input-chat');
        $this->dispatch('scroll-chat-para-fundo');
    }

    public function removerUpload(int $index): void
    {
        if (!isset($this->uploads[$index])) {
            return;
        }

        unset($this->uploads[$index]);
        $this->uploads = array_values($this->uploads);
    }

    public function recarregarBarraLateral(): void
    {
        $utilizador = Auth::user();
        $termo = trim($this->searchConversa);

        $query = $utilizador->conversasParticipadas()
            ->with(['ultimaMensagem.emissor', 'participantes', 'grupo'])
            ->withCount([
                'pins as is_pinned' => function ($q) {
                    $q->where('user_id', auth()->id());
                }
            ]);

        if ($termo !== '') {
            $matchedIdSub = Mensagem::query()
                ->select('id')
                ->whereColumn('conversa_id', 'conversas.id')
                ->whereNotNull('conteudo')
                ->where('conteudo', 'like', "%{$termo}%")
                ->orderByDesc('created_at')
                ->limit(1);

            $matchedBodySub = Mensagem::query()
                ->select('conteudo')
                ->whereColumn('conversa_id', 'conversas.id')
                ->whereNotNull('conteudo')
                ->where('conteudo', 'like', "%{$termo}%")
                ->orderByDesc('created_at')
                ->limit(1);

            $query->addSelect([
                'matched_message_id'   => $matchedIdSub,
                'matched_message_body' => $matchedBodySub,
            ]);

            $query->where(function ($q) use ($termo) {
                $q->whereHas('grupo', fn($q2) => $q2->where('nome', 'like', "%{$termo}%"))
                  ->orWhereHas('participantes', fn($q2) => $q2->where('nome', 'like', "%{$termo}%"))
                  ->orWhereHas('mensagens', fn($q2) => $q2->whereNotNull('conteudo')->where('conteudo', 'like', "%{$termo}%"));
            });
        }

        if ($this->filtroConversas === 'grupos') {
            $query->where('tipo', 'grupo');
        }
        if ($this->filtroConversas === 'favoritos') {
            $query->whereHas('pins', fn($q) => $q->where('user_id', auth()->id()));
        }

        $this->conversas = $query
            ->orderByDesc('is_pinned')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function setFiltroConversas(string $filtro): void
    {
        $permitidos = ['todas', 'grupos', 'favoritos'];
        $this->filtroConversas = in_array($filtro, $permitidos, true) ? $filtro : 'todas';
        $this->recarregarBarraLateral();
    }

    public function highlightTerm(?string $text, string $term): string
    {
        $text = (string) $text;
        $term = trim($term);

        if ($term === '' || $text === '') {
            return e($text);
        }

        $escaped = e($text);

        return preg_replace(
            '/' . preg_quote($term, '/') . '/i',
            '<mark class="bg-yellow-200 px-1 rounded">$0</mark>',
            $escaped
        ) ?? $escaped;
    }

    public function onMensagemRecebidaRealtime(array $payload): void
    {
        if (
            $payload['emissor_id'] === auth()->id() ||
            ($payload['emissor']['is_system'] ?? false)
        ) {
            return;
        }

        if ($this->mensagens->contains('id', $payload['id'])) {
            return;
        }

        $mensagem = Mensagem::with(['emissor', 'anexos'])->find($payload['id']);

        if (! $mensagem) {
            return;
        }

        $this->mensagens->push($mensagem);
        $this->dispatch('scroll-chat-para-fundo');
    }

    public function toggleMembrosGrupo(): void
    {
        $this->mostrarMembrosGrupo = ! $this->mostrarMembrosGrupo;

        if ($this->mostrarMembrosGrupo) {
            $this->carregarMembrosGrupo();
        }
    }

    protected function carregarMembrosGrupo(): void
    {
        if (! $this->conversaAtiva || $this->conversaAtiva->tipo !== 'grupo') {
            $this->membrosGrupo = null;
            return;
        }

        $this->membrosGrupo = $this->conversaAtiva
            ->grupo
            ?->membros()
            ->orderBy('nome')
            ->get();
    }

    public function togglePin(int $conversaId): void
    {
        $userId = auth()->id();

        $existe = \App\Models\ConversaPin::where('user_id', $userId)
            ->where('conversa_id', $conversaId)
            ->exists();

        if ($existe) {
            \App\Models\ConversaPin::where('user_id', $userId)
                ->where('conversa_id', $conversaId)
                ->delete();
        } else {
            \App\Models\ConversaPin::create([
                'user_id' => $userId,
                'conversa_id' => $conversaId,
            ]);
        }

        $this->recarregarBarraLateral();
    }

    public function abrirNovaConversa(): void
    {
        $this->mostrarNovaConversa = true;
        $this->searchUser = '';
        $this->carregarUtilizadoresParaNovaConversa();
    }

    public function fecharNovaConversa(): void
    {
        $this->mostrarNovaConversa = false;
        $this->searchUser = '';
        $this->utilizadoresFiltrados = new Collection();
    }

    public function updatedSearchUser(): void
    {
        if (!$this->mostrarNovaConversa) return;
        $this->carregarUtilizadoresParaNovaConversa();
    }

    protected function carregarUtilizadoresParaNovaConversa(): void
    {
        $me = auth()->id();
        $termo = trim($this->searchUser);

        $q = User::query()
            ->where('id', '!=', $me)
            ->orderBy('nome');

        if ($termo !== '') {
            $q->where('nome', 'like', "%{$termo}%");
        }

        $this->utilizadoresFiltrados = $q->limit(50)->get();
    }

    public function updatedSearchConversa(): void
    {
        $this->recarregarBarraLateral();
    }

    public function render()
    {
        return view('livewire.chat-interface');
    }
}