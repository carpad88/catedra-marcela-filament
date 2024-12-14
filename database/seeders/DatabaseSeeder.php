<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $user = User::factory()->create([
            'first_name' => 'super',
            'last_name' => 'admin',
            'email' => 'super_admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('super_admin');
    }
}
