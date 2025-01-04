<?php

use App\Filament\Resources\GroupResource;
use App\Filament\Resources\GroupResource\Pages\ManageGroups;
use App\Filament\Resources\GroupResource\RelationManagers\ProjectsRelationManager;
use App\Filament\Resources\GroupResource\RelationManagers\UsersRelationManager;
use App\Filament\Resources\GroupResource\RelationManagers\WorksRelationManager;
use App\Filament\Resources\WorkResource;
use App\Models\Group;
use App\Models\Project;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    test()->actingAs(User::factory()->create());
});

it('can render groups page and view only owned groups', function () {
    givePermissions('group', ['view']);

    Group::factory(2)->create();
    $userGroups = Group::factory(2)->create(['owner_id' => auth()->id()]);

    test()->get(GroupResource::getUrl())->assertSuccessful();

    livewire(ManageGroups::class)
        ->assertTableColumnExists('title')
        ->assertTableColumnExists('year')
        ->assertTableColumnExists('cycle')
        ->assertTableColumnHidden('owner.name')
        ->assertCountTableRecords(2)
        ->assertCanSeeTableRecords($userGroups);

    test()->assertDatabaseCount(Group::class, 4);
});

test('unauthorized users cannot render groups page', function () {
    test()->get(GroupResource::getUrl())
        ->assertForbidden()
        ->assertSee('403');
});

it('can create a new group', function () {
    givePermissions('group', ['view', 'create']);

    $item = Group::factory()->make();

    livewire(ManageGroups::class)
        ->assertActionExists('create')
        ->assertActionEnabled('create')
        ->callAction('create', $item->toArray())
        ->assertHasNoActionErrors();

    test()->assertModelExists($item->fresh());
});

test('unauthorized users cannot see create action', function () {
    givePermissions('group', ['view']);

    livewire(ManageGroups::class)
        ->assertActionDisabled('create');
});

it('can update a group', function () {
    givePermissions('group', ['view', 'update']);

    $item = Group::factory()->create(['owner_id' => auth()->id()]);
    $newTitle = 'New Title';

    livewire(ManageGroups::class)
        ->mountTableAction('edit', $item)
        ->assertTableActionDataSet(['title' => $item->title])
        ->setTableActionData(['title' => $newTitle])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($item->fresh()->title)->toBe($newTitle);
});

test('unauthorized users cannot see edit action', function () {
    givePermissions('group', ['view', 'update']);

    $item = Group::factory()->create();

    livewire(ManageGroups::class)
        ->assertTableActionDisabled('edit', $item);
});

it('can delete a group', function () {
    givePermissions('group', ['view', 'delete']);

    $item = Group::factory()->create(['owner_id' => auth()->id()]);

    livewire(ManageGroups::class)
        ->callTableAction('delete', $item->getRouteKey())
        ->assertNotified()
        ->assertCanNotSeeTableRecords([$item]);

    test()->assertModelMissing($item);
});

it('can bulk delete groups', function () {
    givePermissions('group', ['view', 'delete']);

    $items = Group::factory(2)->create(['owner_id' => auth()->id()]);

    livewire(ManageGroups::class)
        ->callTableBulkAction('delete', $items)
        ->assertNotified()
        ->assertCanNotSeeTableRecords($items);

    test()->assertModelMissing($items[0]);
});

test('unauthorized users cannot see delete action', function () {
    givePermissions('group', ['view', 'delete']);

    livewire(ManageGroups::class)
        ->assertTableActionDisabled('delete', Group::factory()->create());
});

it('can render the users relation manager', function () {
    givePermissions('group', ['view']);

    $group = Group::factory()
        ->hasUsers(3)
        ->create(['owner_id' => auth()->id()]);

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => GroupResource\Pages\ViewGroup::class,
    ])
        ->assertSuccessful()
        ->assertTableActionExists('create')
        ->assertTableActionExists('import')
        ->assertTableActionExists('attach')
        ->assertCountTableRecords(3)
        ->assertCanSeeTableRecords($group->users);
});

it('can attach a user to a group', function () {
    givePermissions('group', ['view', 'update']);

    $group = Group::factory()->hasProjects(2)->create(['status' => 'active']);
    $user = User::factory()->create();

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => GroupResource\Pages\ViewGroup::class,
    ])
        ->assertTableActionExists('attach')
        ->callTableAction('attach', data: ['recordId' => $user->getRouteKey()])
        ->assertCanSeeTableRecords([$user]);

    expect($user->works()->count())->toBe(2);
});

it('can remove a user from a group', function () {
    givePermissions('group', ['view', 'update']);

    $group = Group::factory()->create();
    $user = User::factory()->create();

    $group->users()->attach($user);

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => GroupResource\Pages\ViewGroup::class,
    ])
        ->mountTableAction('detach', $user)
        ->assertTableActionExists('detach')
        ->callMountedTableAction()
        ->assertCanNotSeeTableRecords([$user]);
});

it('can render the projects relation manager', function () {
    givePermissions('group', ['view']);

    $group = Group::factory()
        ->hasProjects(3, ['owner_id' => auth()->id()])
        ->create(['owner_id' => auth()->id()]);

    livewire(ProjectsRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => GroupResource\Pages\ViewGroup::class,
    ])
        ->assertSuccessful()
        ->assertTableActionExists('attach')
        ->assertCountTableRecords(3)
        ->assertCanSeeTableRecords($group->projects);
});

it('can attach a project to a group', function () {
    givePermissions('group', ['view', 'update']);

    $group = Group::factory()
        ->has(User::factory()->count(2)->students())
        ->create(['owner_id' => auth()->id(), 'status' => 'active']);
    $item = Project::factory()->create(['owner_id' => auth()->id()]);

    livewire(ProjectsRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => GroupResource\Pages\ViewGroup::class,
    ])
        ->assertTableActionExists('attach')
        ->callTableAction('attach', data: ['recordId' => $item->getRouteKey()])
        ->assertCanSeeTableRecords([$item]);

    expect($group->projects()->count())->toBe(1)
        ->and($group->works->count())->toBe(2);
});

it('can remove a project from a group', function () {
    givePermissions('group', ['view', 'update']);

    $group = Group::factory()->create();
    $item = Project::factory()->create();

    $group->projects()->attach($item);

    livewire(ProjectsRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => GroupResource\Pages\ViewGroup::class,
    ])
        ->mountTableAction('detach', $item)
        ->assertTableActionExists('detach')
        ->callMountedTableAction()
        ->assertCanNotSeeTableRecords([$item]);
});

it('can render works relation manager', function () {
    givePermissions('group', ['view']);

    $group = Group::factory()
        ->hasProjects(3)
        ->create(['owner_id' => auth()->id()]);
    $user = User::factory()->create();

    \App\Actions\Users\CreateUserWorks::handle($group, $user);

    livewire(WorksRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => GroupResource\Pages\ViewGroup::class,
    ])
        ->assertSuccessful()
        ->assertTableActionExists('rubric')
        ->assertCountTableRecords(3)
        ->assertCanSeeTableRecords($group->works);
});

it('can create works in the relation manager', function () {
    givePermissions('group', ['view', 'update']);
    givePermissions('work', ['create']);

    $group = Group::factory()
        ->hasProjects(1, ['owner_id' => auth()->id()])
        ->create(['owner_id' => auth()->id(), 'status' => 'active']);
    $user = User::factory()->create();

    $group->users()->attach($user);

    livewire(WorksRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => GroupResource\Pages\ViewGroup::class,
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
