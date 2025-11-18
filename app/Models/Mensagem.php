<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Mensagem extends Model
{
    use HasFactory, SoftDeletes; 

    protected $table = 'mensagens';

    protected $fillable = ['conversa_id', 'emissor_id', 'conteudo', 'resposta_a_id', 'vista'];

    public function conversa(): BelongsTo
    {
        return $this->belongsTo(Conversa::class);
    }

    public function emissor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emissor_id');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(Anexo::class);
    }

    public function mensagemRespondida(): BelongsTo
    {
        return $this->belongsTo(Mensagem::class, 'resposta_a_id');
    }
}