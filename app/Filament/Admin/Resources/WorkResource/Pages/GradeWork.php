<?php

namespace App\Filament\Admin\Resources\WorkResource\Pages;

use App\Filament\Admin\Resources\WorkResource;
use App\Models\Criteria;
use App\Models\Group;
use App\Models\Level;
use App\Models\Work;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;

class GradeWork extends EditRecord
{
    protected static string $resource = WorkResource::class;

    public string|int|null|Model|Work $record;

    protected static ?string $title = 'Calificar Trabajo';

    protected array|Collection $criterias = [];

    protected array $levelCache = [];

    protected function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->formId('form'),
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([

                Components\Section::make('Información general')
                    ->columns(1)
                    ->columnSpan(1)
                    ->disabled()
                    ->schema([
                        Components\FileUpload::make('cover')
                            ->hiddenLabel()
                            ->deletable(false),
                        Components\Select::make('group_id')
                            ->label('Grupo')
                            ->relationship('group', 'period')
                            ->getOptionLabelFromRecordUsing(fn (Group $record) => "$record->period - $record->title"),
                        Components\Select::make('project_id')
                            ->label('Proyecto')
                            ->options(fn (Get $get): array => Group::where('id', $get('group_id'))
                                ->first()->projects->pluck('title', 'id')
                                ->toArray()
                            ),
                        Components\Select::make('user_id')
                            ->label('Estudiante')
                            ->options(fn (Get $get): array => Group::where('id', $get('group_id'))
                                ->first()->students->pluck('name', 'id')
                                ->toArray()
                            ),
                    ]),

                Components\Section::make('Imágenes')
                    ->columnSpan(2)
                    ->disabled()
                    ->collapsible()
                    ->schema([
                        Components\FileUpload::make('images')
                            ->hiddenLabel()
                            ->panelLayout('grid')
                            ->deletable(false)
                            ->multiple(),
                    ]),

                Components\Section::make('Criterios de evaluación')
                    ->collapsible()
                    ->hiddenOn('create')
                    ->schema([
                        Components\Repeater::make('rubrics')
                            ->columnSpan('full')
                            ->hiddenLabel()
                            ->deletable(false)
                            ->orderColumn(false)
                            ->addable(false)
                            ->itemLabel(fn (array $state): ?string => $state['title']
                                ? "{$state['order']}. {$state['title']}"
                                : null
                            )
                            ->required()
                            ->schema([
                                RadioDeck::make('level_id')
                                    ->hiddenLabel()
                                    ->required()
                                    ->options(function ($get) {
                                        $criteriaId = $get('./')['id'];
                                        if (! isset($this->levelCache[$criteriaId])) {
                                            $this->levelCache[$criteriaId] = Level::where('criteria_id', $criteriaId)->get();
                                        }

                                        return $this->levelCache[$criteriaId]
                                            ->mapWithKeys(fn ($item) => [
                                                $item->id => "{$item->title} ({$item->score} pt)",
                                            ]);
                                    })
                                    ->descriptions(function ($get) {
                                        $criteriaId = $get('./')['id'];
                                        if (! isset($this->levelCache[$criteriaId])) {
                                            $this->levelCache[$criteriaId] = Level::where('criteria_id', $criteriaId)->get();
                                        }

                                        return $this->levelCache[$criteriaId]
                                            ->mapWithKeys(fn ($item) => [
                                                $item->id => $item->description,
                                            ]);
                                    })
                                    ->validationMessages([
                                        'required' => 'Selecciona un nivel de desempeño.',
                                    ])
                                    ->extraCardsAttributes([
                                        'class' => 'flex-col justify-start items-start peer-checked:bg-primary-100',
                                    ])
                                    ->extraOptionsAttributes([
                                        'class' => 'w-full flex flex-col items-start justify-start',
                                    ])
                                    ->extraDescriptionsAttributes([
                                        'class' => 'leading-normal',
                                    ])
                                    ->color('primary')
                                    ->columns(4),
                            ]),
                    ]),
            ]);
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
        $this->record->rubrics()->sync($this->criterias->mapWithKeys(fn ($rubric) => [
            $rubric['id'] => ['level_id' => $rubric['level_id']],
        ]));

        $this->record->update([
            'score' => $this->record->scores->sum(fn ($rubric) => $rubric->level->score),
        ]);
    }
}
