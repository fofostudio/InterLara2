<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            throw new \Exception('Admin role not found. Make sure RoleSeeder has been run.');
        }

        DB::table('users')->insert([
            'name' => 'Administrador',
            'email' => 'admin@appinter.co',
            'email_verified_at' => now(),
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now(),
            'role_id' => $adminRole->id,
        ]);
    }
}
