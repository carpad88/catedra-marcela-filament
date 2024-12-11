<?php

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\ManageUsers;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    test()->actingAs(User::factory()->create());
});

it('can render users page', function () {
    givePermissions('user', ['view']);
    $users = User::factory(2)->create();

    test()->get(UserResource::getUrl())->assertSuccessful();

    livewire(ManageUsers::class)
        ->assertCountTableRecords(3)
        ->assertCanSeeTableRecords($users);

    test()->assertDatabaseCount(User::class, 3);
});

test('unauthorized users cannot render users page', function () {
    givePermissions('user', []);
    User::factory(2)->create();

    test()->get(UserResource::getUrl())
        ->assertForbidden()
        ->assertSee('403');
});

it('can create a new user', function () {
    givePermissions('user', ['view', 'create']);

    $user = User::factory()->make();

    livewire(ManageUsers::class)
        ->assertActionExists('create')
        ->assertActionEnabled('create')
        ->callAction('create', $user->toArray())
        ->assertHasNoActionErrors();

    test()->assertModelExists($user->fresh());
});

test('unauthorized users cannot see create action', function () {
    givePermissions('user', ['view']);

    livewire(ManageUsers::class)
        ->assertActionDisabled('create');
});

it('can update a user', function () {
    givePermissions('user', ['view', 'update']);

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

test('unauthorized users cannot see edit action', function () {
    givePermissions('user', ['view']);

    livewire(ManageUsers::class)
        ->assertTableActionDisabled('edit', User::factory()->make());
});

it('can delete a single user', function () {
    givePermissions('user', ['view', 'delete']);

    $user = User::factory()->create();

    livewire(ManageUsers::class)
        ->callTableAction('delete', $user->getRouteKey())
        ->assertNotified()
        ->assertCanNotSeeTableRecords([$user]);

    test()->assertModelMissing($user);
});

it('can bulk delete users', function () {
    givePermissions('user', ['view', 'delete']);

    $users = User::factory(3)->create();

    livewire(ManageUsers::class)
        ->callTableBulkAction('delete', $users)
        ->assertNotified()
        ->assertCanNotSeeTableRecords($users);

    test()->assertModelMissing($users[0]);
});

test('unauthorized users cannot see delete action', function () {
    givePermissions('user', ['view']);

    livewire(ManageUsers::class)
        ->assertTableActionDisabled('delete', User::factory()->make());
});
