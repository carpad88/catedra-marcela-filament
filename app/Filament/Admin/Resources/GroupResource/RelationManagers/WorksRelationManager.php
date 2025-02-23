<?php

namespace App\Filament\Admin\Resources\GroupResource\RelationManagers;

use App\Enums\Status;
use App\Filament\Admin\Resources\WorkResource;
use App\Models\Group;
use App\Models\Work;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WorksRelationManager extends RelationManager
{
    protected static string $relationship = 'works';

    protected static ?string $title = 'Trabajos';

    protected static ?string $label = 'Trabajo';

    protected static ?string $icon = 'phosphor-images-duotone';

    public function isReadOnly(): bool
    {
        return false;
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->works->count();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('project_id')
                    ->label('Proyecto')
                    ->options(fn (): array => Group::whereId($this->getOwnerRecord()->id)
                        ->first()
                        ->projects
                        ->pluck('title', 'id')
                        ->toArray()
                    )
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->label('Estudiante')
                    ->options(fn () => Group::whereId($this->getOwnerRecord()->id)
                        ->first()
                        ->students()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                    )
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort(fn (Builder $query) => $query
                ->select('works.*')
                ->join('users', 'works.user_id', '=', 'users.id')
                ->join('projects', 'works.project_id', '=', 'projects.id')
                ->orderBy('projects.title')
                ->orderBy('users.name')
            )
            ->recordTitleAttribute('id')
            ->recordUrl(false)
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                    ->label('Portada')
                    ->height(80)
                    ->defaultImageUrl(url('images/placeholder.svg')),
                Tables\Columns\TextColumn::make('project.title')
                    ->label('Proyecto')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('started')
                    ->label('Comienzo')
                    ->dateTime('M j, Y'),
                Tables\Columns\TextColumn::make('finished')
                    ->label('Entrega')
                    ->dateTime('M j, Y'),
                Tables\Columns\TextColumn::make('score')
                    ->label('Calificación')
                    ->alignCenter()
                    ->badge(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project_id')
                    ->label('Proyecto')
                    ->native(false)
                    ->options(fn () => $this->getOwnerRecord()->projects->pluck('title', 'id')->toArray()),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => $this->getOwnerRecord()->status == Status::Active)
                    ->modalWidth('2xl')
                    ->using(fn ($data) => Work::updateOrCreate(
                        [
                            'group_id' => $this->getOwnerRecord()->id,
                            'project_id' => $data['project_id'],
                            'user_id' => $data['user_id'],
                        ], $data)
                    )
                    ->successRedirectUrl(fn (Work $record) => WorkResource::getUrl('edit', ['record' => $record])),
            ])
            ->actions([
                Tables\Actions\Action::make('rubric')
                    ->label('Rúbrica')
                    ->icon('phosphor-exam-duotone')
                    ->color(Color::Green)
                    ->url(fn ($record) => WorkResource::getUrl('rubric', ['record' => $record])),
                Tables\Actions\Action::make('edit')
                    ->label('Editar')
                    ->icon('phosphor-pencil-duotone')
                    ->url(fn ($record) => WorkResource::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No se encontraron trabajos')
            ->emptyStateDescription('Agrega trabajos a este grupo para que aparezcan aquí.');
    }
}
