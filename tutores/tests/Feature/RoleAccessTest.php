<?php

namespace Tests\Feature;

use App\Models\Subject;
use PHPUnit\Framework\Attributes\Test;

class RoleAccessTest extends RoleTestCase
{
    #[Test]
    public function student_cannot_access_admin_panel(): void
    {
        $this->actingAs($this->student())
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    #[Test]
    public function student_cannot_access_teacher_panel(): void
    {
        $this->actingAs($this->student())
            ->get(route('teacher.subjects.index'))
            ->assertForbidden();
    }

    #[Test]
    public function teacher_can_access_teacher_panel_but_not_admin(): void
    {
        $teacher = $this->teacher();

        $this->actingAs($teacher)->get(route('teacher.subjects.index'))->assertOk();
        $this->actingAs($teacher)->get(route('admin.dashboard'))->assertForbidden();
    }

    #[Test]
    public function admin_can_access_both_panels(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('teacher.subjects.index'))->assertOk();
    }

    #[Test]
    public function teacher_cannot_manage_subjects_of_other_teachers(): void
    {
        $owner = $this->teacher();
        $other = $this->teacher();
        $subject = Subject::factory()->create(['teacher_id' => $owner->id]);

        $this->actingAs($other)
            ->get(route('teacher.subjects.show', $subject))
            ->assertForbidden();
    }

    #[Test]
    public function teacher_can_manage_their_own_subject(): void
    {
        $teacher = $this->teacher();
        $subject = Subject::factory()->create(['teacher_id' => $teacher->id]);

        $this->actingAs($teacher)
            ->get(route('teacher.subjects.show', $subject))
            ->assertOk()
            ->assertSee($subject->name);
    }
}
