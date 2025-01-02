<?php

namespace App\Filament\Resources\WorkResource\Pages;

use App\Filament\Resources\WorkResource;
use App\Models\Criteria;
use App\Models\Work;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EditWork extends EditRecord
{
    protected static string $resource = WorkResource::class;

    public string|int|null|Model|Work $record;

    protected array|Collection $criterias = [];

    protected function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->formId('form'),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['rubrics'] = $this->record->rubrics->isEmpty()
            ? Criteria::where('project_id', $this->record->project_id)
                ->select(['id', 'title', 'order'])
                ->get()
                ->map(fn ($criteria) => [
                    'id' => $criteria->id,
                    'title' => $criteria->title,
                    'order' => $criteria->order,
                    'level_id' => null,
                ])
            : $this->record->rubrics->map(fn ($rubric) => [
                'id' => $rubric->id,
                'title' => $rubric->title,
                'order' => $rubric->order,
                'level_id' => $rubric->pivot->level_id,
            ]);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->criterias = collect($data['rubrics'] ?? []);
        unset($data['rubrics']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->rubrics()->sync($this->criterias->map(fn ($rubric) => [
            'criteria_id' => $rubric['id'],
            'level_id' => $rubric['level_id'],
        ]));

        $this->record->update([
            'score' => $this->record->scores->sum(fn ($rubric) => $rubric->level->score),
        ]);
    }
}
