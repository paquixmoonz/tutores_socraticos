<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\Cycle;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\SubjectCategory;
use PHPUnit\Framework\Attributes\Test;

/**
 * Sondeo de la estructura académica: facultad → carrera → ciclo → materia.
 * Estos casos son los que aparecen cuando el catálogo crece a todas las
 * carreras de la universidad y varios admins cargan datos en paralelo.
 */
class AcademicStructureTest extends RoleTestCase
{
    /*
    |--------------------------------------------------------------------------
    | Facultades
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function faculty_name_must_be_unique(): void
    {
        Faculty::factory()->create(['name' => 'Facultad de Ingeniería y Arquitectura']);

        $this->actingAs($this->admin())
            ->post(route('admin.facultades.store'), ['name' => 'Facultad de Ingeniería y Arquitectura'])
            ->assertSessionHasErrors('name');

        $this->assertSame(1, Faculty::where('name', 'Facultad de Ingeniería y Arquitectura')->count());
    }

    #[Test]
    public function faculty_can_keep_its_own_name_when_editing(): void
    {
        $faculty = Faculty::factory()->create(['name' => 'Facultad de Derecho']);

        $this->actingAs($this->admin())
            ->put(route('admin.facultades.update', $faculty), [
                'name' => 'Facultad de Derecho',
                'description' => 'Descripción nueva',
                'is_active' => 1,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('Descripción nueva', $faculty->fresh()->description);
    }

    /*
    |--------------------------------------------------------------------------
    | Carreras
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function career_name_must_be_unique_within_the_same_faculty(): void
    {
        $faculty = Faculty::factory()->create();
        Career::factory()->create(['faculty_id' => $faculty->id, 'name' => 'Ingeniería en Sistemas']);

        $this->actingAs($this->admin())
            ->post(route('admin.carreras.store'), [
                'faculty_id' => $faculty->id,
                'name' => 'Ingeniería en Sistemas',
            ])
            ->assertSessionHasErrors('name');

        $this->assertSame(1, Career::where('faculty_id', $faculty->id)->count());
    }

    #[Test]
    public function same_career_name_is_allowed_in_a_different_faculty(): void
    {
        $a = Faculty::factory()->create();
        $b = Faculty::factory()->create();
        Career::factory()->create(['faculty_id' => $a->id, 'name' => 'Licenciatura en Idiomas']);

        $this->actingAs($this->admin())
            ->post(route('admin.carreras.store'), [
                'faculty_id' => $b->id,
                'name' => 'Licenciatura en Idiomas',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(2, Career::where('name', 'Licenciatura en Idiomas')->count());
    }

    /**
     * Una facultad se arma en privado y se publica después, así que crear
     * carreras dentro de una facultad inactiva es válido: lo que debe cumplirse
     * es que nada de eso se filtre al catálogo del estudiante.
     */
    #[Test]
    public function careers_of_an_inactive_faculty_stay_out_of_the_catalog(): void
    {
        $faculty = Faculty::factory()->create(['is_active' => false]);

        $this->actingAs($this->admin())
            ->post(route('admin.carreras.store'), [
                'faculty_id' => $faculty->id,
                'name' => 'Carrera en Preparación',
            ])
            ->assertSessionHasNoErrors();

        $career = Career::where('name', 'Carrera en Preparación')->firstOrFail();
        $student = $this->student();

        $this->actingAs($student)->get(route('catalog.index'))->assertDontSee($faculty->name);
        $this->actingAs($student)->get(route('catalog.faculty', $faculty))->assertNotFound();
        $this->actingAs($student)->get(route('catalog.career', [$faculty, $career]))->assertNotFound();
    }

