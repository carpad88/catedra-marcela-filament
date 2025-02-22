<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Project;
use App\Models\User;
use App\Models\Work;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Work::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $project = Project::factory()->hasCriterias(2)->create();
        $group = Group::factory()->create();
        $user = User::factory()->create();

        $project->groups()->attach($group, ['started_at' => now(), 'finished_at' => now()->addDays(15)]);

        $folder = "$group->folderName/$user->folderName/$project->folderName";

        return [
            'project_id' => $project->id,
            'user_id' => $user->id,
            'group_id' => $group->id,
            'cover' => "$folder/cover.jpg",
            'images' => ["$folder/image1.jpg", "$folder/image2.jpg", "$folder/image2.jpg"],
            'visibility' => $this->faker->randomElement(['public', 'private', 'group']),
        ];
    }
}
