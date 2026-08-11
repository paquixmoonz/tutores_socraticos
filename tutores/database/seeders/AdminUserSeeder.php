<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('tutores.seed_admin_email');

        $admin = User::firstOrCreate(
            ['email' => $email],
            ['name' => 'Administrador UNICAES'],
        );

        $admin->syncRoles(['admin']);
    }
}
