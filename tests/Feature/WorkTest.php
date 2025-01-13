<?php

use App\Filament\Resources\WorkResource;
use App\Models\Work;
use Filament\Forms\Components\Repeater;

use function Pest\Livewire\livewire;

it('renders the works page and displays the correct columns and records', function () {
    actingAsWithPermissions('work', ['view']);

    Work::factory(2)->create();

    test()->get(WorkResource::getUrl())
        ->assertSuccessful()
        ->assertSee('Trabajos');

    livewire(WorkResource\Pages\ListWorks::class)
        ->assertTableColumnExists('group.title')
        ->assertTableColumnExists('cover')
        ->assertTableColumnExists('project.title')
        ->assertTableColumnExists('user.name')
        ->assertTableColumnExists('project.finished_at')
        ->assertCountTableRecords(2);

    test()->assertDatabaseCount(Work::class, 2);
});

it('prevents unauthorized users from accessing the works page', function () {
    actingAsWithPermissions('works', []);

    test()->get(WorkResource::getUrl())
        ->assertForbidden()
        ->assertSee('403');
});

it('prevents guests from accessing the admin works page', function () {
    test()->get(WorkResource::getUrl())
        ->assertRedirect('admin/login');
});

it('allows authorized users to create a new work', function () {
    actingAsWithPermissions('work', ['view', 'create']);

    $work = Work::factory()->make();

    livewire(WorkResource\Pages\ListWorks::class)
        ->assertActionExists('create')
        ->assertActionEnabled('create')
        ->callAction('create', [
            'group_id' => $work->group_id,
            'project_id' => $work->project_id,
            'user_id' => $work->user_id,
        ])
        ->assertHasNoActionErrors();

    test()->assertModelExists($work->fresh());

    $createdWork = Work::latest()->first();

    expect($createdWork)->not->toBeNull()
        ->and($createdWork->group_id)->toBe($work->group_id)
        ->and($createdWork->project_id)->toBe($work->project_id)
        ->and($createdWork->user_id)->toBe($work->user_id)
        ->and($createdWork->folder)->not->toBeNull();
});

it('rejects invalid data during work creation', function () {
    actingAsWithPermissions('work', ['view', 'create']);

    livewire(WorkResource\Pages\ListWorks::class)
        ->assertActionExists('create')
        ->assertActionEnabled('create')
        ->callAction('create', [
            'group_id' => null, // Invalid data
            'project_id' => null,
            'user_id' => null,
        ])
        ->assertHasActionErrors(['group_id', 'project_id', 'user_id']);
});

it('prevents unauthorized users from seeing the create action', function () {
    actingAsWithPermissions('work', ['view']);

    livewire(WorkResource\Pages\ListWorks::class)
        ->assertActionDisabled('create');
});

it('allows teachers to update any work in their groups', function () {
    $teacher = actingAsWithPermissions('work', ['view', 'update'], 'teacher');

    $group = \App\Models\Group::factory()->create(['owner_id' => $teacher->id]);
    $work = Work::factory()->create(['group_id' => $group->id]);
    $newWork = Work::factory()->make();

    test()->get(WorkResource::getUrl('edit', ['record' => $work]))->assertSuccessful();

    livewire(WorkResource\Pages\EditWork::class, [
        'record' => $work->getRouteKey(),
    ])
        ->assertFormSet([
            'group_id' => $work->group_id,
            'project_id' => $work->project_id,
            'user_id' => $work->user_id,
        ])
        ->fillForm([
            'cover' => [$newWork->cover],
            'images' => $newWork->images,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $work->refresh();
    expect($work->cover)->toBe($newWork->cover)
        ->and($work->images)->toBe($newWork->images);
});

it('allows teachers to grade any work in their groups', function () {
    $teacher = actingAsWithPermissions('work', ['view', 'update'], 'teacher');

    $group = \App\Models\Group::factory()->create(['owner_id' => $teacher->id]);
    $work = Work::factory()->create(['group_id' => $group->id]);

    test()->get(WorkResource::getUrl('rubric', ['record' => $work]))->assertSuccessful();

    $undoRepeaterFake = Repeater::fake();

    livewire(WorkResource\Pages\GradeWork::class, [
        'record' => $work->getRouteKey(),
    ])
        ->assertFormSet([
            'group_id' => $work->group_id,
            'project_id' => $work->project_id,
            'user_id' => $work->user_id,
        ])
        ->fillForm([
            'rubrics' => $work->project->criterias->map(fn ($rubric) => [
                'id' => $rubric->id,
                'title' => $rubric->title,
                'order' => $rubric->order,
                'level_id' => $rubric->levels->first()->id,
            ])->toArray(),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $undoRepeaterFake();

    $work->refresh();
    expect($work->score)->not->toBeNull()
        ->and($work->score)->toBe($work->scores->sum('level.score'));
});

it('allows users to update their own works', function () {
    $user = actingAsWithPermissions('work', ['view', 'update']);

    $work = Work::factory()->create(['user_id' => $user->id]);
    $newWork = Work::factory()->make();

    test()->get(WorkResource::getUrl('edit', ['record' => $work]))->assertSuccessful();

    $undoRepeaterFake = Repeater::fake();

    livewire(WorkResource\Pages\EditWork::class, [
        'record' => $work->getRouteKey(),
    ])
        ->assertFormSet([
            'group_id' => $work->group_id,
            'project_id' => $work->project_id,
            'user_id' => $work->user_id,
        ])
        ->fillForm([
            'cover' => [$newWork->cover],
            'images' => $newWork->images,
            'rubrics' => $work->project->criterias->map(fn ($rubric) => [
                'id' => $rubric->id,
                'title' => $rubric->title,
                'order' => $rubric->order,
                'level_id' => $rubric->levels->first()->id,
            ])->toArray(),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $undoRepeaterFake();

    $work->refresh();
    expect($work->cover)->toBe($newWork->cover)
        ->and($work->images)->toBe($newWork->images);
});

it('prevents unauthorized users from accessing the edit project page', function () {
    actingAsWithPermissions('work', []);

    $work = Work::factory()->create();

    test()->get(WorkResource::getUrl('edit', ['record' => $work]))
        ->assertForbidden()
        ->assertSee('403');
});

it('allows teachers to delete any work in their groups', function () {
    $teacher = actingAsWithPermissions('work', ['view', 'delete'], 'teacher');

    $group = \App\Models\Group::factory()->create(['owner_id' => $teacher->id]);
    $work = Work::factory()->create(['group_id' => $group->id]);

    livewire(WorkResource\Pages\ListWorks::class)
        ->assertTableActionExists('delete')
        ->callTableAction('delete', $work);

    test()->assertModelMissing($work);
});

it('prevents unauthorized users from deleting a project', function () {
    actingAsWithPermissions('work', ['view']);

    $work = Work::factory()->create();

    livewire(WorkResource\Pages\ListWorks::class)
        ->assertTableActionDisabled('delete', $work);
});
