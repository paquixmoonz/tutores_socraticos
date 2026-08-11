<?php

namespace App\Policies;

use App\Models\Agent;
use App\Models\User;

class AgentPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    // Cualquier miembro autenticado descarga el agente activo de una materia activa.
    public function download(User $user, Agent $agent): bool
    {
        return $agent->is_active && $agent->subject->is_active;
    }
}
