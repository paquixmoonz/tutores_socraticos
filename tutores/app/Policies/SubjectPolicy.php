<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

class SubjectPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    // Solo el docente encargado gestiona los planes de su materia.
    public function managePlans(User $user, Subject $subject): bool
    {
        return $user->isTeacher() && $subject->teacher_id === $user->id;
    }
}
