<?php

namespace App\Filament\App\Resources\WorkResource\Pages;

use App\Actions\Works\PrepareRubricAction;
use App\Filament\Admin\Resources\WorkResource;
use App\Models\Level;
use App\Models\Work;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;

class GradeWork extends EditRecord
{
    protected static string $resource = WorkResource::class;

    public string|int|null|Model|Work $record;

    protected array|Collection $criterias = [];

    protected function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->formId('form')
                ->visible($this->shouldAllowGrade()),
        ];
    }

    public function getTitle(): \Illuminate\Contracts\Support\Htmlable|string
    {
        return 'Rúbrica '.$this->record->project->title;
    }

    protected function getRedirectUrl(): ?string
    {
        return WorkResource::getUrl();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->disabled(! $this->shouldAllowGrade())
            ->schema([
                Components\Repeater::make('rubrics')
                    ->hiddenLabel()
                    ->columnSpan('full')
                    ->collapsible()
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
                            ->options(fn ($get) => Level::where('criteria_id', $get('./')['id'])
                                ->get()
                                ->mapWithKeys(fn ($item) => [$item->id => "{$item->title} ({$item->score} pt)"])
                            )
                            ->validationMessages([
                                'required' => 'Selecciona un nivel de desempeño.',
                            ])
                            ->descriptions(fn ($get) => Level::where('criteria_id', $get('./')['id'])
                                ->get()
                                ->mapWithKeys(fn ($item) => [$item->id => $item->description])
                            )
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
            ]);
    }

    protected function getFormActions(): array
    {
        return $this->shouldAllowGrade() ? [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ] : [];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['rubrics'] = PrepareRubricAction::handle($this->record);

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

    protected function shouldAllowGrade(): bool
    {
        return now() < $this->record->project->finished_at->addDays(3);
    }
}
