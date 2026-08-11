<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Cycle extends Model implements AuditableContract
{
    use Auditable, HasFactory;

    protected $fillable = ['career_id', 'number', 'name'];

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    public function label(): string
    {
        return $this->name ?: 'Ciclo '.$this->roman();
    }

    public function roman(): string
    {
        $map = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];

        return $map[$this->number] ?? (string) $this->number;
    }
}
