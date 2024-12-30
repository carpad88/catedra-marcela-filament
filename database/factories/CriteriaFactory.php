<?php

namespace Database\Factories;

use App\Models\Criteria;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class CriteriaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Criteria::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'weight' => $this->faker->randomFloat(2, 0, 1),
            'order' => $this->faker->numberBetween(1, 10),
            'project_id' => Project::factory(),
        ];
    }
}
