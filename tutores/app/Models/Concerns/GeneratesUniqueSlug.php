<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Genera slugs únicos respetando el índice de la tabla.
 *
 * Dos nombres distintos pueden colapsar al mismo slug ("Ciencias Exactas" y
 * "Ciencias-Exactas"), así que la unicidad se resuelve aquí y no se delega al
 * índice de la base de datos, que reventaría con un error 500.
 */
trait GeneratesUniqueSlug
{
    /**
     * @param  array<string, mixed>  $scope  Columnas que acotan la unicidad (ej. ['faculty_id' => 3]).
     */
    public static function uniqueSlug(string $name, ?int $ignoreId = null, array $scope = []): string
    {
        $base = Str::slug($name) ?: 'sin-nombre';
        $slug = $base;
        $suffix = 2;

        while (static::query()
            ->where('slug', $slug)
            ->where($scope)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
