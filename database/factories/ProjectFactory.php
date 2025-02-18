<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'category_id' => Tag::factory(),
            'cover' => $this->faker->url(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text(),
            'goals' => $this->faker->text(),
            'activities' => $this->faker->text(),
            'conditions' => $this->faker->text(),
            'started_at' => $this->faker->dateTime(),
            'finished_at' => $this->faker->dateTime(),
            'status' => $this->faker->randomElement(['active', 'archived']),
        ];
    }
}
