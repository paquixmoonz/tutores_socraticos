<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Agent extends Model implements AuditableContract
{
    use Auditable, HasFactory;

    protected $fillable = [
        'subject_id', 'uploaded_by', 'name', 'description', 'file_path', 'file_name',
        'file_size', 'mime_type', 'version', 'download_count', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    // El contador cambia en cada descarga; auditarlo generaría ruido infinito.
    protected $auditExclude = ['download_count'];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isCopyable(): bool
    {
        return in_array($this->mime_type, ['text/plain', 'text/markdown', 'application/json'])
            || preg_match('/\.(txt|md|json)$/i', $this->file_name);
    }
}
