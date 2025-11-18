<?php

namespace App\Events;

use App\Models\Mensagem;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// 1. Implementamos a interface ShouldBroadcast
class NovaMensagemEnviada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // A mensagem que foi enviada
    public Mensagem $mensagem;

    /**
     * Cria uma nova instância do evento.
     * Carregamos o 'emissor' para que o frontend o possa usar.
     */
    public function __construct(Mensagem $mensagem)
    {
        // Carrega a relação 'emissor' antes de transmitir
        $this->mensagem = $mensagem->load('emissor');
    }

    /**
     * Define os dados que serão transmitidos.
     * O frontend receberá um JSON com esta estrutura.
     */
    public function broadcastWith(): array
    {
        return [
            // Usamos toArray() para converter o modelo em dados simples
            'mensagem' => $this->mensagem->toArray(),
        ];
    }

    /**
     * Define o canal privado para onde transmitir.
     * Apenas os participantes da conversa podem "ouvir".
     */
    public function broadcastOn(): array
    {
        // O nome do canal será 'conversa.ID_DA_CONVERSA'
        // É um PrivateChannel porque não queremos que todos oiçam
        return [
            new PrivateChannel('conversa.' . $this->mensagem->conversa_id),
        ];
    }
}