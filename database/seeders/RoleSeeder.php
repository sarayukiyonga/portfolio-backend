<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrador con acceso total: ver, editar y borrar']
        );

        $this->command->info('✅ Rol "admin" creado correctamente');
    }
}
