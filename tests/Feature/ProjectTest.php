<?php

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    test()->actingAs(User::factory()->create());
});

test('can render projects page and view only owned projects', function () {
    givePermissions('project', ['view']);

    Project::factory(2)->create();
    $userProjects = Project::factory(2)->create(['owner_id' => auth()->id()]);

    test()->get(ProjectResource::getUrl())->assertSuccessful();

    livewire(ProjectResource\Pages\ListProjects::class)
        ->assertTableColumnExists('title')
        ->assertTableColumnExists('status')
        ->assertTableColumnExists('started_at')
        ->assertTableColumnExists('finished_at')
        ->assertCountTableRecords(2)
        ->assertCanSeeTableRecords($userProjects);

    test()->assertDatabaseCount(Project::class, 4);
});

test('unauthorized users cannot render projects page', function () {
    test()->get(ProjectResource::getUrl())
        ->assertForbidden()
        ->assertSee('403');
});

it('can create a new project', function () {
    givePermissions('project', ['view', 'create']);

    $item = Project::factory()->make();

    test()->get(ProjectResource::getUrl('create'))->assertSuccessful();

    livewire(ProjectResource\Pages\CreateProject::class)
        ->fillForm([
            'cover' => [$item->cover],
            'title' => $item->title,
            'started_at' => now(),
            'finished_at' => now()->addDays(15),
            'description' => $item->description,
            'goals' => $item->goals,
            'activities' => $item->activities,
            'conditions' => $item->conditions,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    test()->assertModelExists($item->fresh());
});

test('unauthorized users cannot render create project page', function () {
    test()->get(ProjectResource::getUrl('create'))
        ->assertForbidden()
        ->assertSee('403');
});

//it('can view project page', function () {
//    givePermissions('project', ['view']);
//
//    test()->get(ProjectResource::getUrl('view', [
//        'record' => Project::factory()->create(['owner_id' => auth()->id()]),
//    ]))->assertSuccessful();
//
//    $item = Project::factory()->create(['owner_id' => auth()->id()]);
//
//    livewire(ProjectResource\Pages\ViewProject::class, [
//        'record' => $item->getRouteKey(),
//    ])
//        ->assertFormSet([
//            'title' => $item->title,
//            'started_at' => $item->started_at,
//            'finished_at' => $item->finished_at,
//        ]);
//});

//test('unauthorized users cannot render view project page', function () {
//    test()->get(ProjectResource::getUrl('view', ['record' => Project::factory()->create()]))
//        ->assertForbidden()
//        ->assertSee('403');
//});

it('can update a project', function () {
    givePermissions('project', ['view', 'update']);

    $item = Project::factory()->create(['owner_id' => auth()->id()]);
    $newTitle = 'New Title';

    test()->get(ProjectResource::getUrl('edit', ['record' => $item]))->assertSuccessful();

    livewire(ProjectResource\Pages\EditProject::class, [
        'record' => $item->getRouteKey(),
    ])
        ->assertFormSet([
            'title' => $item->title,
            'started_at' => $item->started_at,
            'finished_at' => $item->finished_at,
        ])->fillForm([
            'cover' => [$item->cover],
            'title' => $newTitle,
            'started_at' => now(),
            'finished_at' => now()->addDays(15),
            'description' => $item->description,
            'goals' => $item->goals,
            'activities' => $item->activities,
            'conditions' => $item->conditions,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($item->refresh())->title->toBe($newTitle);
});

test('unauthorized users cannot render edit project page', function () {
    test()->get(ProjectResource::getUrl('edit', ['record' => Project::factory()->create()]))
        ->assertForbidden()
        ->assertSee('403');
});

it('can delete a project', function () {
    givePermissions('project', ['view', 'delete']);

    $item = Project::factory()->create(['owner_id' => auth()->id()]);

    livewire(ProjectResource\Pages\ListProjects::class)
        ->assertTableActionExists('delete')
        ->callTableAction('delete', $item);

    test()->assertModelMissing($item);
});

test('unauthorized users cannot delete a project', function () {
    givePermissions('project', ['view']);

    livewire(ProjectResource\Pages\ListProjects::class)
        ->assertTableActionDisabled('delete', Project::factory()->create());
});
