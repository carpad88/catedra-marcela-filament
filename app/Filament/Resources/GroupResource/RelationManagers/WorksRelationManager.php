<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Filament\Resources\WorkResource;
use App\Models\Group;
use App\Models\Project;
use App\Models\User;
use App\Models\Work;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorksRelationManager extends RelationManager
{
    protected static string $relationship = 'works';

    protected static ?string $title = 'Trabajos';

    protected static ?string $label = 'Trabajo';

    public function isReadOnly(): bool
    {
        return false;
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
                ->join('projects', 'works.project_id', '=', 'projects.id')
                ->join('users', 'works.user_id', '=', 'users.id')
                ->orderBy('projects.finished_at', 'desc')
                ->orderBy('users.name')
                ->select('works.*')
            )
            ->recordTitleAttribute('id')
            ->recordUrl(fn ($record) => WorkResource::getUrl('edit', ['record' => $record]))
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                    ->label('Portada')
                    ->height(100)
                    ->defaultImageUrl(url('images/placeholder.png')),
                Tables\Columns\TextColumn::make('project.title')
                    ->label('Proyecto')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.started_at')
                    ->label('Comienzo')
                    ->dateTime('M j, Y'),
                Tables\Columns\TextColumn::make('project.finished_at')
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
                    ->modalWidth('2xl')
                    ->using(function ($data, $model) {
                        $user = User::where('id', $data['user_id'])->first();
                        $project = Project::where('id', $data['project_id'])->first();

                        return $model::updateOrCreate([
                            'group_id' => $this->getOwnerRecord()->id,
                            'project_id' => $data['project_id'],
                            'user_id' => $data['user_id'],
                        ], [
                            'folder' => "{$this->getOwnerRecord()->folderName}/$user->folderName/$project->folderName",
                        ]);

                    })
                    ->successRedirectUrl(fn (Work $record) => WorkResource::getUrl('edit', ['record' => $record])),
            ])
            ->actions([
                Tables\Actions\Action::make('Rúbrica')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->url(fn ($record) => WorkResource::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
