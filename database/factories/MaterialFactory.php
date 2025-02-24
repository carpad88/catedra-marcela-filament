<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => \App\Models\Tag::factory()->create(['type' => 'Recursos'])->id,
            'title' => $this->faker->sentence(),
            'author' => $this->faker->name(),
            'data' => [
                'year' => $this->faker->year(),
                'link' => $this->faker->url(),
            ],
        ];
    }

    public function book(): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => \App\Models\Tag::factory()->create(['name' => 'Libros', 'type' => 'Recursos'])->id,
            'data' => [
                'year' => $this->faker->year(),
                'publisher' => $this->faker->company(),
                'location' => $this->faker->city(),
            ],
        ]);
    }
}
