<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'email' => 'user1@example.com',
                'password' => Hash::make('password123'),
                'persona_id' => 1,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'user2@example.com',
                'password' => Hash::make('password123'),
                'persona_id' => 2,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Añade más usuarios si lo necesitas
        ]);
    }
}
