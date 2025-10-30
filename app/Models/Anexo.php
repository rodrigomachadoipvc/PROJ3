<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anexo extends Model
{
    use HasFactory;

    protected $fillable = ['mensagem_id', 'nome_original', 'caminho_storage', 'mime_type', 'tamanho_bytes'];

    public function mensagem(): BelongsTo
    {
        return $this->belongsTo(Mensagem::class);
    }
}