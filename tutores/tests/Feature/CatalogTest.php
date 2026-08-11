<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\Career;
use App\Models\Cycle;
use App\Models\Faculty;
use App\Models\Subject;
use PHPUnit\Framework\Attributes\Test;

class CatalogTest extends RoleTestCase
{
    #[Test]
    public function catalog_lists_active_faculties(): void
    {
        $active = Faculty::factory()->create();
        $inactive = Faculty::factory()->create(['is_active' => false]);

        $this->actingAs($this->student())
            ->get(route('catalog.index'))
            ->assertOk()
            ->assertSee($active->name)
            ->assertDontSee($inactive->name);
    }

    #[Test]
    public function search_finds_subjects_by_name(): void
    {
        Subject::factory()->create(['name' => 'Cálculo Diferencial Avanzado']);

        $this->actingAs($this->student())
            ->get(route('catalog.index', ['q' => 'Cálculo']))
            ->assertOk()
            ->assertSee('Cálculo Diferencial Avanzado');
    }

    #[Test]
    public function career_page_groups_subjects_by_cycle(): void
    {
        $career = Career::factory()->create();
        $cycle = Cycle::factory()->create(['career_id' => $career->id, 'number' => 1]);
        $subject = Subject::factory()->create();
        $career->subjects()->attach($subject->id, ['cycle_id' => $cycle->id]);

        $this->actingAs($this->student())
            ->get(route('catalog.career', [$career->faculty, $career]))
            ->assertOk()
            ->assertSee($subject->name);
    }

    #[Test]
    public function subject_page_shows_agent_download_when_available(): void
    {
        $agent = Agent::factory()->create();

        $this->actingAs($this->student())
            ->get(route('catalog.subject', $agent->subject))
            ->assertOk()
            ->assertSee('Descargar agente')
            ->assertSee($agent->name);
    }

    #[Test]
    public function subject_page_shows_pending_state_without_agent(): void
    {
        $subject = Subject::factory()->create();

        $this->actingAs($this->student())
            ->get(route('catalog.subject', $subject))
            ->assertOk()
            ->assertSee('aún no está disponible');
    }

    #[Test]
    public function inactive_subject_returns_404(): void
    {
        $subject = Subject::factory()->create(['is_active' => false]);

        $this->actingAs($this->student())
            ->get(route('catalog.subject', $subject))
            ->assertNotFound();
    }
}
