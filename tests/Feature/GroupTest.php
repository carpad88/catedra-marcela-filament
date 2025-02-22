<?php

use App\Filament\Admin\Resources\GroupResource;
use App\Filament\Admin\Resources\GroupResource\Pages\ManageGroups;
use App\Filament\Admin\Resources\GroupResource\RelationManagers\ProjectsRelationManager;
use App\Filament\Admin\Resources\GroupResource\RelationManagers\UsersRelationManager;
use App\Filament\Admin\Resources\GroupResource\RelationManagers\WorksRelationManager;
use App\Filament\Admin\Resources\WorkResource;
use App\Models\Group;
use App\Models\Project;
use App\Models\User;

use function Pest\Livewire\livewire;

it('renders the groups page and displays only owned groups', function () {
    actingAsWithPermissions('group', ['view'], 'teacher');

    Group::factory(2)->create();
    $ownedGroups = Group::factory(2)->create(['owner_id' => auth()->id()]);

    test()->get(GroupResource::getUrl())
        ->assertSuccessful();

    livewire(ManageGroups::class)
        ->assertTableColumnExists('title')
        ->assertTableColumnExists('year')
        ->assertTableColumnExists('cycle')
        ->assertTableColumnHidden('owner.name')
        ->assertCountTableRecords(2)
        ->assertCanSeeTableRecords($ownedGroups);

    test()->assertDatabaseCount(Group::class, 4);
});

it('prevents unauthorized users from accessing the groups page', function () {
    actingAsWithPermissions('group', []);

    test()->get(GroupResource::getUrl())
        ->assertForbidden()
        ->assertSee('403');
});

it('allows authorized users to create a new group', function () {
    actingAsWithPermissions('group', ['view', 'create']);

    $newGroupData = Group::factory()->make()->toArray();

    livewire(ManageGroups::class)
        ->assertActionExists('create')
        ->assertActionEnabled('create')
        ->callAction('create', $newGroupData)
        ->assertHasNoActionErrors();

    $createdGroup = Group::where('title', $newGroupData['title'])->first();

    expect($createdGroup)->not->toBeNull()
        ->and($createdGroup->title)->toBe($newGroupData['title'])
        ->and($createdGroup->year)->toBe(intval($newGroupData['year']));
});

it('prevents unauthorized users from seeing the create action', function () {
    actingAsWithPermissions('group', ['view']);

    livewire(ManageGroups::class)
        ->assertActionDisabled('create');
});

it('allows authorized users to update a group', function () {
    actingAsWithPermissions('group', ['view', 'update']);

    $group = Group::factory()->create(['owner_id' => auth()->id()]);
    $newTitle = 'New Title';

    livewire(ManageGroups::class)
        ->mountTableAction('edit', $group)
        ->assertTableActionDataSet(['title' => $group->title])
        ->setTableActionData(['title' => $newTitle])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($group->fresh()->title)->toBe($newTitle);
});

it('prevents unauthorized users from seeing the edit action', function () {
    actingAsWithPermissions('group', ['view']);

    $group = Group::factory()->create();

    livewire(ManageGroups::class)
        ->assertTableActionDisabled('edit', $group);
});

it('allows authorized users to delete a group', function () {
    actingAsWithPermissions('group', ['view', 'delete']);

    $group = Group::factory()->create(['owner_id' => auth()->id()]);

    livewire(ManageGroups::class)
        ->callTableAction('delete', $group->getRouteKey())
        ->assertNotified()
        ->assertCanNotSeeTableRecords([$group]);

    test()->assertModelMissing($group);
});

it('allows authorized users to bulk delete groups', function () {
    actingAsWithPermissions('group', ['view', 'delete']);

    $groups = Group::factory(2)->create(['owner_id' => auth()->id()]);

    livewire(ManageGroups::class)
        ->callTableBulkAction('delete', $groups)
        ->assertNotified()
        ->assertCanNotSeeTableRecords($groups);

    foreach ($groups as $group) {
        test()->assertModelMissing($group);
    }
});

it('prevents unauthorized users from seeing the delete action', function () {
    actingAsWithPermissions('group', ['view']);

    $group = Group::factory()->create();

    livewire(ManageGroups::class)
        ->assertTableActionDisabled('delete', $group);
});

it('renders the users relation manager', function () {
    actingAsWithPermissions('group', ['view']);

    $group = Group::factory()
        ->hasUsers(3)
        ->create(['owner_id' => auth()->id()]);

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => \App\Filament\Admin\Resources\GroupResource\Pages\ViewGroup::class,
    ])
        ->assertSuccessful()
        ->assertTableActionExists('create')
        ->assertTableActionExists('import')
        ->assertTableActionExists('attach')
        ->assertCountTableRecords(3)
        ->assertCanSeeTableRecords($group->users);
});

