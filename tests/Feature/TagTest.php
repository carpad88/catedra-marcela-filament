<?php

use App\Filament\Admin\Resources\TagResource;
use App\Filament\Admin\Resources\TagResource\Pages\ManageTags;
use App\Models\Tag;

use function Pest\Livewire\livewire;

it('renders the tags page', function () {
    actingAsWithPermissions('tag', ['view'], 'teacher');

    Tag::factory(2)->create([
        'type' => \App\Enums\RootTagsEnum::Projects->getLabel(),
    ]);

    test()->get(TagResource::getUrl())
        ->assertSuccessful();

    livewire(ManageTags::class)
        ->assertTableColumnExists('name')
        ->assertCountTableRecords(2);

    test()->assertDatabaseCount(Tag::class, 2);
});

it('prevents unauthorized users from accessing the tags page', function () {
    actingAsWithPermissions('tag', []);

    test()->get(TagResource::getUrl())
        ->assertForbidden()
        ->assertSee('403');
});

it('allows authorized users to create a new tag', function () {
    actingAsWithPermissions('tag', ['view', 'create']);

    $newTagData = Tag::factory()->make()->toArray();

    livewire(ManageTags::class)
        ->assertActionExists('create')
        ->assertActionEnabled('create')
        ->callAction('create', [
            'type' => $newTagData['type'],
            'name.es' => $newTagData['name']['es'],
        ])
        ->assertHasNoActionErrors();

    $createdTag = Tag::findFromStringOfAnyType($newTagData['name']['es'])->first();

    expect($createdTag)->not->toBeNull()
        ->and($createdTag->name)->toBe($newTagData['name']['es'])
        ->and($createdTag->type)->toBe($newTagData['type']);
});

it('prevents unauthorized users from seeing the create action', function () {
    actingAsWithPermissions('tag', ['view']);

    livewire(ManageTags::class)
        ->assertActionDisabled('create');
});

it('allows authorized users to update a tag', function () {
    actingAsWithPermissions('tag', ['view', 'update']);

    $tag = Tag::factory()->create([
        'type' => \App\Enums\RootTagsEnum::Projects->getLabel(),
    ]);
    $newTitle = 'New Title';

    livewire(ManageTags::class)
        ->mountTableAction('edit', $tag)
        ->assertTableActionDataSet(['name.es' => $tag->name])
        ->setTableActionData(['name.es' => $newTitle])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($tag->fresh()->name)->toBe($newTitle);
});

it('prevents unauthorized users from seeing the edit action', function () {
    actingAsWithPermissions('tag', ['view']);

    $tag = Tag::factory()->create();

    livewire(ManageTags::class)
        ->assertTableActionDisabled('edit', $tag);
});

it('allows authorized users to delete a tag', function () {
    actingAsWithPermissions('tag', ['view', 'delete']);

    $tag = Tag::factory()->create([
        'type' => \App\Enums\RootTagsEnum::Projects->getLabel(),
    ]);

    livewire(ManageTags::class)
        ->callTableAction('delete', $tag->getRouteKey())
        ->assertNotified()
        ->assertCanNotSeeTableRecords([$tag]);

    test()->assertModelMissing($tag);
});

it('prevents unauthorized users from seeing the delete action', function () {
    actingAsWithPermissions('tag', ['view']);

    $tag = Tag::factory()->create();

    livewire(ManageTags::class)
        ->assertTableActionDisabled('delete', $tag);
});
