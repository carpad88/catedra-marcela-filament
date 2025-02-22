<?php

namespace App\Filament\Admin\Resources\GroupResource\RelationManagers;

use App\Enums\Status;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Columns;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = 'Proyectos';

    protected static ?string $icon = 'phosphor-calendar-dots-duotone';

    public function isReadOnly(): bool
    {
        return false;
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->projects->count();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('started_at')
                    ->label('Fecha de inicio')
                    ->native(false)
                    ->required(),
                Forms\Components\DatePicker::make('finished_at')
                    ->label('Fecha de entrega')
                    ->native(false)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->paginated(false)
            ->columns([
                Columns\TextColumn::make('title')
                    ->label('Título'),
                Columns\TextColumn::make('started_at')
                    ->label('Comienzo')
                    ->dateTime('M j, Y'),
                Columns\TextColumn::make('finished_at')
                    ->label('Entrega')
                    ->dateTime('M j, Y'),
                Columns\TextColumn::make('works_count')
                    ->counts([
                        'works' => fn (Builder $query) => $query
                            ->where('group_id', '=', $this->getOwnerRecord()->id),
                    ])
                    ->label('Trabajos')
                    ->badge()
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Vincular proyecto')
                    ->icon('phosphor-calendar-plus-duotone')
                    ->visible(function () {
                        $activeProjects = Project::whereStatus('active')->get();
                        $attachedProjects = $this->getOwnerRecord()->projects;

                        $unattachedProjects = $activeProjects->diff($attachedProjects);

                        return $this->getOwnerRecord()->status == Status::Active && $unattachedProjects->isNotEmpty();
                    })
                    ->modalHeading('Vincular proyecto')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn ($query) => $query->whereStatus('active'))
                    ->recordSelect(fn (Select $select) => $select->searchable(false))
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\Split::make([
                            Forms\Components\DatePicker::make('started_at')
                                ->columnSpan(1)
                                ->label('Fecha de inicio')
                                ->native(false)
                                ->default(now())
                                ->required(),
                            Forms\Components\DatePicker::make('finished_at')
                                ->columnSpan(1)
                                ->label('Fecha de entrega')
                                ->native(false)
                                ->default(now()->addDays(10))
                                ->required(),
                        ]),
                    ])
                    ->after(function ($record) {
                        $this->getOwnerRecord()
                            ->students()
                            ->whereNotNull('email_verified_at')
                            ->get()
                            ->map(function ($student) use ($record) {
                                $student->works()
                                    ->firstOrCreate([
                                        'project_id' => $record->id,
                                        'group_id' => $this->getOwnerRecord()->id,
                                    ]);
                            });
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('lg'),
                Tables\Actions\DetachAction::make()
                    ->visible(fn () => $this->getOwnerRecord()->status == Status::Active)
                    ->icon('phosphor-calendar-minus-duotone')
                    ->modalIcon('phosphor-calendar-minus-duotone')
                    ->modalHeading('Desvincular proyecto del grupo'),
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateHeading('No se encontraron proyectos')
            ->emptyStateDescription('Vincula proyectos a este grupo para que aparezcan aquí.');
    }
}
