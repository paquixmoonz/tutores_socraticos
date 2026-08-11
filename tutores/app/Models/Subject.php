<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Subject extends Model implements AuditableContract
{
    use Auditable, HasFactory;

    protected $fillable = [
        'name', 'slug', 'code', 'subject_category_id', 'teacher_id', 'description', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SubjectCategory::class, 'subject_category_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function careers(): BelongsToMany
    {
        return $this->belongsToMany(Career::class)
            ->withPivot('id', 'cycle_id')
            ->withTimestamps();
    }

    public function studyPlans(): HasMany
    {
        return $this->hasMany(StudyPlan::class)->orderByDesc('version');
    }

    public function activeStudyPlan(): HasOne
    {
        return $this->hasOne(StudyPlan::class)->where('is_active', true)->latest('version');
    }

    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class)->orderByDesc('version');
    }

    public function activeAgent(): HasOne
    {
        return $this->hasOne(Agent::class)->where('is_active', true)->latest('version');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
