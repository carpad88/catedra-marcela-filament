<?php

use App\Filament\Admin\Resources\ProjectResource;
use App\Models\Project;

use function Pest\Livewire\livewire;

it('renders the projects page and displays only owned projects', function () {
    actingAsWithPermissions('project', ['view']);

    Project::factory(2)->create(); // Projects not owned by the authenticated user
    $userProjects = Project::factory(2)->create(['owner_id' => auth()->id()]); // Owned projects

    test()->get(ProjectResource::getUrl())
        ->assertSuccessful();

    livewire(ProjectResource\Pages\ListProjects::class)
        ->assertTableColumnExists('title')
        ->assertTableColumnExists('status')
        ->assertTableColumnExists('started_at')
        ->assertTableColumnExists('finished_at')
        ->assertCountTableRecords(2)
        ->assertCanSeeTableRecords($userProjects);

    test()->assertDatabaseCount(Project::class, 4);
});

it('prevents unauthorized users from accessing the projects page', function () {
    actingAsWithPermissions('project', []);

    test()->get(ProjectResource::getUrl())
        ->assertForbidden()
        ->assertSee('403');
});

it('prevents guests from accessing the admin projects page', function () {
    test()->get(ProjectResource::getUrl())
        ->assertRedirect('admin/login');
});

it('allows authorized users to view a project page', function () {})
    ->skip('Not implemented');

it('prevents unauthorized users from accessing the view project page', function () {})
    ->skip('Not implemented');

it('allows authorized users to create a new project', function () {
    actingAsWithPermissions('project', ['view', 'create']);

    $newProjectData = Project::factory()->make([
        'started_at' => now(),
        'finished_at' => now()->addDays(15),
    ])->toArray();

    test()->get(ProjectResource::getUrl('create'))
        ->assertSuccessful();

    livewire(ProjectResource\Pages\CreateProject::class)
        ->fillForm([
            'cover' => [$newProjectData['cover']],
            'title' => $newProjectData['title'],
            'started_at' => $newProjectData['started_at'],
            'finished_at' => $newProjectData['finished_at'],
            'description' => $newProjectData['description'],
            'goals' => $newProjectData['goals'],
            'activities' => $newProjectData['activities'],
            'conditions' => $newProjectData['conditions'],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $createdProject = Project::where('title', $newProjectData['title'])->first();

    expect($createdProject)->not->toBeNull()
        ->and($createdProject->title)->toBe($newProjectData['title'])
        ->and($createdProject->description)->toBe($newProjectData['description']);
});

it('prevents unauthorized users from accessing the create project page', function () {
    actingAsWithPermissions('project', []);

    test()->get(ProjectResource::getUrl('create'))
        ->assertForbidden()
        ->assertSee('403');
});

it('allows authorized users to update a project', function () {
    actingAsWithPermissions('project', ['view', 'update']);

    $project = Project::factory()->create(['owner_id' => auth()->id()]);
    $newTitle = 'New Title';

    test()->get(ProjectResource::getUrl('edit', ['record' => $project]))
        ->assertSuccessful();

    livewire(ProjectResource\Pages\EditProject::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertFormSet([
            'title' => $project->title,
            'started_at' => $project->started_at,
            'finished_at' => $project->finished_at,
        ])
        ->fillForm([
            'cover' => [$project->cover],
            'title' => $newTitle,
            'started_at' => now(),
            'finished_at' => now()->addDays(15),
            'description' => $project->description,
            'goals' => $project->goals,
            'activities' => $project->activities,
            'conditions' => $project->conditions,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($project->refresh())->title->toBe($newTitle);
});

it('prevents unauthorized users from accessing the edit project page', function () {
    actingAsWithPermissions('project', []);

    $project = Project::factory()->create();

    test()->get(ProjectResource::getUrl('edit', ['record' => $project]))
        ->assertForbidden()
        ->assertSee('403');
});

it('allows authorized users to delete a project', function () {
    actingAsWithPermissions('project', ['view', 'delete']);

    $project = Project::factory()->create(['owner_id' => auth()->id()]);

    livewire(ProjectResource\Pages\ListProjects::class)
        ->assertTableActionExists('delete')
        ->callTableAction('delete', $project->getRouteKey());

    test()->assertModelMissing($project);
});

it('prevents unauthorized users from deleting a project', function () {
    actingAsWithPermissions('project', ['view']);

    $project = Project::factory()->create();

    livewire(ProjectResource\Pages\ListProjects::class)
        ->assertTableActionDisabled('delete', $project);
});

it('allows authorized users to duplicate a project', function () {
    actingAsWithPermissions('project', ['view', 'create', 'replicate']);

    $originalProject = Project::factory()->create(['owner_id' => auth()->id()]);

    livewire(ProjectResource\Pages\ListProjects::class)
        ->assertTableActionExists('replicate')
        ->callTableAction('replicate', $originalProject->getRouteKey());

    $duplicatedProject = Project::where('title', $originalProject->title.' (Copia)')->first();

    expect($duplicatedProject)->not->toBeNull()
        ->and($duplicatedProject->title)->toBe($originalProject->title.' (Copia)')
        ->and($duplicatedProject->id)->not->toBe($originalProject->id);
});
