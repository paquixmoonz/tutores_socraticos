<?php

namespace Tests\Feature;

use App\Models\Subject;
use PHPUnit\Framework\Attributes\Test;

class GuideTest extends RoleTestCase
{
    #[Test]
    public function the_guide_requires_authentication(): void
    {
        $this->get(route('guide'))->assertRedirect(route('login'));
    }

    #[Test]
    public function students_can_read_the_guide(): void
    {
        $this->actingAs($this->student())
            ->get(route('guide'))
            ->assertOk()
            ->assertSee('Cómo usar tu tutor socrático')
            ->assertSee('Lo que sí hace')
            ->assertSee('Lo que no hace');
    }

    #[Test]
    public function the_guide_explains_every_supported_platform(): void
    {
        $response = $this->actingAs($this->student())->get(route('guide'));

        foreach (['ChatGPT', 'Claude', 'Gemini'] as $platform) {
            $response->assertSee($platform);
        }
    }

    #[Test]
    public function the_subject_page_explains_the_socratic_expectation(): void
    {
        $subject = Subject::factory()->create();

        $this->actingAs($this->student())
            ->get(route('catalog.subject', $subject))
            ->assertOk()
            ->assertSee('no te dará la')          // "no te dará la respuesta de un ejercicio"
            ->assertSee('Dónde pegarlo')
            ->assertSee('ChatGPT');
    }

    #[Test]
    public function the_subject_page_links_to_the_full_guide(): void
    {
        $subject = Subject::factory()->create();

        $this->actingAs($this->student())
            ->get(route('catalog.subject', $subject))
            ->assertOk()
            ->assertSee(route('guide'));
    }
}
