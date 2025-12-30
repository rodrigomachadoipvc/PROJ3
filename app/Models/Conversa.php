<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversa extends Model
{
    use HasFactory;

    protected $fillable = ['tipo', 'grupo_id', 'ultima_mensagem_id'];

    public function mensagens(): HasMany
    {
        return $this->hasMany(Mensagem::class)->orderBy('created_at', 'asc');
    }

    public function participantes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'participantes_conversa', 'conversa_id', 'utilizador_id')
                    ->withTimestamps('entrou_em', 'saiu_em'); // Inclui timestamps da pivot
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    public function ultimaMensagem(): BelongsTo
    {
        return $this->belongsTo(Mensagem::class, 'ultima_mensagem_id');
    }

    public function pins()
    {
        return $this->hasMany(\App\Models\ConversaPin::class, 'conversa_id');
    }
}