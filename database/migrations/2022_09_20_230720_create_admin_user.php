<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Admin',
            'dni' => '000000000',
            'first_last_name' => 'Admin',
            'second_last_name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('Password123.'),
            'state' => 'A',
            'role_id' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('users')
            ->where('id', '=', 1)
            ->delete();
    }
};
