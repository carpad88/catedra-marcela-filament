<?php

namespace App\Actions\Users;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CreateUserWorks
{
    public static function handle(Group|Model $group, User $user): void
    {
        if (! $user->hasRole('student')) {
            return;
        }

        $group->projects->each(fn ($project) => $user->works()
            ->firstOrCreate([
                'project_id' => $project->id,
                'group_id' => $group->id,
            ])
        );
    }
}
