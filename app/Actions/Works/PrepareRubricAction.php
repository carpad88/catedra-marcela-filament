<?php

namespace App\Actions\Works;

use App\Models\Criteria;
use App\Models\Work;
use Illuminate\Support\Collection;

class PrepareRubricAction
{
    public static function handle(Work $record): Collection
    {
        return app(static::class)->execute($record);
    }

    public function execute(Work $record): Collection
    {
        return $record->rubrics->isEmpty()
            ? $this->getDefaultRubrics($record->project_id)
            : $this->getExistingRubrics($record);
    }

    private function getDefaultRubrics(int $projectId): Collection
    {
        return Criteria::where('project_id', $projectId)
            ->select(['id', 'title', 'order'])
            ->get()
            ->map(fn ($criteria) => $this->mapDefaultCriteria($criteria));
    }

    private function getExistingRubrics(Work $record): Collection
    {
        return $record->rubrics->map(fn ($rubric) => $this->mapExistingRubric($rubric));
    }

    private function mapDefaultCriteria(Criteria $criteria): array
    {
        return [
            'id' => $criteria->id,
            'title' => $criteria->title,
            'order' => $criteria->order,
            'level_id' => null,
        ];
    }

    private function mapExistingRubric($rubric): array
    {
        return [
            'id' => $rubric->id,
            'title' => $rubric->title,
            'order' => $rubric->order,
            'level_id' => $rubric->pivot->level_id,
        ];
    }
}