    /*
    |--------------------------------------------------------------------------
    | Ciclos
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function cycle_number_must_be_within_range(): void
    {
        $career = Career::factory()->create();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.cycles.store', $career), ['number' => 0])
            ->assertSessionHasErrors('number');

        $this->actingAs($admin)
            ->post(route('admin.cycles.store', $career), ['number' => 13])
            ->assertSessionHasErrors('number');

        $this->assertDatabaseCount('cycles', 0);
    }

    #[Test]
    public function cycle_with_subjects_cannot_be_deleted(): void
    {
        $cycle = Cycle::factory()->create();
        $subject = Subject::factory()->create();
        $cycle->career->subjects()->attach($subject->id, ['cycle_id' => $cycle->id]);

        $this->actingAs($this->admin())
            ->delete(route('admin.cycles.destroy', $cycle))
            ->assertSessionHasErrors();

        $this->assertDatabaseHas('cycles', ['id' => $cycle->id]);
    }

    #[Test]
    public function deleting_a_career_removes_its_cycles(): void
    {
        $career = Career::factory()->create();
        Cycle::factory()->count(3)->create(['career_id' => $career->id]);

        $this->actingAs($this->admin())
            ->delete(route('admin.carreras.destroy', $career))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('cycles', 0);
        $this->assertDatabaseCount('careers', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Categorías de materia
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function category_names_that_collapse_to_the_same_slug_are_handled(): void
    {
        SubjectCategory::factory()->create(['name' => 'Ciencias Exactas', 'slug' => 'ciencias-exactas']);

        // Nombre distinto (pasa unique:name) pero mismo slug: no debe reventar la BD.
        $this->actingAs($this->admin())
            ->post(route('admin.categorias.store'), ['name' => 'Ciencias-Exactas'])
            ->assertSessionHasNoErrors();

        $this->assertSame(2, SubjectCategory::count());
        $this->assertSame(2, SubjectCategory::distinct()->count('slug'));
    }

    /*
    |--------------------------------------------------------------------------
    | Materias
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function subject_code_must_be_unique(): void
    {
        Subject::factory()->create(['code' => 'MAT-101']);

        $this->actingAs($this->admin())
            ->post(route('admin.materias.store'), [
                'name' => 'Otra Materia',
                'code' => 'MAT-101',
                'subject_category_id' => SubjectCategory::factory()->create()->id,
            ])
            ->assertSessionHasErrors('code');
    }

    #[Test]
    public function subject_name_must_be_unique(): void
    {
        Subject::factory()->create(['name' => 'Matemática I']);

        $this->actingAs($this->admin())
            ->post(route('admin.materias.store'), [
                'name' => 'Matemática I',
                'subject_category_id' => SubjectCategory::factory()->create()->id,
            ])
            ->assertSessionHasErrors('name');
    }

    #[Test]
    public function subject_can_be_shared_by_several_careers_in_different_cycles(): void
    {
        $subject = Subject::factory()->create();
        $cycleA = Cycle::factory()->create(['number' => 1]);
        $cycleB = Cycle::factory()->create(['number' => 2]);
        $admin = $this->admin();

        foreach ([$cycleA, $cycleB] as $cycle) {
            $this->actingAs($admin)->post(route('admin.subjects.assignments.store', $subject), [
                'career_id' => $cycle->career_id,
                'cycle_id' => $cycle->id,
            ])->assertSessionHasNoErrors();
        }

        $this->assertSame(2, $subject->careers()->count());
    }

    #[Test]
    public function subject_cannot_be_assigned_twice_to_the_same_career_and_cycle(): void
    {
        $subject = Subject::factory()->create();
        $cycle = Cycle::factory()->create();
        $admin = $this->admin();

        $payload = ['career_id' => $cycle->career_id, 'cycle_id' => $cycle->id];

        $this->actingAs($admin)->post(route('admin.subjects.assignments.store', $subject), $payload);
        $this->actingAs($admin)->post(route('admin.subjects.assignments.store', $subject), $payload)
            ->assertSessionHasErrors();

        $this->assertDatabaseCount('career_subject', 1);
    }
}
