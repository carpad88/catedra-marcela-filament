<?php

use App\Filament\Admin\Resources\ResourceResource;
use App\Filament\Admin\Resources\ResourceResource\Pages\ManageResources;
use App\Models\Resource;

use function Pest\Livewire\livewire;

it('renders the resources page', function () {
    actingAsWithPermissions('resource', ['view'], 'teacher');

    Resource::factory(2)->book()->create();
    Resource::factory(1)->create();

    test()->get(ResourceResource::getUrl())
        ->assertSuccessful();

    livewire(ManageResources::class)
        ->set('activeTab', 'books')
        ->assertTableColumnExists('title')
        ->assertTableColumnExists('data.year')
        ->assertCountTableRecords(2);

    livewire(ManageResources::class)
        ->set('activeTab', 'digital')
        ->assertTableColumnExists('title')
        ->assertTableColumnExists('data.link')
        ->assertCountTableRecords(1);

    test()->assertDatabaseCount(Resource::class, 3);
});

it('prevents unauthorized users from accessing the resources page', function () {
    actingAsWithPermissions('resource', []);

    test()->get(ResourceResource::getUrl())
        ->assertForbidden()
        ->assertSee('403');
});

it('allows authorized users to create a new resource', function () {
    actingAsWithPermissions('resource', ['view', 'create']);

    $newResourceData = Resource::factory()->book()->make()->toArray();

    livewire(ManageResources::class)
        ->assertActionExists('create')
        ->assertActionEnabled('create')
        ->callAction('create', [
            'category_id' => $newResourceData['category_id'],
            'title' => $newResourceData['title'],
            'author' => $newResourceData['author'],
            'data' => $newResourceData['data'],
        ])
        ->assertHasNoActionErrors();

    $createdResource = Resource::where('title', $newResourceData['title'])->first();

    expect($createdResource)->not->toBeNull()
        ->and($createdResource->title)->toBe($newResourceData['title'])
        ->and($createdResource->author)->toBe($newResourceData['author']);
});

it('prevents unauthorized users from seeing the create action', function () {
    actingAsWithPermissions('resource', ['view']);

    livewire(ManageResources::class)
        ->assertActionDisabled('create');
});

it('allows authorized users to update a resource', function () {
    actingAsWithPermissions('resource', ['view', 'update']);

    $resource = Resource::factory()->book()->create();
    $newTitle = 'New Title';

    livewire(ManageResources::class)
        ->mountTableAction('edit', $resource)
        ->assertTableActionDataSet(['title' => $resource->title])
        ->setTableActionData(['title' => $newTitle])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($resource->fresh()->title)->toBe($newTitle);
});

it('prevents unauthorized users from seeing the edit action', function () {
    actingAsWithPermissions('resource', ['view']);

    $resource = Resource::factory()->create();

    livewire(ManageResources::class)
        ->assertTableActionDisabled('edit', $resource);
});

it('allows authorized users to delete a resource', function () {
    actingAsWithPermissions('resource', ['view', 'delete']);

    $resource = Resource::factory()->book()->create();

    livewire(ManageResources::class)
        ->callTableAction('delete', $resource->getRouteKey())
        ->assertNotified()
        ->assertCanNotSeeTableRecords([$resource]);

    test()->assertModelMissing($resource);
});

it('prevents unauthorized users from seeing the delete action', function () {
    actingAsWithPermissions('resource', ['view']);

    $resource = Resource::factory()->create();

    livewire(ManageResources::class)
        ->assertTableActionDisabled('delete', $resource);
});
