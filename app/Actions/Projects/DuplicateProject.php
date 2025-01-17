<?php

namespace App\Actions\Projects;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

class DuplicateProject
{
    public static function handle(Model|Project $record): Application|Redirector|RedirectResponse
    {
        $replica = Project::create([
            'owner_id' => auth()->id(),
            'cover' => $record->cover,
            'title' => $record->title.' (Copia)',
            'started_at' => now(),
            'finished_at' => now()->addDays(10),
            'description' => $record->description,
            'goals' => $record->goals,
            'activities' => $record->activities,
            'conditions' => $record->conditions,
        ]);

        $replica->save();

        foreach ($record->criterias as $criteria) {
            $replicaCriteria = $criteria->replicate();
            $replicaCriteria->project_id = $replica->id;
            $replicaCriteria->save();

            foreach ($criteria->levels as $level) {
                $replicaLevel = $level->replicate();
                $replicaLevel->criteria_id = $replicaCriteria->id;
                $replicaLevel->save();
            }
        }

        Notification::make()
            ->title('Proyecto duplicado')
            ->body('El proyecto "'.$replica->title.'" ha sido duplicado correctamente.')
            ->success()
            ->send();

        return redirect(ProjectResource::getUrl('edit', ['record' => $replica->id]));
    }
}
