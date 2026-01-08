<?php

namespace App\Policies;

use App\Models\Grupo;
use App\Models\User;

class GrupoPolicy
{
    public function updateProfile(User $user, Grupo $grupo): bool
    {
        return $grupo->canEditProfile($user->id);
    }

    public function addMember(User $user, Grupo $grupo): bool
    {
        return $grupo->canManageMembers($user->id);
    }

    public function removeMember(User $user, Grupo $grupo, User $target): bool
    {
        return $grupo->canManageMembers($user->id) && !$grupo->isAdmin($target->id);
    }

    public function setRole(User $user, Grupo $grupo): bool
    {
        return $grupo->canManageMembers($user->id);
    }
}