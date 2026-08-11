<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\Cycle;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\SubjectCategory;
use PHPUnit\Framework\Attributes\Test;

class AdminCrudTest extends RoleTestCase
{
    #[Test]
    public function admin_creates_the_academic_structure(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.facultades.store'), [
            'name' => 'Facultad de Prueba',
        ])->assertRedirect(route('admin.facultades.index'));

        $faculty = Faculty::where('name', 'Facultad de Prueba')->first();
        $this->assertNotNull($faculty);

        $this->actingAs($admin)->post(route('admin.carreras.store'), [
            'faculty_id' => $faculty->id,
            'name' => 'Ingeniería de Prueba',
            'plan_year' => '2024-2028',
        ]);

        $career = Career::where('name', 'Ingeniería de Prueba')->first();
        $this->assertNotNull($career);

        $this->actingAs($admin)->post(route('admin.cycles.store', $career), ['number' => 1]);
        $this->assertDatabaseHas('cycles', ['career_id' => $career->id, 'number' => 1]);
    }

    #[Test]
    public function duplicate_cycle_numbers_are_rejected(): void
    {
        $admin = $this->admin();
        $cycle = Cycle::factory()->create(['number' => 1]);

        $this->actingAs($admin)
            ->post(route('admin.cycles.store', $cycle->career), ['number' => 1])
            ->assertSessionHasErrors('number');
    }

    #[Test]
    public function subject_requires_a_category(): void
    {
        $this->actingAs($this->admin())->post(route('admin.materias.store'), [
            'name' => 'Materia sin categoría',
        ])->assertSessionHasErrors('subject_category_id');

        $this->assertDatabaseCount('subjects', 0);
    }

    #[Test]
    public function subject_teacher_must_have_the_docente_role(): void
    {
        $student = $this->student();

        $this->actingAs($this->admin())->post(route('admin.materias.store'), [
            'name' => 'Materia X',
            'subject_category_id' => SubjectCategory::factory()->create()->id,
            'teacher_id' => $student->id,
        ])->assertSessionHasErrors('teacher_id');
    }

    #[Test]
    public function admin_creates_subject_with_category_and_teacher(): void
    {
        $teacher = $this->teacher();
        $category = SubjectCategory::factory()->create();

        $this->actingAs($this->admin())->post(route('admin.materias.store'), [
            'name' => 'Matemática de Prueba',
            'code' => 'MAT-01',
            'subject_category_id' => $category->id,
            'teacher_id' => $teacher->id,
            'description' => 'Descripción',
        ]);

        $this->assertDatabaseHas('subjects', [
            'name' => 'Matemática de Prueba',
            'subject_category_id' => $category->id,
            'teacher_id' => $teacher->id,
        ]);
    }

    #[Test]
    public function subject_can_be_assigned_to_career_and_cycle(): void
    {
        $subject = Subject::factory()->create();
        $cycle = Cycle::factory()->create();

        $this->actingAs($this->admin())->post(route('admin.subjects.assignments.store', $subject), [
            'career_id' => $cycle->career_id,
            'cycle_id' => $cycle->id,
        ]);

        $this->assertDatabaseHas('career_subject', [
            'subject_id' => $subject->id,
            'career_id' => $cycle->career_id,
            'cycle_id' => $cycle->id,
        ]);
    }

    #[Test]
    public function assignment_rejects_cycle_from_another_career(): void
    {
        $subject = Subject::factory()->create();
        $cycle = Cycle::factory()->create();
        $otherCareer = Career::factory()->create();

        $this->actingAs($this->admin())->post(route('admin.subjects.assignments.store', $subject), [
            'career_id' => $otherCareer->id,
            'cycle_id' => $cycle->id,
        ])->assertSessionHasErrors('cycle_id');
    }

    #[Test]
    public function category_in_use_cannot_be_deleted(): void
    {
        $subject = Subject::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.categorias.destroy', $subject->category))
            ->assertSessionHasErrors();

        $this->assertDatabaseHas('subject_categories', ['id' => $subject->subject_category_id]);
    }

    #[Test]
    public function admin_changes_a_user_role_and_it_is_audited(): void
    {
        $admin = $this->admin();
        $user = $this->student();

        $this->actingAs($admin)->patch(route('admin.users.role', $user), ['role' => 'docente']);

        $this->assertTrue($user->fresh()->hasRole('docente'));
        $this->assertFalse($user->fresh()->hasRole('estudiante'));
        $this->assertDatabaseHas('audits', [
            'event' => 'role.assigned',
            'auditable_id' => $user->id,
            'user_id' => $admin->id,
        ]);
    }

    #[Test]
    public function admin_cannot_change_their_own_role(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(route('admin.users.role', $admin), ['role' => 'estudiante'])
            ->assertForbidden();

        $this->assertTrue($admin->fresh()->hasRole('admin'));
    }

    #[Test]
    public function students_cannot_use_admin_crud_endpoints(): void
    {
        $student = $this->student();

        $this->actingAs($student)->post(route('admin.facultades.store'), ['name' => 'X'])->assertForbidden();
        $this->actingAs($student)->post(route('admin.materias.store'), ['name' => 'X'])->assertForbidden();
        $this->actingAs($student)->patch(route('admin.users.role', $this->student()), ['role' => 'admin'])->assertForbidden();
    }

    #[Test]
    public function audit_log_records_catalog_changes(): void
    {
        $this->actingAs($this->admin())->post(route('admin.facultades.store'), [
            'name' => 'Facultad Auditada',
        ]);

        $faculty = Faculty::where('name', 'Facultad Auditada')->first();

        $this->assertDatabaseHas('audits', [
            'event' => 'created',
            'auditable_type' => Faculty::class,
            'auditable_id' => $faculty->id,
        ]);
    }

    #[Test]
    public function audit_page_is_visible_only_to_admins(): void
    {
        $this->actingAs($this->admin())->get(route('admin.audits.index'))->assertOk();
        $this->actingAs($this->teacher())->get(route('admin.audits.index'))->assertForbidden();
        $this->actingAs($this->student())->get(route('admin.audits.index'))->assertForbidden();
    }
}
