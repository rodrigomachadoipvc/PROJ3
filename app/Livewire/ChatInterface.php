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


    protected $listeners = [
        'mensagemRecebidaRealtime' => 'onMensagemRecebidaRealtime',
    ];


    public string $novaMensagem = '';

    /**
     * Uploads (múltiplos)
     * @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile>
     */
    public array $uploads = [];

    public function mount(Request $request): void
    {
        $this->carregarPropriedadesIniciais();

        $conversaAtivaId = $request->session()->get('conversa_ativa_id');
        if ($conversaAtivaId) {
            $this->selecionarConversa((int) $conversaAtivaId);
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
                ->with(['emissor', 'anexos'])
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $this->mensagens = new Collection();
        }

        // Limpar campos ao trocar de conversa
        $this->novaMensagem = '';
        $this->uploads = [];
        $this->dispatch('reset-upload-input');

        $this->mostrarMembrosGrupo = false;
        $this->membrosGrupo = null;
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

    /**
     * Validação dos uploads (ao escolher ficheiros)
     */
 public function updatedUploads(): void
{
    $this->validate([
        'uploads' => ['array', 'max:5'], // até 5 ficheiros
        'uploads.*' => [
            'file',
            'max:10240', // 10MB por ficheiro (em KB)
            // robusto por extensão (mimetypes em rar/zip varia muito)
            'mimes:jpg,jpeg,png,webp,pdf,zip,rar',
        ],
    ], [
        'uploads.max' => 'Máximo de 5 anexos por mensagem.',
        'uploads.*.max' => 'Um dos ficheiros excede 10MB.',
        'uploads.*.mimes' => 'Tipo de ficheiro não permitido.',
    ]);

    // Limite total por mensagem: 25MB
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

        // Permitir enviar só anexos (sem texto)
        if (! $temTexto && ! $temUploads) {
            return;
        }

        // Segurança: garantir que o utilizador participa nesta conversa
        $participa = $utilizador->conversasParticipadas()
            ->where('conversas.id', $this->conversaAtiva->id)
            ->exists();

        if (! $participa) {
            abort(403);
        }

        // Validar novamente antes de persistir
        $this->validate([
            'uploads' => ['array', 'max:5'],
            'uploads.*' => [
                'file',
                'max:10240', // 10MB por ficheiro
                'mimes:jpg,jpeg,png,webp,pdf,zip,rar',
            ],
        ], [
            'uploads.max' => 'Máximo de 5 anexos por mensagem.',
            'uploads.*.max' => 'Um dos ficheiros excede 10MB.',
            'uploads.*.mimes' => 'Tipo de ficheiro não permitido.',
        ]);

        // Limite total por mensagem: 25MB
        $totalBytes = 0;
        foreach ($this->uploads as $file) {
            $totalBytes += (int) $file->getSize();
        }
        if ($totalBytes > 25 * 1024 * 1024) {
            $this->addError('uploads', 'O total de anexos por mensagem não pode exceder 25MB.');
            return;
        }

        DB::transaction(function () use ($utilizador, $temTexto) {

            /** @var Mensagem $novaMsg */
            $novaMsg = $this->conversaAtiva->mensagens()->create([
                'emissor_id' => $utilizador->id,
                'conteudo'   => $temTexto ? $this->novaMensagem : null,
            ]);

            app(\App\Services\AutoReplyService::class)
                ->handle($novaMsg);

            // Guardar anexos (se existirem)
            if (!empty($this->uploads)) {
                foreach ($this->uploads as $file) {

                    $original = $file->getClientOriginalName();
                    $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');

                    // Nome seguro e único (contrato: UUID + extensão)
                    $filename = (string) Str::uuid() . '.' . $ext;

                    // Contrato: storage/app/public/anexos/conversas/{conversa_id}/
                    $path = "anexos/conversas/{$this->conversaAtiva->id}/{$filename}";

                    // Disk public -> /storage/...
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

            // Atualiza a referência à última mensagem na conversa
            $this->conversaAtiva->update(['ultima_mensagem_id' => $novaMsg->id]);
        });

        // Recarrega mensagens (com anexos)
        $this->mensagens = $this->conversaAtiva->mensagens()
            ->with(['emissor', 'anexos'])
            ->orderBy('created_at', 'asc')
            ->get();

        $this->recarregarBarraLateral();

        // Limpa inputs
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

        // Reindexar o array para evitar buracos
        $this->uploads = array_values($this->uploads);
    }

    public function recarregarBarraLateral(): void
    {
        $utilizador = Auth::user();

        $this->conversas = $utilizador->conversasParticipadas()
            ->with(['ultimaMensagem.emissor', 'participantes', 'grupo'])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function onMensagemRecebidaRealtime(array $payload): void
    {
        // Ignorar mensagens criadas localmente
        if (
            $payload['emissor_id'] === auth()->id() ||
            ($payload['emissor']['is_system'] ?? false)
        ) {
            return;
        }

        // Evitar duplicação por segurança
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


    public function render()
    {
        return view('livewire.chat-interface');
    }
}
