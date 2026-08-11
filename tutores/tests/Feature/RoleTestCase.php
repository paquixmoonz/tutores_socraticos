<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

abstract class RoleTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['estudiante', 'docente', 'admin'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    protected function student(): User
    {
        return User::factory()->create()->assignRole('estudiante');
    }

    protected function teacher(): User
    {
        return User::factory()->create()->assignRole('docente');
    }

    protected function admin(): User
    {
        return User::factory()->create()->assignRole('admin');
    }
}
