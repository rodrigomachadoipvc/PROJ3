<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Grupo extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'descricao', 'criador_id'];

    public function utilizadores(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'utilizador_grupo', 'grupo_id', 'utilizador_id')
                    ->withPivot('funcao_no_grupo')
                    ->withTimestamps();
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criador_id');
    }

    public function conversa(): HasOne
    {
        return $this->hasOne(Conversa::class, 'grupo_id');
    }

}