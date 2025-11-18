<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversa;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Aqui pode registar todos os canais de transmissão de eventos que a
| sua aplicação suporta.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// NOVO: Autorização para o canal da conversa
// O nome {conversa} deve corresponder ao {id} em broadcastOn()
Broadcast::channel('conversa.{conversaId}', function (User $user, int $conversaId) {
    
    // Verifica se o utilizador autenticado ($user) 
    // está na lista de participantes da conversa ($conversaId)
    
    $conversa = Conversa::findOrFail($conversaId);
    
    // Se o utilizador for um participante, retorna true
    return $conversa->participantes->contains($user);
});