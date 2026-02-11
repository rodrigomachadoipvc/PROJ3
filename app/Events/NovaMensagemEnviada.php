<?php

namespace App\Events;

use App\Models\Mensagem;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NovaMensagemEnviada implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public array $payload;

    public function __construct(Mensagem $mensagem)
    {
        $this->payload = [
            'id' => $mensagem->id,
            'conversa_id' => $mensagem->conversa_id,
            'emissor_id' => $mensagem->emissor_id,
            'created_at' => $mensagem->created_at?->toISOString(),
        ];

        Log::info('[Realtime] Evento NovaMensagemEnviada criado', $this->payload);
    }

    public function broadcastOn(): Channel
    {
        return new Channel('conversa.' . $this->payload['conversa_id']);
    }

    public function broadcastAs(): string
    {
        return 'mensagem.enviada';
    }

    public function broadcastWith(): array
    {
        Log::info('[Realtime] Broadcast enviado', $this->payload);
        return $this->payload;
    }
}
