<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Enums\Status;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = 'Proyectos';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
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
                    ->visible(fn () => $this->getOwnerRecord()->status == Status::Active)
                    ->modalHeading('Vincular proyecto')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn ($query) => $query->whereStatus('active'))
                    ->recordSelect(fn (Select $select) => $select->searchable(false))
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
                                // TODO: create the project folder for each STUDENT
                            });
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->visible(fn () => $this->getOwnerRecord()->status == Status::Active)
                    ->icon(null),
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateHeading('No se encontraron proyectos')
            ->emptyStateDescription('Vincula proyectos a este grupo para que aparezcan aquí.');
    }
}
