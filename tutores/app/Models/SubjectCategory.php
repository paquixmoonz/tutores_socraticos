<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class SubjectCategory extends Model implements AuditableContract
{
    use Auditable, GeneratesUniqueSlug, HasFactory;

    protected $fillable = ['name', 'slug', 'color', 'description'];

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
}
