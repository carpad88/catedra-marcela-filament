<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;
use Random\RandomException;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws RandomException
     */
    public function run(): void
    {
        foreach (range(1, 3) as $index) {
            $group = Group::factory()->create(['owner_id' => 1]);
            $students = User::factory()->count(random_int(5, 8))->create();

            foreach ($students as $student) {
                $student->assignRole('student');
            }
            $group->students()->attach($students);
        }

        foreach (range(1, 3) as $index) {
            $group = Group::factory()
                ->create(['owner_id' => 2]);

            $students = User::factory()->count(random_int(5, 8))->create();
            foreach ($students as $student) {
                $student->assignRole('student');
            }
            $group->students()->attach($students);
        }
    }
}
