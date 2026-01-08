<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'is_system',
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

    /* =========================
     | Accessors / Mutators
     ========================= */

    public function getNameAttribute(): ?string
    {
        return $this->attributes['nome'] ?? null;
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['nome'] = $value;
    }

    public function getInitialsAttribute(): string
    {
        $name = $this->attributes['nome'] ?? '';

        if (!$name) {
            return 'U';
        }

        $parts = preg_split('/\s+/', trim($name));
        $parts = array_filter($parts);

        $initials = '';
        foreach ($parts as $part) {
            $initials .= mb_substr($part, 0, 1);
            if (mb_strlen($initials) === 2) {
                break;
            }
        }

        return strtoupper($initials ?: 'U');
    }

    public function getAvatarUrlPublicAttribute(): ?string
    {
        if (empty($this->attributes['avatar_url'])) {
            return null;
        }

        return asset('storage/' . ltrim($this->attributes['avatar_url'], '/'));
    }

    /* =========================
     | Relations
     ========================= */

    public function tipoUtilizador(): BelongsTo
    {
        return $this->belongsTo(TipoUtilizador::class, 'tipo_utilizador_id');
    }

    public function hasPermissionTo(string $permissao): bool
    {
        return $this->tipoUtilizador
            ? $this->tipoUtilizador->permissoes()->where('nome', $permissao)->exists()
            : false;
    }

    public function conversasParticipadas(): BelongsToMany
    {
        return $this->belongsToMany(
            Conversa::class,
            'participantes_conversa',
            'utilizador_id',
            'conversa_id'
        )->withPivot(['entrou_em', 'saiu_em']);
    }

    public function conversasAtivas(): BelongsToMany
    {
        return $this->conversasParticipadas()->wherePivotNull('saiu_em');
    }

    public function mensagensEnviadas(): HasMany
    {
        return $this->hasMany(Mensagem::class, 'emissor_id');
    }

    public function gruposCriados(): HasMany
    {
        return $this->hasMany(Grupo::class, 'criador_id');
    }

    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(Grupo::class, 'grupo_user')
            ->withPivot('is_admin', 'role', 'added_by', 'joined_at', 'left_at')
            ->withTimestamps();
    }

    public function gruposAtivos(): BelongsToMany
    {
        return $this->grupos()->wherePivotNull('left_at');
    }
}