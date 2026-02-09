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
use App\Models\Grupo;
use App\Services\SystemConversationService;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ChatInterface extends Component
{
    use WithFileUploads;
    use AuthorizesRequests;

    public ?Conversa $conversaAtiva = null;
    public Collection $mensagens;
    public Collection $conversas;
    public Collection $outrosUtilizadores;

    public ?Conversa $conversaSistema = null;
    public int $mensagensPorDefeito = 80;

    public bool $mostrarMembrosGrupo = false;
    public ?Collection $membrosGrupo = null;

    public bool $mostrarNovaConversa = false;
    public string $searchUser = '';
    public Collection $utilizadoresFiltrados;

    public string $searchConversa = '';

    public string $filtroConversas = 'recentes';

    public ?int $highlightMessageId = null;

    protected $listeners = [
        'mensagemRecebidaRealtime' => 'onMensagemRecebidaRealtime',
        'mensagemRecebidaRealtimeSidebar' => 'onMensagemRecebidaRealtimeSidebar',
        'clearHighlight' => 'clearHighlight',
    ];

    public bool $mostrarEditarGrupo = false;
    public string $novoNomeGrupo = '';
    public $fotoGrupoUpload = null;
    public bool $mostrarAdicionarMembro = false;
    public string $searchMember = '';
    public Collection $utilizadoresParaAdicionar;
    public ?int $grupoIdAtivo = null;
    public array $rolesPermitidos = ['member', 'moderator'];
    public ?int $selectedUserToAdd = null;
    public Collection $usersDisponiveisAdicionar;

    public function mount(Request $request): void
    {
        // Default: "recentes" (compatível com sessões antigas "todas")
        $storedFiltro = session()->get('filtro_conversas', 'recentes');
        if ($storedFiltro === 'todas') {
            $storedFiltro = 'recentes';
        }

        $permitidos = ['recentes', 'grupos', 'favoritos'];
        $this->filtroConversas = in_array($storedFiltro, $permitidos, true) ? $storedFiltro : 'recentes';
        session()->put('filtro_conversas', $this->filtroConversas);

        $this->carregarPropriedadesIniciais();
        $this->utilizadoresFiltrados = new Collection();
        $this->utilizadoresParaAdicionar = new Collection();
        $this->usersDisponiveisAdicionar = new Collection();

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

        $this->carregarConversaSistema(true);

        $this->mensagens = new Collection();

        $dms = $utilizador->conversasParticipadas()
            ->where('tipo', 'dm')
            ->with(['ultimaMensagem.emissor', 'participantes', 'grupo'])
            ->withCount([
                'pins as is_pinned' => function ($q) {
                    $q->where('user_id', auth()->id());
                }
            ])
            ->get();

        $grupos = Conversa::where('tipo', 'grupo')
            ->whereHas('grupo.membrosAtivos', function ($q) use ($utilizador) {
                $q->where('users.id', $utilizador->id);
            })
            ->with(['ultimaMensagem.emissor', 'participantes', 'grupo'])
            ->withCount([
                'pins as is_pinned' => function ($q) {
                    $q->where('user_id', auth()->id());
                }
            ])
            ->get();

        $merged = $dms->merge($grupos)->unique('id');

        $merged = $this->removerSistemaDaLista($merged);

        // Ordenação SEM prioridade de favoritos/pins: sempre por atividade mais recente
        $this->conversas = $merged->sort(function ($a, $b) {
            $aTs = $this->conversaLastActivityTs($a);
            $bTs = $this->conversaLastActivityTs($b);
            if ($aTs !== $bTs) {
                return $bTs - $aTs;
            }
            // Tie-break por ultima_mensagem_id desc
            $aId = (int) $a->ultima_mensagem_id ?? 0;
            $bId = (int) $b->ultima_mensagem_id ?? 0;
            return $bId - $aId;
        })->values();

        $this->outrosUtilizadores = User::where('id', '!=', $utilizador->id)
            ->orderBy('nome')
            ->get();

        $this->dispatchConversasIds();
    }

    private function conversaLastActivityTs($conversa): int
    {
        return $conversa->ultimaMensagem?->created_at?->timestamp ?? $conversa->updated_at?->timestamp ?? 0;
    }

    protected function carregarConversaSistema(bool $forcarEnsure = false): void
    {
        $user = Auth::user();

        if (! $user || ($user->is_system ?? false)) {
            $this->conversaSistema = null;
            return;
        }

        // Se já temos a conversa em memória e não forçamos ensure, apenas refrescamos os relacionamentos
        if (! $forcarEnsure && $this->conversaSistema) {
            $this->conversaSistema = Conversa::with(['ultimaMensagem.emissor', 'participantes', 'grupo'])
                ->find($this->conversaSistema->id);
            return;
        }

        $conversa = app(SystemConversationService::class)->ensureForUser($user);

        if (! $conversa) {
            $this->conversaSistema = null;
            return;
        }

        $this->conversaSistema = Conversa::with(['ultimaMensagem.emissor', 'participantes', 'grupo'])
            ->find($conversa->id);
    }

    protected function removerSistemaDaLista(Collection $conversas): Collection
    {
        if (! $this->conversaSistema) {
            return $conversas;
        }

        $sid = (int) $this->conversaSistema->id;

        return $conversas
            ->reject(fn ($c) => (int) $c->id === $sid)
            ->values();
    }

    protected function carregarMensagensAtivas(?int $anchorMessageId = null): void
    {
        if (! $this->conversaAtiva) {
            $this->mensagens = new Collection();
            return;
        }

        $limit = max(20, (int) $this->mensagensPorDefeito);
        $conversaId = (int) $this->conversaAtiva->id;

        // Se houver âncora (pesquisa), tentamos garantir que a mensagem alvo está dentro do lote carregado
        if ($anchorMessageId) {
            $anchor = Mensagem::where('conversa_id', $conversaId)->find($anchorMessageId);

            if ($anchor) {
                $antes = (int) floor(($limit - 1) / 2);
                $depois = $limit - 1 - $antes;

                $idsAntes = Mensagem::where('conversa_id', $conversaId)
                    ->where('created_at', '<=', $anchor->created_at)
                    ->orderByDesc('created_at')
                    ->limit($antes + 1) // inclui a âncora
                    ->pluck('id');

                $idsDepois = Mensagem::where('conversa_id', $conversaId)
                    ->where('created_at', '>', $anchor->created_at)
                    ->orderBy('created_at')
                    ->limit($depois)
                    ->pluck('id');

                $ids = $idsAntes->merge($idsDepois)->unique()->values();

                $this->mensagens = Mensagem::with(['emissor', 'anexos'])
                    ->whereIn('id', $ids)
                    ->orderBy('created_at', 'asc')
                    ->get();

                return;
            }
        }

        // Default: últimas N mensagens (render em ordem ascendente)
        $this->mensagens = $this->conversaAtiva->mensagens()
            ->with(['emissor', 'anexos'])
            // Nota: a relação Conversa::mensagens() já tem orderBy('created_at','asc').
            // Aqui queremos as ÚLTIMAS N mensagens, por isso limpamos a ordenação da relação.
            ->reorder()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    public function hydrate(): void
    {
        if (Auth::check()) {
            $this->recarregarBarraLateral();
            $this->carregarConversaSistema(false);
        }

        if ($this->conversaAtiva) {
            $this->conversaAtiva = Conversa::with(['participantes', 'grupo', 'ultimaMensagem.emissor'])->find($this->conversaAtiva->id);
            if ($this->conversaAtiva) {
                $this->carregarMensagensAtivas($this->highlightMessageId);
            } else {
                $this->conversaAtiva = null;
                $this->mensagens = new Collection();
            }
        }
    }

    private function ensureParticipanteAtivo(Conversa $conversa, int $userId): void
    {
        $pivot = $conversa->participantes()->where('utilizador_id', $userId)->first();

        if ($pivot) {
            $updates = [];
            if ($pivot->pivot->saiu_em !== null) {
                $updates['saiu_em'] = null;
            }
            if ($pivot->pivot->entrou_em === null) {
                $updates['entrou_em'] = now();
            }
            if (!empty($updates)) {
                $conversa->participantes()->updateExistingPivot($userId, $updates);
            }
        } else {
            $conversa->participantes()->attach($userId, [
                'entrou_em' => now(),
                'saiu_em' => null,
            ]);
        }
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

        if (! $conversa) {
            $this->conversaAtiva = null;
            $this->mensagens = new Collection();
            return;
        }

        if ($conversa->tipo === 'dm') {
            $this->ensureParticipanteAtivo($conversa, auth()->id());
        } elseif ($conversa->tipo === 'grupo' && $conversa->grupo) {
            if (!$conversa->grupo->membrosAtivos()->where('users.id', auth()->id())->exists()) {
                $this->conversaAtiva = null;
                $this->mensagens = new Collection();
                return;
            } else {
                $this->ensureParticipanteAtivo($conversa, auth()->id());
            }
        }

        $this->conversaAtiva = $conversa;

        if ($this->conversaAtiva) {
            $this->carregarMensagensAtivas($scrollToMessageId);
        } else {
            $this->mensagens = new Collection();
        }

        $this->novaMensagem = '';
        $this->uploads = [];
        $this->dispatch('reset-upload-input');

        $this->mostrarMembrosGrupo = false;
        $this->membrosGrupo = null;

        $this->highlightMessageId = $scrollToMessageId;

        // Realtime: subscrição do canal no browser
        $this->dispatch('conversa-selecionada', conversaId: $conversaId);

        if ($scrollToMessageId) {
            $this->dispatch('scroll-to-message', messageId: $scrollToMessageId);
        } else {
            $this->dispatch('scroll-chat-para-fundo');
        }

        session()->put('conversa_ativa_id', $conversaId);

        $this->recarregarBarraLateral();
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
                $outroUtilizadorId => ['entrou_em' => now(), 'saiu_em' => null],
            ]);
        }

        $this->fecharNovaConversa();
        $this->recarregarBarraLateral();

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

        $participa = false;
        if ($this->conversaAtiva->tipo === 'dm') {
            $participa = $this->conversaAtiva->participantes()->where('utilizador_id', auth()->id())->exists();
            if ($participa) {
                $this->ensureParticipanteAtivo($this->conversaAtiva, auth()->id());
            }
        } elseif ($this->conversaAtiva->tipo === 'grupo' && $this->conversaAtiva->grupo) {
            $participa = $this->conversaAtiva->grupo->membrosAtivos()->where('users.id', auth()->id())->exists();
            if ($participa) {
                $this->ensureParticipanteAtivo($this->conversaAtiva, auth()->id());
            }
        }

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

        $novaMsg = null;
        DB::transaction(function () use ($utilizador, $temTexto, &$novaMsg) {
            $novaMsg = $this->conversaAtiva->mensagens()->create([
                'emissor_id' => $utilizador->id,
                'conteudo'   => $temTexto ? $this->novaMensagem : null,
            ]);

            try {
                app(\App\Services\AutoReplyService::class)->handle($novaMsg);
            } catch (\Throwable $e) {
                report($e);
            }

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
        });

        // Push manual para $mensagens para evitar recarregar tudo e perda de state
        if ($novaMsg) {
            $novaMsg->load(['emissor', 'anexos']);
            $this->mensagens->push($novaMsg);
            $limite = max(20, (int) $this->mensagensPorDefeito);
            if ($this->mensagens->count() > $limite) {
                $this->mensagens = $this->mensagens->slice(-$limite)->values();
            }
        }

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
        $this->carregarConversaSistema(false);
        $termo = trim($this->searchConversa);

        $dmsQuery = $utilizador->conversasParticipadas()
            ->where('tipo', 'dm')
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

            $matchedParticipantSub = User::query()
                ->select('users.nome')
                ->join('participantes_conversa', 'users.id', '=', 'participantes_conversa.utilizador_id')
                ->whereColumn('participantes_conversa.conversa_id', 'conversas.id')
                ->where('users.nome', 'like', "%{$termo}%")
                ->orderBy('users.nome')
                ->limit(1);

            $matchedGroupSub = Grupo::query()
                ->select('grupos.nome')
                ->whereColumn('grupos.id', 'conversas.grupo_id')
                ->where('grupos.nome', 'like', "%{$termo}%")
                ->limit(1);

            $dmsQuery->addSelect([
                'matched_message_id'   => $matchedIdSub,
                'matched_message_body' => $matchedBodySub,
                'matched_participant_name' => $matchedParticipantSub,
                'matched_group_name' => $matchedGroupSub,
            ]);

            $dmsQuery->where(function ($q) use ($termo) {
                $q->whereHas('grupo', fn($q2) => $q2->where('nome', 'like', "%{$termo}%"))
                  ->orWhereHas('participantes', fn($q2) => $q2->where('nome', 'like', "%{$termo}%"))
                  ->orWhereHas('mensagens', fn($q2) => $q2->whereNotNull('conteudo')->where('conteudo', 'like', "%{$termo}%"));
            });
        }

        $gruposQuery = Conversa::where('tipo', 'grupo')
            ->whereHas('grupo.membrosAtivos', function ($q) use ($utilizador) {
                $q->where('users.id', $utilizador->id);
            })
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

            $matchedParticipantSub = User::query()
                ->select('users.nome')
                ->join('participantes_conversa', 'users.id', '=', 'participantes_conversa.utilizador_id')
                ->whereColumn('participantes_conversa.conversa_id', 'conversas.id')
                ->where('users.nome', 'like', "%{$termo}%")
                ->orderBy('users.nome')
                ->limit(1);

            $matchedGroupSub = Grupo::query()
                ->select('grupos.nome')
                ->whereColumn('grupos.id', 'conversas.grupo_id')
                ->where('grupos.nome', 'like', "%{$termo}%")
                ->limit(1);

            $gruposQuery->addSelect([
                'matched_message_id'   => $matchedIdSub,
                'matched_message_body' => $matchedBodySub,
                'matched_participant_name' => $matchedParticipantSub,
                'matched_group_name' => $matchedGroupSub,
            ]);

            $gruposQuery->where(function ($q) use ($termo) {
                $q->whereHas('grupo', fn($q2) => $q2->where('nome', 'like', "%{$termo}%"))
                  ->orWhereHas('participantes', fn($q2) => $q2->where('nome', 'like', "%{$termo}%"))
                  ->orWhereHas('mensagens', fn($q2) => $q2->whereNotNull('conteudo')->where('conteudo', 'like', "%{$termo}%"));
            });
        }

        if ($this->filtroConversas === 'grupos') {
            $dms = collect();
            $grupos = $gruposQuery->get();
        } elseif ($this->filtroConversas === 'favoritos') {
            $dmsQuery->whereHas('pins', fn($q) => $q->where('user_id', auth()->id()));
            $gruposQuery->whereHas('pins', fn($q) => $q->where('user_id', auth()->id()));
            $dms = $dmsQuery->get();
            $grupos = $gruposQuery->get();
        } else {
            // "recentes" (default): tudo
            $dms = $dmsQuery->get();
            $grupos = $gruposQuery->get();
        }

        $merged = $dms->merge($grupos)->unique('id');

        $merged = $this->removerSistemaDaLista($merged);

        // Ordenação SEM prioridade de favoritos/pins: sempre por atividade mais recente
        $this->conversas = $merged->sort(function ($a, $b) {
            $aTs = $this->conversaLastActivityTs($a);
            $bTs = $this->conversaLastActivityTs($b);
            if ($aTs !== $bTs) {
                return $bTs - $aTs;
            }
            // Tie-break por ultima_mensagem_id desc
            $aId = (int) $a->ultima_mensagem_id ?? 0;
            $bId = (int) $b->ultima_mensagem_id ?? 0;
            return $bId - $aId;
        })->values();

        $this->dispatchConversasIds();
    }

    public function setFiltroConversas(string $filtro): void
    {
        // Compatibilidade: "todas" passa a "recentes"
        if ($filtro === 'todas') {
            $filtro = 'recentes';
        }

        $permitidos = ['recentes', 'grupos', 'favoritos'];
        $this->filtroConversas = in_array($filtro, $permitidos, true) ? $filtro : 'recentes';

        session()->put('filtro_conversas', $this->filtroConversas);
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

    public function highlightTermUnderline(?string $text, string $term): string
    {
        $text = (string) $text;
        $term = trim($term);

        if ($term === '' || $text === '') {
            return e($text);
        }

        $escaped = e($text);

        return preg_replace(
            '/' . preg_quote($term, '/') . '/i',
            '<span class="underline decoration-yellow-400 decoration-2 underline-offset-4">$0</span>',
            $escaped
        ) ?? $escaped;
    }

    public function clearHighlight(): void
    {
        $this->highlightMessageId = null;
    }

    public function onMensagemRecebidaRealtime(array $payload): void
    {
        // ignora apenas sistema (mantém UI limpa)
        if (($payload['emissor']['is_system'] ?? false)) {
            return;
        }

        if ($this->mensagens->contains('id', $payload['id'] ?? null)) {
            return;
        }

        $mensagem = Mensagem::with(['emissor', 'anexos'])->find($payload['id'] ?? null);
        if (! $mensagem) {
            return;
        }

        $this->mensagens->push($mensagem);

        $limite = max(20, (int) $this->mensagensPorDefeito);
        if ($this->mensagens->count() > $limite) {
            $this->mensagens = $this->mensagens->slice(-$limite)->values();
        }

        // **Crítico**: refresca sidebar para ordenar e atualizar snippet/hora
        $this->recarregarBarraLateral();

        $this->dispatch('scroll-chat-para-fundo');
    }

    public function onMensagemRecebidaRealtimeSidebar(array $payload): void
    {
        if (($payload['emissor']['is_system'] ?? false)) {
            return;
        }

        $conversaId = (int)($payload['conversa_id'] ?? 0);

        // Se o broadcast não vier com conversa_id, faz fallback ao DB
        if ($conversaId <= 0 && isset($payload['id'])) {
            $conversaId = (int) Mensagem::whereKey($payload['id'])->value('conversa_id');
        }

        if ($conversaId <= 0) {
            return;
        }

        // Se for a conversa ativa, reaproveita o handler que injeta na UI do chat
        if ($this->conversaAtiva && (int)$this->conversaAtiva->id === $conversaId) {
            $this->onMensagemRecebidaRealtime($payload);
            return;
        }

        // Caso contrário, basta refrescar sidebar (ordenar e atualizar snippet/hora)
        $this->recarregarBarraLateral();
    }

    public function toggleMembrosGrupo(): void
    {
        $this->mostrarMembrosGrupo = ! $this->mostrarMembrosGrupo;

        if ($this->mostrarMembrosGrupo) {
            $this->carregarMembrosGrupo();
        }

        $this->recarregarBarraLateral();
    }

    protected function carregarMembrosGrupo(): void
    {
        if (! $this->conversaAtiva || $this->conversaAtiva->tipo !== 'grupo') {
            $this->membrosGrupo = null;
            return;
        }

        $grupo = $this->conversaAtiva->grupo;
        if (! $grupo) {
            $this->membrosGrupo = null;
            return;
        }

        $this->membrosGrupo = $grupo->membrosAtivos()
            ->withPivot(['role', 'left_at'])
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

        if (mb_strlen($termo) < 2) {
            $this->utilizadoresFiltrados = new Collection();
            return;
        }

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

    public function openEditarGrupo()
    {
        if (!$this->conversaAtiva || $this->conversaAtiva->tipo !== 'grupo') {
            return;
        }

        $grupo = $this->conversaAtiva->grupo;

        if (!$grupo) {
            return;
        }

        if (!$this->userPodeGerirGrupo()) {
            abort(403);
        }

        $this->novoNomeGrupo = $grupo->nome;
        $this->mostrarEditarGrupo = true;
    }

    public function cancelarEditarGrupo()
    {
        $this->mostrarEditarGrupo = false;
        $this->novoNomeGrupo = '';
        $this->fotoGrupoUpload = null;
    }

    public function guardarNomeGrupo()
    {
        if (!$this->conversaAtiva || $this->conversaAtiva->tipo !== 'grupo') {
            return;
        }

        $grupo = $this->conversaAtiva->grupo;

        if (!$grupo) {
            return;
        }

        if (!$this->userPodeGerirGrupo()) {
            abort(403);
        }

        $this->validate([
            'novoNomeGrupo' => ['required', 'string', 'min:2', 'max:60'],
        ]);

        $grupo->update(['nome' => $this->novoNomeGrupo]);

        $this->recarregarBarraLateral();

        $this->cancelarEditarGrupo();
    }

    public function updatedFotoGrupoUpload()
    {
        $this->validate([
            'fotoGrupoUpload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }

    public function guardarFotoGrupo()
    {
        if (!$this->conversaAtiva || $this->conversaAtiva->tipo !== 'grupo') {
            return;
        }

        $grupo = $this->conversaAtiva->grupo;

        if (!$grupo) {
            return;
        }

        if (!$this->userPodeGerirGrupo()) {
            abort(403);
        }

        if ($this->fotoGrupoUpload) {
            $ext = $this->fotoGrupoUpload->getClientOriginalExtension();
            $filename = 'foto_' . Str::uuid() . '.' . $ext;
            $path = "grupos/{$grupo->id}/{$filename}";

            Storage::disk('public')->putFileAs(
                dirname($path),
                $this->fotoGrupoUpload,
                $filename
            );

            $grupo->update(['foto_path' => $path]);
        }

        $this->recarregarBarraLateral();

        $this->cancelarEditarGrupo();
    }

    public function guardarGrupo(): void
    {
        if (!$this->conversaAtiva || $this->conversaAtiva->tipo !== 'grupo') return;

        $grupo = $this->conversaAtiva->grupo;
        if (!$grupo) return;

        if (!$this->userPodeGerirGrupo()) abort(403);

        $this->validate([
            'novoNomeGrupo'   => ['required', 'string', 'min:2', 'max:60'],
            'fotoGrupoUpload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $updates = [];

        if ($this->novoNomeGrupo !== $grupo->nome) {
            $updates['nome'] = $this->novoNomeGrupo;
        }

        if ($this->fotoGrupoUpload) {
            $ext = strtolower($this->fotoGrupoUpload->getClientOriginalExtension() ?: 'jpg');
            $filename = 'foto_' . Str::uuid() . '.' . $ext;
            $path = "grupos/{$grupo->id}/{$filename}";

            Storage::disk('public')->putFileAs("grupos/{$grupo->id}", $this->fotoGrupoUpload, $filename);

            $updates['foto_path'] = $path;
        }

        if (!empty($updates)) {
            $grupo->update($updates);
        }

        $this->recarregarBarraLateral();
        $this->cancelarEditarGrupo();
    }

    protected function carregarUsersDisponiveisAdicionar(): void
    {
        if (! $this->conversaAtiva || $this->conversaAtiva->tipo !== 'grupo' || ! $this->conversaAtiva->grupo) {
            $this->usersDisponiveisAdicionar = new Collection();
            return;
        }

        $grupo = $this->conversaAtiva->grupo;

        $this->usersDisponiveisAdicionar = User::query()
            ->select('id', 'nome', 'email')
            ->whereNotIn('id', $grupo->membrosAtivos()->pluck('users.id'))
            ->orderBy('nome')
            ->get();
    }

    public function abrirAdicionarMembro()
    {
        if (!$this->conversaAtiva || $this->conversaAtiva->tipo !== 'grupo') {
            return;
        }

        $grupo = $this->conversaAtiva->grupo;

        if (!$grupo) {
            return;
        }

        if (!$this->userPodeGerirMembros()) {
            abort(403);
        }

        $this->mostrarAdicionarMembro = true;
        $this->selectedUserToAdd = null;
        $this->carregarUsersDisponiveisAdicionar();
    }

    public function fecharAdicionarMembro()
    {
        $this->mostrarAdicionarMembro = false;
        $this->searchMember = '';
        $this->utilizadoresParaAdicionar = new Collection();
    }

    public function updatedSearchMember()
    {
        $this->carregarUtilizadoresParaAdicionar();
    }

    protected function carregarUtilizadoresParaAdicionar()
    {
        if (!$this->conversaAtiva || $this->conversaAtiva->tipo !== 'grupo') return;

        $grupo = $this->conversaAtiva->grupo;
        if (!$grupo) return;

        $termo = trim($this->searchMember);

        if (mb_strlen($termo) < 2) {
            $this->utilizadoresParaAdicionar = new Collection();
            return;
        }

        $this->utilizadoresParaAdicionar = User::query()
            ->where('nome', 'like', "%{$termo}%")
            ->whereNotIn('id', function ($sub) use ($grupo) {
                $sub->select('user_id')
                    ->from('grupo_user')
                    ->where('grupo_id', $grupo->id)
                    ->whereNull('left_at');
            })
            ->orderBy('nome')
            ->limit(30)
            ->get();
    }

    public function adicionarMembroSelecionado(): void
    {
        $this->validate([
            'selectedUserToAdd' => 'required|exists:users,id',
        ]);

        $this->adicionarMembroAoGrupo($this->selectedUserToAdd);

        $this->selectedUserToAdd = null;
        $this->carregarUsersDisponiveisAdicionar();
        $this->recarregarBarraLateral();
    }

    public function adicionarMembroAoGrupo($userId): void
    {
        if (!$this->conversaAtiva || $this->conversaAtiva->tipo !== 'grupo') return;

        $grupo = $this->conversaAtiva->grupo;
        if (!$grupo) return;

        if (!$this->userPodeGerirMembros()) abort(403);

        $user = User::with('tipoUtilizador')->find($userId);
        if (!$user) return;

        // Role automático: admins do sistema e criador => moderador
        $role = (optional($user->tipoUtilizador)->nome === 'Administrador' || (int)$grupo->criador_id === (int)$user->id)
            ? 'moderator'
            : 'member';

        DB::transaction(function () use ($grupo, $userId, $role) {

            // 1) PIVOT grupo_user: se já existe, reativa; se não, cria
            $existe = DB::table('grupo_user')
                ->where('grupo_id', $grupo->id)
                ->where('user_id', $userId)
                ->exists();

            if ($existe) {
                $grupo->membros()->updateExistingPivot($userId, [
                    'role'      => $role,
                    'added_by'  => auth()->id(),
                    'joined_at' => now(),
                    'left_at'   => null,
                    'updated_at'=> now(),
                ]);
            } else {
                $grupo->membros()->attach($userId, [
                    'role'      => $role,
                    'added_by'  => auth()->id(),
                    'joined_at' => now(),
                    'left_at'   => null,
                    'created_at'=> now(),
                    'updated_at'=> now(),
                ]);
            }

            // 2) PIVOT participantes_conversa: mesmo princípio
            $existePc = DB::table('participantes_conversa')
                ->where('conversa_id', $this->conversaAtiva->id)
                ->where('utilizador_id', $userId)
                ->exists();

            if ($existePc) {
                $this->conversaAtiva->participantes()->updateExistingPivot($userId, [
                    'entrou_em' => now(),
                    'saiu_em'   => null,
                ]);
            } else {
                $this->conversaAtiva->participantes()->attach($userId, [
                    'entrou_em' => now(),
                    'saiu_em'   => null,
                ]);
            }
        });

        $this->carregarMembrosGrupo();
        $this->recarregarBarraLateral();
        $this->carregarUtilizadoresParaAdicionar();
    }

    public function removerMembroDoGrupo($userId): void
    {
        if (!$this->conversaAtiva || $this->conversaAtiva->tipo !== 'grupo') return;

        $grupo = $this->conversaAtiva->grupo;
        if (!$grupo) return;

        if ((int)$grupo->criador_id === (int)$userId) {
            // O criador não deve ser removível
            return;
        }

        $user = User::with('tipoUtilizador')->find($userId);
        if (optional($user->tipoUtilizador)->nome === 'Administrador' && !$this->userIsSystemAdmin()) {
            return;
        }

        if (!$this->userPodeGerirMembros()) abort(403);

        DB::transaction(function () use ($grupo, $userId) {
            $grupo->membros()->updateExistingPivot($userId, [
                'left_at' => now(),
                'updated_at'=> now(),
            ]);

            $this->conversaAtiva->participantes()->updateExistingPivot($userId, [
                'saiu_em' => now(),
            ]);
        });

        $this->carregarMembrosGrupo();
        $this->recarregarBarraLateral();
        $this->carregarUtilizadoresParaAdicionar();

        if ($userId === auth()->id()) {
            $this->conversaAtiva = null;
            $this->mensagens = new Collection();
        }
    }

    public function definirRoleMembro($userId, $role): void
    {
        if (!$this->conversaAtiva || $this->conversaAtiva->tipo !== 'grupo') return;

        $grupo = $this->conversaAtiva->grupo;
        if (!$grupo) return;

        if (!in_array($role, $this->rolesPermitidos, true)) return;

        if (!$this->userPodeGerirMembros()) abort(403);

        // Não permitir mexer no criador
        if ((int)$grupo->criador_id === (int)$userId) return;

        $user = User::with('tipoUtilizador')->find($userId);

        if (optional($user->tipoUtilizador)->nome === 'Administrador') {
            if (!$this->userIsSystemAdmin()) return;
            $role = 'moderator';
        }

        $grupo->membros()->updateExistingPivot($userId, [
            'role' => $role,
            'updated_at' => now(),
        ]);

        $this->carregarMembrosGrupo();
    }

    protected function userPodeModerarGrupo(): bool
    {
        if (! $this->conversaAtiva || $this->conversaAtiva->tipo !== 'grupo' || ! $this->conversaAtiva->grupo) {
            return false;
        }

        $grupo = $this->conversaAtiva->grupo;
        $uid = auth()->id();

        // Criador do grupo é sempre moderador
        if ((int) $grupo->criador_id === (int) $uid) {
            return true;
        }

        // Moderador via pivot
        return $grupo->membros()
            ->where('users.id', $uid)
            ->wherePivotNull('left_at')
            ->wherePivot('role', 'moderator')
            ->exists();
    }

    private function userIsSystemAdmin(): bool
    {
        return optional(auth()->user()->tipoUtilizador)->nome === 'Administrador';
    }

    private function userPodeGerirGrupo(): bool
    {
        if ($this->userIsSystemAdmin()) {
            return true;
        }

        return $this->userPodeModerarGrupo();
    }

    private function userPodeGerirMembros(): bool
    {
        if ($this->userIsSystemAdmin()) {
            return true;
        }

        return $this->userPodeModerarGrupo();
    }

    private function dispatchConversasIds(): void
    {
        // Limita para evitar subscrições excessivas
        $ids = $this->conversas->pluck('id')->take(60)->values()->all();

        $this->dispatch('conversas-ids', ids: $ids, activeId: $this->conversaAtiva?->id);
    }
}
