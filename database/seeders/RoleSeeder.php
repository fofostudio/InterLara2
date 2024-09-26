<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::create(['name' => 'admin', 'description' => 'Administrador']);
        Role::create(['name' => 'operator', 'description' => 'Operador']);
        Role::create(['name' => 'cashier', 'description' => 'Cajero']);
    }
}
