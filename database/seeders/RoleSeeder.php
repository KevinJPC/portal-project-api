<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * It creates a new admin role in the database.
     *
     * @return void
     */
    public function run()
    {
        //
        Role::create([
            'name' => 'Admin',
            'name_slug' => 'admin',
            'description' => 'Rol de usuario administrador',
            'state' => 'A',
        ]);
    }
}
