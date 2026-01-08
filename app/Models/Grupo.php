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

    protected $fillable = ['nome', 'descricao', 'criador_id', 'foto_path'];

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criador_id');
    }

    public function conversa(): HasOne
    {
        return $this->hasOne(Conversa::class, 'grupo_id');
    }

    /**
     * Fonte ÚNICA de verdade para membros do grupo.
     * NÃO filtrar aqui por left_at, para permitir reactivação e histórico.
     */
    public function membros(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'grupo_user')
            ->withPivot(['role', 'added_by', 'joined_at', 'left_at']) // mantém is_admin na BD se existir, mas não uses
            ->withTimestamps();
    }

    public function membrosAtivos(): BelongsToMany
    {
        return $this->membros()->wherePivotNull('left_at');
    }

    // ALIAS compatível (se existir código antigo)
    public function utilizadores(): BelongsToMany
    {
        return $this->membros();
    }

    public function roleOfUser(int $userId): ?string
    {
        return $this->membrosAtivos()
            ->wherePivot('user_id', $userId)
            ->value('grupo_user.role');
    }

    public function isModerator(int $userId): bool
    {
        if ((int)$this->criador_id === (int)$userId) {
            return true;
        }

        return $this->membrosAtivos()
            ->wherePivot('user_id', $userId)
            ->wherePivot('role', 'moderator')
            ->exists();
    }

    // Mantém estes métodos se já estiverem a ser usados no código, mas com a semântica correcta:
    public function canManageMembers(int $userId)
    {
        return $this->isModerator($userId);
    }

    public function canEditProfile(int $userId)
    {
        return $this->isModerator($userId);
    }
}