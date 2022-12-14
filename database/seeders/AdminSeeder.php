<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * It creates a user with the role of admin.
     * @return void
     */
    public function run()
    {
        //
        User::create([
            'name' => 'Admin',
            'dni' => '000000000',
            'first_last_name' => 'Admin',
            'second_last_name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Password123.'),
            'state' => 'A',
            'is_admin' => true,
        ]);
    }
}
