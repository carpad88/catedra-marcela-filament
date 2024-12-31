<?php

namespace App\Filament\Resources\WorkResource\Pages;

use App\Filament\Resources\WorkResource;
use App\Models\Group;
use App\Models\Project;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateWork extends CreateRecord
{
    protected static string $resource = WorkResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $group = Group::where('id', $data['group_id'])->first();
        $user = User::where('id', $data['user_id'])->first();
        $project = Project::where('id', $data['project_id'])->first();

        $data['folder'] = "$group->folderName/$user->folderName/$project->folderName";

        return $data;
    }
}
