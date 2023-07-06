<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $password = bcrypt('password');

        // Seed users:
        // 1 Admin
        User::create([
            'first_name' => 'Ad',
            'last_name' => 'Ministrator',
            'email' => 'admin@admin.admin',
            'role' => 'admin',
            'password' => $password,
        ]);

        // 1 Teacher
        User::create([
            'first_name' => 'Pani',
            'last_name' => 'Ucitelka',
            'email' => 'pani@uc.ka',
            'role' => 'teacher',
            'password' => $password,
        ]);

        // 1 Student
        User::create([
            'first_name' => 'Jozko',
            'last_name' => 'Vajda',
            'email' => 'jozo@e.mail',
            'role' => 'student',
            'password' => $password,
        ]);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
