<?php

use App\Filament\Admin\Resources\UserResource;
use App\Filament\Admin\Resources\UserResource\Pages\ManageUsers;
use App\Filament\Admin\Resources\UserResource\Pages\ViewUser;
use App\Filament\Admin\Resources\UserResource\RelationManagers\WorksRelationManager;
use App\Models\Group;
use App\Models\User;
use Spatie\Permission\Models\Role;

use function Pest\Livewire\livewire;

it('renders the users page and displays the correct records', function () {
    actingAsWithPermissions('user', ['view'], 'teacher');

    $createdUsers = User::factory(2)->create();

    test()->get(UserResource::getUrl())
        ->assertSuccessful();

    livewire(ManageUsers::class)
        ->assertCountTableRecords(3)
        ->assertCanSeeTableRecords($createdUsers);

    test()->assertDatabaseCount(User::class, 3);
});

it('prevents unauthorized users from accessing the users page', function () {
    actingAsWithPermissions('user', []);

    test()->get(UserResource::getUrl())
        ->assertForbidden()
        ->assertSee('403');
});

it('prevents guests from accessing the admin users page', function () {
    test()->get(UserResource::getUrl())
        ->assertRedirect('admin/login');
});

it('allows authorized users to view a user', function () {
    actingAsWithPermissions('user', ['view'], 'teacher');

    $user = User::factory()->create();

    test()->get(UserResource::getUrl('view', ['record' => $user->getRouteKey()]))
        ->assertSuccessful();

    livewire(ViewUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionExists('edit')
        ->assertSee($user->code)
        ->assertSee($user->name)
        ->assertSee($user->email);
});

it('allows authorized users to create a new user', function () {
    actingAsWithPermissions('user', ['view', 'create'], 'super_admin');

    $newUserData = User::factory()->make()->toArray();
    Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

    livewire(ManageUsers::class)
        ->assertActionExists('create')
        ->assertActionEnabled('create')
        ->callAction('create', $newUserData)
        ->assertHasNoActionErrors();

    $createdUser = User::where('email', $newUserData['email'])->first();

    expect($createdUser)->not->toBeNull()
        ->and($createdUser->first_name)->toBe(str($newUserData['first_name'])->title()->value())
        ->and($createdUser->email)->toBe($newUserData['email']);
});

it('prevents unauthorized users from seeing the create action', function () {
    actingAsWithPermissions('user', ['view']);

    livewire(ManageUsers::class)
        ->assertActionDisabled('create');
});

it('allows authorized users to update a user', function () {
    actingAsWithPermissions('user', ['view', 'update']);

    $user = User::factory()->create();
    $newName = 'New Name';

    livewire(ManageUsers::class)
        ->mountTableAction('edit', $user)
        ->assertTableActionDataSet(['first_name' => $user->first_name])
        ->setTableActionData(['first_name' => $newName])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($user->fresh()->first_name)->toBe($newName);
});

it('prevents unauthorized users from seeing the edit action', function () {
    actingAsWithPermissions('user', ['view']);

    $user = User::factory()->make();

    livewire(ManageUsers::class)
        ->assertTableActionDisabled('edit', $user);
});

it('allows authorized users to delete a single user', function () {
    actingAsWithPermissions('user', ['view', 'delete']);

    $user = User::factory()->create();

    livewire(ManageUsers::class)
        ->callTableAction('delete', $user->getRouteKey())
        ->assertNotified()
        ->assertCanNotSeeTableRecords([$user]);

    test()->assertModelMissing($user);
});

it('prevents unauthorized users from seeing the delete action', function () {
    actingAsWithPermissions('user', ['view']);

    $user = User::factory()->make();

    livewire(ManageUsers::class)
        ->assertTableActionDisabled('delete', $user);
});

it('allows authorized users to bulk delete users', function () {
    actingAsWithPermissions('user', ['view', 'delete']);

    $users = User::factory(3)->create();

    livewire(ManageUsers::class)
        ->callTableBulkAction('delete', $users)
        ->assertNotified()
        ->assertCanNotSeeTableRecords($users);

    foreach ($users as $user) {
        test()->assertModelMissing($user);
    }
});

it('prevents bulk delete with no selection', function () {
    actingAsWithPermissions('user', ['view', 'delete']);

    livewire(ManageUsers::class)
        ->callTableBulkAction('delete', [])
        ->assertHasNoTableActionErrors();
});

it('renders the works relation manager and displays the correct records', function () {
    actingAsWithPermissions('user', ['view']);

    $group = Group::factory()
        ->withProjects(3)
        ->create(['owner_id' => auth()->id()]);
    $user = User::factory()->students()->create();
    $user->groups()->attach($group);

    \App\Actions\Users\CreateUserWorks::handle($user->groups()->first(), $user);

    livewire(WorksRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => UserResource\Pages\ViewUser::class,
    ])
        ->assertSuccessful()
        ->assertCountTableRecords(3)
        ->assertCanSeeTableRecords($group->works);
});
