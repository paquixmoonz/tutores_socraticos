<?php

namespace App\Policies;

use App\Models\StudyPlan;
use App\Models\User;

class StudyPlanPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    // Cualquier miembro autenticado puede descargar el plan activo;
    // las versiones inactivas solo las ve quien las gestiona.
    public function download(User $user, StudyPlan $studyPlan): bool
    {
        return $studyPlan->is_active
            || ($user->isTeacher() && $studyPlan->subject->teacher_id === $user->id);
    }

    public function manage(User $user, StudyPlan $studyPlan): bool
    {
        return $user->isTeacher() && $studyPlan->subject->teacher_id === $user->id;
    }
}
