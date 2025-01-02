<?php

namespace Database\Factories;

use App\Models\Criteria;
use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

class LevelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Level::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text(),
            'score' => $this->faker->numberBetween(1, 10),
            'criteria_id' => Criteria::factory(),
        ];
    }
}