it('allows authorized users to attach a user to a group', function () {
    actingAsWithPermissions('group', ['view', 'update']);

    $group = Group::factory()->withProjects(2)->create(['status' => 'active']);
    $user = User::factory()->create();

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => \App\Filament\Admin\Resources\GroupResource\Pages\ViewGroup::class,
    ])
        ->assertTableActionExists('attach')
        ->callTableAction('attach', data: ['recordId' => $user->getRouteKey()])
        ->assertCanSeeTableRecords([$user]);

    expect($user->works()->count())->toBe(2);
});

it('allows authorized users to remove a user from a group', function () {
    actingAsWithPermissions('group', ['view', 'update']);

    $group = Group::factory()->create();
    $user = User::factory()->create();

    $group->users()->attach($user);

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => \App\Filament\Admin\Resources\GroupResource\Pages\ViewGroup::class,
    ])
        ->mountTableAction('detach', $user)
        ->assertTableActionExists('detach')
        ->callMountedTableAction()
        ->assertCanNotSeeTableRecords([$user]);
});

it('renders the projects relation manager', function () {
    actingAsWithPermissions('group', ['view']);

    $group = Group::factory()
        ->withProjects(3, ['owner_id' => auth()->id()])
        ->create(['owner_id' => auth()->id()]);

    livewire(ProjectsRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => \App\Filament\Admin\Resources\GroupResource\Pages\ViewGroup::class,
    ])
        ->assertSuccessful()
        ->assertTableActionExists('attach')
        ->assertCountTableRecords(3)
        ->assertCanSeeTableRecords($group->projects);
});

it('allows authorized users to attach a project to a group', function () {
    actingAsWithPermissions('group', ['view', 'update']);

    $group = Group::factory()
        ->has(User::factory()->count(2)->students())
        ->create(['owner_id' => auth()->id(), 'status' => 'active']);
    $project = Project::factory()->create(['owner_id' => auth()->id(), 'status' => 'active']);

    livewire(ProjectsRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => \App\Filament\Admin\Resources\GroupResource\Pages\ViewGroup::class,
    ])
        ->assertTableActionExists('attach')
        ->callTableAction('attach', data: ['recordId' => $project->getRouteKey()])
        ->assertCanSeeTableRecords([$project]);

    expect($group->projects()->count())->toBe(1)
        ->and($group->works->count())->toBe(2);
});

it('allows authorized users to remove a project from a group', function () {
    actingAsWithPermissions('group', ['view', 'update']);

    $group = Group::factory()->create();
    $project = Project::factory()->create();

    $group->projects()->attach($project, ['started_at' => now(), 'finished_at' => now()->addDays(30)]);

    livewire(ProjectsRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => \App\Filament\Admin\Resources\GroupResource\Pages\ViewGroup::class,
    ])
        ->mountTableAction('detach', $project)
        ->assertTableActionExists('detach')
        ->callMountedTableAction()
        ->assertCanNotSeeTableRecords([$project]);
});

it('renders the works relation manager', function () {
    actingAsWithPermissions('group', ['view']);

    $group = Group::factory()
        ->withProjects(3)
        ->create(['owner_id' => auth()->id()]);
    $user = User::factory()->create();

    \App\Actions\Users\CreateUserWorks::handle($group, $user);

    livewire(WorksRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => \App\Filament\Admin\Resources\GroupResource\Pages\ViewGroup::class,
    ])
        ->assertSuccessful()
        ->assertTableActionExists('rubric')
        ->assertCountTableRecords(3)
        ->assertCanSeeTableRecords($group->works);
});

it('allows authorized users to create works in the relation manager', function () {
    actingAsWithPermissions('group', ['view', 'update']);
    actingAsWithPermissions('work', ['create']);

    $group = Group::factory()
        ->withProjects(1, ['owner_id' => auth()->id()])
        ->create(['owner_id' => auth()->id(), 'status' => 'active']);
    $user = User::factory()->create();

    $group->users()->attach($user);

    livewire(WorksRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => \App\Filament\Admin\Resources\GroupResource\Pages\ViewGroup::class,
    ])
        ->assertTableActionExists('create')
        ->callTableAction('create', data: [
            'project_id' => $group->projects->first()->id,
            'user_id' => $user->getRouteKey(),
        ])
        ->assertHasNoActionErrors()
        ->assertRedirect(WorkResource::getUrl('edit', ['record' => $group->works->first()]));

    expect($group->works()->count())->toBe(1);
});
