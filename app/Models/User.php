<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nome',
        'email',
        'password',
        'tipo_utilizador_id',
        'status',
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tipoUtilizador(): BelongsTo
    {
        return $this->belongsTo(TipoUtilizador::class, 'tipo_utilizador_id');
    }

    public function hasPermissionTo(string $permissao): bool
    {
        if (!$this->tipoUtilizador) {
            return false;
        }

        return $this->tipoUtilizador->permissoes()->where('nome', $permissao)->exists();
    }

    public function conversasParticipadas(): BelongsToMany
    {
        return $this->belongsToMany(Conversa::class, 'participantes_conversa', 'utilizador_id', 'conversa_id')
                    ->withTimestamps('entrou_em', 'saiu_em');
    }

    public function mensagensEnviadas(): HasMany
    {
        return $this->hasMany(Mensagem::class, 'emissor_id');
    }

    public function gruposCriados(): HasMany
    {
        return $this->hasMany(Grupo::class, 'criador_id');
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_user')
                    ->withPivot('is_admin')
                    ->withTimestamps();
    }

    /**
     * Iniciais (até 2 letras) para usar como avatar textual.
     */
    public function getInitialsAttribute(): string
    {
        $name = $this->nome ?? $this->attributes['name'] ?? '';

        $initials = '';
        if ($name) {
            $parts = preg_split('/\s+/', trim($name));
            $parts = array_filter($parts);

            $i = 0;
            foreach ($parts as $part) {
                $initials .= mb_substr($part, 0, 1);
                $i++;
                if ($i >= 2) {
                    break;
                }
            }
        }

        return strtoupper($initials ?: 'U');
    }

    /**
     * URL público completo para o avatar (ou null).
     */
    public function getAvatarUrlPublicAttribute(): ?string
    {
        if (!$this->avatar_url) {
            return null;
        }

        return asset('storage/' . $this->avatar_url);
    }
}
