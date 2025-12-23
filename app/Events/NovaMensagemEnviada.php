<?php

namespace App\Events;

use App\Models\Mensagem;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NovaMensagemEnviada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Mensagem $mensagem;

    public function __construct(Mensagem $mensagem)
    {
        $this->mensagem = $mensagem->load('emissor');
    }

    public function broadcastWith(): array
    {
        return [
            'mensagem' => $this->mensagem->toArray(),
        ];
    }

    /**
     * Nome do evento no frontend (simples de ouvir)
     */
    public function broadcastAs(): string
    {
        return 'mensagem.nova';
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversa.' . $this->mensagem->conversa_id),
        ];
    }
}
