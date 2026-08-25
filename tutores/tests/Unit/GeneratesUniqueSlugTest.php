<?php

namespace Tests\Unit;

use App\Models\Career;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\SubjectCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GeneratesUniqueSlugTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_slugifies_a_plain_name(): void
    {
        $this->assertSame('ingenieria-en-sistemas', Career::uniqueSlug('Ingeniería en Sistemas'));
    }

    #[Test]
    public function it_appends_a_suffix_when_the_slug_is_taken(): void
    {
        Faculty::factory()->create(['slug' => 'ciencias-exactas']);

        $this->assertSame('ciencias-exactas-2', Faculty::uniqueSlug('Ciencias Exactas'));
    }

    #[Test]
    public function it_keeps_incrementing_until_it_finds_a_free_slug(): void
    {
        foreach (['fisica', 'fisica-2', 'fisica-3'] as $taken) {
            SubjectCategory::factory()->create(['slug' => $taken]);
        }

        $this->assertSame('fisica-4', SubjectCategory::uniqueSlug('Física'));
    }

    #[Test]
    public function different_names_that_collapse_to_the_same_slug_get_different_slugs(): void
    {
        SubjectCategory::factory()->create(['name' => 'Ciencias Exactas', 'slug' => 'ciencias-exactas']);

        $this->assertSame('ciencias-exactas-2', SubjectCategory::uniqueSlug('Ciencias-Exactas'));
        $this->assertSame('ciencias-exactas-2', SubjectCategory::uniqueSlug('¡Ciencias  Exactas!'));
    }

    #[Test]
    public function it_ignores_the_record_being_updated(): void
    {
        $subject = Subject::factory()->create(['name' => 'Matemática I', 'slug' => 'matematica-i']);

        // Al reguardar la misma materia sin cambiar el nombre no debe aparecer "-2".
        $this->assertSame('matematica-i', Subject::uniqueSlug('Matemática I', $subject->id));
    }

    #[Test]
    public function the_scope_limits_uniqueness_to_a_subset(): void
    {
        $a = Faculty::factory()->create();
        $b = Faculty::factory()->create();
        Career::factory()->create(['faculty_id' => $a->id, 'slug' => 'idiomas']);

        // Ocupado en la facultad A, libre en la B.
        $this->assertSame('idiomas-2', Career::uniqueSlug('Idiomas', scope: ['faculty_id' => $a->id]));
        $this->assertSame('idiomas', Career::uniqueSlug('Idiomas', scope: ['faculty_id' => $b->id]));
    }

    #[Test]
    public function a_name_without_slugifiable_characters_falls_back(): void
    {
        $this->assertSame('sin-nombre', Faculty::uniqueSlug('###'));
    }
}
