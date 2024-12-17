<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'first_name' => 'super',
            'last_name' => 'admin',
            'email' => 'super_admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('super_admin');

        $teacher = User::factory()->create([
            'first_name' => 'teacher',
            'last_name' => 'usr',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
        ]);

        $teacher->assignRole('teacher');
    }
}
