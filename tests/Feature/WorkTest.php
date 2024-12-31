<?php

use App\Filament\Resources\WorkResource;
use App\Models\User;
use App\Models\Work;

use function Pest\Livewire\livewire;

beforeEach(function () {
    test()->actingAs(User::factory()->create());
});

test('can render works page', function () {
    givePermissions('work', ['view']);

    Work::factory(2)->create();

    test()->get(WorkResource::getUrl())->assertSuccessful();

    livewire(WorkResource\Pages\ListWorks::class)
        ->assertTableColumnExists('group.period')
        ->assertTableColumnExists('cover')
        ->assertTableColumnExists('project.title')
        ->assertTableColumnExists('user.name')
        ->assertTableColumnExists('project.finished_at')
        ->assertCountTableRecords(2);

    test()->assertDatabaseCount(Work::class, 2);
});

test('unauthorized users cannot render works page', function () {
    test()->get(WorkResource::getUrl())
        ->assertForbidden()
        ->assertSee('403');
});

it('can create a new work', function () {
    givePermissions('work', ['view', 'create']);

    $item = Work::factory()->make();

    test()->get(WorkResource::getUrl('create'))->assertSuccessful();

    livewire(WorkResource\Pages\CreateWork::class)
        ->fillForm([
            'group_id' => $item->group_id,
            'project_id' => $item->project_id,
            'user_id' => $item->user_id,
            'cover' => [$item->cover],
            'images' => $item->images,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    test()->assertModelExists($item->fresh());
});

test('unauthorized users cannot render create work page', function () {
    test()->get(WorkResource::getUrl('create'))
        ->assertForbidden()
        ->assertSee('403');
});

//it('can view project page', function () {
//    givePermissions('work', ['view']);
//
//    test()->get(WorkResource::getUrl('view', [
//        'record' => Work::factory()->create(),
//    ]))->assertSuccessful();
//
//    $item = Work::factory()->create();
//
//    livewire(WorkResource\Pages\ViewWork::class, [
//        'record' => $item->getRouteKey(),
//    ])
//        ->assertFormSet([
//            'group_id' => $item->group_id,
//            'project_id' => $item->project_id,
//            'user_id' => $item->user_id,
//        ]);
//});
//
//test('unauthorized users cannot render view work page', function () {
//    test()->get(WorkResource::getUrl('view', ['record' => Work::factory()->create()]))
//        ->assertForbidden()
//        ->assertSee('403');
//});

test('teachers can update any work from its groups', function () {
    givePermissions('work', ['view', 'update']);
    assignRole('teacher');

    $group = \App\Models\Group::factory()->create(['owner_id' => auth()->id()]);
    $item = Work::factory()->create(['group_id' => $group->id]);
    $newItem = Work::factory()->make();

    test()->get(WorkResource::getUrl('edit', ['record' => $item]))->assertSuccessful();

    livewire(WorkResource\Pages\EditWork::class, [
        'record' => $item->getRouteKey(),
    ])
        ->assertFormSet([
            'group_id' => $item->group_id,
            'project_id' => $item->project_id,
            'user_id' => $item->user_id,
        ])->fillForm([
            'cover' => [$newItem->cover],
            'images' => $newItem->images,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($item->refresh())->cover->toBe($newItem->cover);
});

test('users can update its works', function () {
    givePermissions('work', ['view', 'update']);

    $item = Work::factory()->create(['user_id' => auth()->id()]);
    $newItem = Work::factory()->make();

    test()->get(WorkResource::getUrl('edit', ['record' => $item]))->assertSuccessful();

    livewire(WorkResource\Pages\EditWork::class, [
        'record' => $item->getRouteKey(),
    ])
        ->assertFormSet([
            'group_id' => $item->group_id,
            'project_id' => $item->project_id,
            'user_id' => $item->user_id,
        ])->fillForm([
            'cover' => [$newItem->cover],
            'images' => $newItem->images,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($item->refresh())->cover->toBe($newItem->cover);
});

test('unauthorized users cannot render edit project page', function () {
    test()->get(WorkResource::getUrl('edit', ['record' => Work::factory()->create()]))
        ->assertForbidden()
        ->assertSee('403');
});

test('teachers can delete any work from its groups', function () {
    givePermissions('work', ['view', 'delete']);
    assignRole('teacher');

    $group = \App\Models\Group::factory()->create(['owner_id' => auth()->id()]);
    $item = Work::factory()->create(['group_id' => $group->id]);

    livewire(WorkResource\Pages\ListWorks::class)
        ->assertTableActionExists('delete')
        ->callTableAction('delete', $item);

    test()->assertModelMissing($item);
});

test('unauthorized users cannot delete a project', function () {
    givePermissions('work', ['view']);

    livewire(WorkResource\Pages\ListWorks::class)
        ->assertTableActionDisabled('delete', Work::factory()->create());
});
