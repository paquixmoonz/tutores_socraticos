<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\StudyPlan;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OwenIt\Auditing\Models\Audit;

class DocumentService
{
    public function storeStudyPlan(Subject $subject, UploadedFile $file, User $uploader, string $title): StudyPlan
    {
        return DB::transaction(function () use ($subject, $file, $uploader, $title) {
            $version = ($subject->studyPlans()->max('version') ?? 0) + 1;
            $subject->studyPlans()->update(['is_active' => false]);

            $plan = $subject->studyPlans()->create([
                'uploaded_by' => $uploader->id,
                'title' => $title,
                'file_path' => $this->putFile('study-plans/'.$subject->id, $file),
                'file_name' => $this->sanitizeName($file),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'version' => $version,
                'is_active' => true,
            ]);

            $this->recordEvent('study_plan.uploaded', $plan, $uploader);

            return $plan;
        });
    }

    public function storeAgent(Subject $subject, UploadedFile $file, User $uploader, string $name, ?string $description): Agent
    {
        return DB::transaction(function () use ($subject, $file, $uploader, $name, $description) {
            $version = ($subject->agents()->max('version') ?? 0) + 1;
            $subject->agents()->update(['is_active' => false]);

            $agent = $subject->agents()->create([
                'uploaded_by' => $uploader->id,
                'name' => $name,
                'description' => $description,
                'file_path' => $this->putFile('agents/'.$subject->id, $file),
                'file_name' => $this->sanitizeName($file),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'version' => $version,
                'is_active' => true,
            ]);

            $this->recordEvent('agent.uploaded', $agent, $uploader);

            return $agent;
        });
    }

    public function deleteDocumentFile(StudyPlan|Agent $document): void
    {
        Storage::disk('private')->delete($document->file_path);
    }

    /**
     * Registra un evento personalizado en la misma tabla de auditoría
     * (descargas, cambios de rol, etc.). Inmutable como el resto.
     */
    public function recordEvent(string $event, Model $auditable, ?User $user, array $extra = []): void
    {
        Audit::create([
            'user_type' => $user ? User::class : null,
            'user_id' => $user?->id,
            'event' => $event,
            'auditable_type' => $auditable->getMorphClass(),
            'auditable_id' => $auditable->getKey(),
            'old_values' => [],
            'new_values' => $extra,
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => Str::limit((string) request()->userAgent(), 1023),
            'tags' => null,
        ]);
    }

    private function putFile(string $directory, UploadedFile $file): string
    {
        // Nombre regenerado (uuid): el nombre original solo vive en BD.
        $extension = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $path = $directory.'/'.Str::uuid().'.'.$extension;

        Storage::disk('private')->put($path, $file->get());

        return $path;
    }

    private function sanitizeName(UploadedFile $file): string
    {
        $base = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = strtolower($file->getClientOriginalExtension());

        return Str::slug(Str::limit($base, 100, '')).($extension ? '.'.$extension : '');
    }
}
