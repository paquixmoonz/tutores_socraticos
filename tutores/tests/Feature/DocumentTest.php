<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\Subject;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

class DocumentTest extends RoleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
    }

    #[Test]
    public function teacher_uploads_study_plan_for_their_subject(): void
    {
        $teacher = $this->teacher();
        $subject = Subject::factory()->create(['teacher_id' => $teacher->id]);

        $response = $this->actingAs($teacher)->post(route('teacher.study-plans.store', $subject), [
            'title' => 'Plan de grado 2026',
            'file' => UploadedFile::fake()->create('plan.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('study_plans', [
            'subject_id' => $subject->id,
            'title' => 'Plan de grado 2026',
            'version' => 1,
            'is_active' => true,
        ]);

        $plan = $subject->studyPlans()->first();
        Storage::disk('private')->assertExists($plan->file_path);
    }

    #[Test]
    public function new_study_plan_version_deactivates_the_previous_one(): void
    {
        $teacher = $this->teacher();
        $subject = Subject::factory()->create(['teacher_id' => $teacher->id]);

        foreach (['v1', 'v2'] as $title) {
            $this->actingAs($teacher)->post(route('teacher.study-plans.store', $subject), [
                'title' => $title,
                'file' => UploadedFile::fake()->create('plan.pdf', 100, 'application/pdf'),
            ]);
        }

        $this->assertDatabaseHas('study_plans', ['title' => 'v1', 'version' => 1, 'is_active' => false]);
        $this->assertDatabaseHas('study_plans', ['title' => 'v2', 'version' => 2, 'is_active' => true]);
    }

    #[Test]
    public function teacher_cannot_upload_plan_to_foreign_subject(): void
    {
        $subject = Subject::factory()->create(['teacher_id' => $this->teacher()->id]);

        $this->actingAs($this->teacher())->post(route('teacher.study-plans.store', $subject), [
            'title' => 'Intento',
            'file' => UploadedFile::fake()->create('plan.pdf', 100, 'application/pdf'),
        ])->assertForbidden();
    }

    #[Test]
    public function student_cannot_upload_study_plan(): void
    {
        $subject = Subject::factory()->create(['teacher_id' => $this->teacher()->id]);

        $this->actingAs($this->student())->post(route('teacher.study-plans.store', $subject), [
            'title' => 'Intento',
            'file' => UploadedFile::fake()->create('plan.pdf', 100, 'application/pdf'),
        ])->assertForbidden();
    }

    #[Test]
    public function study_plan_upload_rejects_invalid_file_types(): void
    {
        $teacher = $this->teacher();
        $subject = Subject::factory()->create(['teacher_id' => $teacher->id]);

        $this->actingAs($teacher)->post(route('teacher.study-plans.store', $subject), [
            'title' => 'Malicioso',
            'file' => UploadedFile::fake()->create('virus.exe', 100, 'application/x-msdownload'),
        ])->assertSessionHasErrors('file');

        $this->assertDatabaseCount('study_plans', 0);
    }

    #[Test]
    public function only_admin_can_upload_agents(): void
    {
        $teacher = $this->teacher();
        $subject = Subject::factory()->create(['teacher_id' => $teacher->id]);

        $payload = [
            'name' => 'Tutor Socrático',
            'file' => UploadedFile::fake()->createWithContent('tutor.md', '# Tutor'),
        ];

        $this->actingAs($teacher)->post(route('admin.agents.store', $subject), $payload)->assertForbidden();
        $this->actingAs($this->student())->post(route('admin.agents.store', $subject), $payload)->assertForbidden();

        $this->actingAs($this->admin())->post(route('admin.agents.store', $subject), $payload)->assertRedirect();
        $this->assertDatabaseHas('agents', ['subject_id' => $subject->id, 'name' => 'Tutor Socrático', 'is_active' => true]);
    }

    #[Test]
    public function student_can_download_active_agent_and_it_is_audited(): void
    {
        $agent = Agent::factory()->create();
        Storage::disk('private')->put($agent->file_path, '# Tutor de prueba');

        $response = $this->actingAs($this->student())->get(route('agents.download', $agent));

        $response->assertOk();
        $response->assertDownload($agent->file_name);
        $this->assertSame(1, $agent->fresh()->download_count);
        $this->assertDatabaseHas('audits', [
            'event' => 'agent.downloaded',
            'auditable_id' => $agent->id,
        ]);
    }

    #[Test]
    public function inactive_agent_cannot_be_downloaded_by_students(): void
    {
        $agent = Agent::factory()->create(['is_active' => false]);
        Storage::disk('private')->put($agent->file_path, 'x');

        $this->actingAs($this->student())
            ->get(route('agents.download', $agent))
            ->assertForbidden();
    }

    #[Test]
    public function copyable_agent_content_is_returned_as_json(): void
    {
        $agent = Agent::factory()->create(['mime_type' => 'text/markdown', 'file_name' => 'tutor.md']);
        Storage::disk('private')->put($agent->file_path, '# Contenido del tutor');

        $this->actingAs($this->student())
            ->get(route('agents.content', $agent))
            ->assertOk()
            ->assertJson(['content' => '# Contenido del tutor']);
    }

    #[Test]
    public function pdf_agent_content_is_not_exposed_for_copy(): void
    {
        $agent = Agent::factory()->create(['mime_type' => 'application/pdf', 'file_name' => 'tutor.pdf']);
        Storage::disk('private')->put($agent->file_path, '%PDF');

        $this->actingAs($this->student())
            ->get(route('agents.content', $agent))
            ->assertNotFound();
    }

    #[Test]
    public function guests_cannot_download_agents(): void
    {
        $agent = Agent::factory()->create();
        Storage::disk('private')->put($agent->file_path, 'x');

        $this->get(route('agents.download', $agent))->assertRedirect(route('login'));
    }
}
