<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::create(['name' => 'superadmin', 'description' => 'SuperAdministrador']);
        Role::create(['name' => 'admin', 'description' => 'Administrador Punto']);
        Role::create(['name' => 'operator', 'description' => 'Operador']);
        Role::create(['name' => 'cashier', 'description' => 'Cajero']);
        Role::create(['name' => 'misc', 'description' => 'Miscelaneo']);
    }
}
