<?php

use App\Filament\Admin\Resources\MaterialResource;
use App\Filament\Admin\Resources\MaterialResource\Pages\ManageMaterials;
use App\Models\Material;

use function Pest\Livewire\livewire;

it('renders the materials page', function () {
    actingAsWithPermissions('material', ['view'], 'teacher');

    Material::factory(2)->book()->create();
    Material::factory(1)->create();

    test()->get(MaterialResource::getUrl())
        ->assertSuccessful();

    livewire(ManageMaterials::class)
        ->set('activeTab', 'books')
        ->assertTableColumnExists('title')
        ->assertTableColumnExists('data.year')
        ->assertCountTableRecords(2);

    livewire(ManageMaterials::class)
        ->set('activeTab', 'digital')
        ->assertTableColumnExists('title')
        ->assertTableColumnExists('data.link')
        ->assertCountTableRecords(1);

    test()->assertDatabaseCount(Material::class, 3);
});

it('prevents unauthorized users from accessing the materials page', function () {
    actingAsWithPermissions('material', []);

    test()->get(MaterialResource::getUrl())
        ->assertForbidden()
        ->assertSee('403');
});

it('allows authorized users to create a new material', function () {
    actingAsWithPermissions('material', ['view', 'create']);

    $newResourceData = Material::factory()->book()->make()->toArray();

    livewire(ManageMaterials::class)
        ->assertActionExists('create')
        ->assertActionEnabled('create')
        ->callAction('create', [
            'category_id' => $newResourceData['category_id'],
            'title' => $newResourceData['title'],
            'author' => $newResourceData['author'],
            'data' => $newResourceData['data'],
        ])
        ->assertHasNoActionErrors();

    $createdResource = Material::where('title', $newResourceData['title'])->first();

    expect($createdResource)->not->toBeNull()
        ->and($createdResource->title)->toBe($newResourceData['title'])
        ->and($createdResource->author)->toBe($newResourceData['author']);
});

it('prevents unauthorized users from seeing the create action', function () {
    actingAsWithPermissions('material', ['view']);

    livewire(ManageMaterials::class)
        ->assertActionDisabled('create');
});

it('allows authorized users to update a material', function () {
    actingAsWithPermissions('material', ['view', 'update']);

    $material = Material::factory()->book()->create();
    $newTitle = 'New Title';

    livewire(ManageMaterials::class)
        ->mountTableAction('edit', $material)
        ->assertTableActionDataSet(['title' => $material->title])
        ->setTableActionData(['title' => $newTitle])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($material->fresh()->title)->toBe($newTitle);
});

it('prevents unauthorized users from seeing the edit action', function () {
    actingAsWithPermissions('material', ['view']);

    $material = Material::factory()->create();

    livewire(ManageMaterials::class)
        ->assertTableActionDisabled('edit', $material);
});

it('allows authorized users to delete a material', function () {
    actingAsWithPermissions('material', ['view', 'delete']);

    $material = Material::factory()->book()->create();

    livewire(ManageMaterials::class)
        ->callTableAction('delete', $material->getRouteKey())
        ->assertNotified()
        ->assertCanNotSeeTableRecords([$material]);

    test()->assertModelMissing($material);
});

it('prevents unauthorized users from seeing the delete action', function () {
    actingAsWithPermissions('material', ['view']);

    $material = Material::factory()->create();

    livewire(ManageMaterials::class)
        ->assertTableActionDisabled('delete', $material);
});
