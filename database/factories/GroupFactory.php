<?php

namespace Database\Factories;

use App\Enums\Status;
use App\Models\Group;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class GroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Group::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'year' => date('Y'),
            'cycle' => Arr::random(['A', 'B']),
            'title' => $this->faker->sentence(4),
            'status' => Status::Active,
        ];
    }

    public function withProjects($count = 1, array $projectAttributes = []): GroupFactory
    {
        return $this->hasAttached(
            Project::factory()->count($count)->state($projectAttributes),
            [
                'started_at' => now(),
                'finished_at' => now()->addDays(30),
            ]
        );
    }
}
