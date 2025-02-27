<?php

use App\Filament\Admin\Resources\PostResource;
use App\Models\Post;

use function Pest\Livewire\livewire;

it('renders the posts page and displays posts', function () {
    actingAsWithPermissions('post', ['view'], 'teacher');

    $posts = Post::factory(2)->create();

    test()->get(PostResource::getUrl())
        ->assertSuccessful();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableColumnExists('cover')
        ->assertTableColumnExists('title')
        ->assertCountTableRecords(2)
        ->assertCanSeeTableRecords($posts);

    test()->assertDatabaseCount(Post::class, 2);
});

it('prevents unauthorized users from accessing the posts page', function () {
    actingAsWithPermissions('post', []);

    test()->get(PostResource::getUrl())
        ->assertForbidden()
        ->assertSee('403');
});

it('prevents guests from accessing the admin posts page', function () {
    test()->get(PostResource::getUrl())
        ->assertRedirect('admin/login');
});

it('allows authorized users to create a new post', function () {
    actingAsWithPermissions('post', ['view', 'create'], 'teacher');

    $newPostData = Post::factory()->make()->toArray();

    test()->get(PostResource::getUrl('create'))
        ->assertSuccessful();

    livewire(PostResource\Pages\CreatePost::class)
        ->fillForm([
            'cover' => [$newPostData['cover']],
            'title' => $newPostData['title'],
            'excerpt' => $newPostData['excerpt'],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $createdPost = Post::where('title', $newPostData['title'])->first();

    expect($createdPost)->not->toBeNull()
        ->and($createdPost->title)->toBe($newPostData['title'])
        ->and($createdPost->excerpt)->toBe($newPostData['excerpt']);
});

it('prevents unauthorized users from accessing the create post page', function () {
    actingAsWithPermissions('post', []);

    test()->get(PostResource::getUrl('create'))
        ->assertForbidden()
        ->assertSee('403');
});

it('allows authorized users to update a post', function () {
    actingAsWithPermissions('post', ['view', 'update'], 'teacher');

    $post = Post::factory()->create();
    $newTitle = 'New Title';

    test()->get(PostResource::getUrl('edit', ['record' => $post]))
        ->assertSuccessful();

    livewire(PostResource\Pages\EditPost::class, [
        'record' => $post->getRouteKey(),
    ])
        ->assertFormSet([
            'title' => $post->title,
        ])
        ->fillForm([
            'cover' => [$post->cover],
            'title' => $newTitle,
            'excerpt' => $post->excerpt,
            'content' => $post->content,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($post->refresh())->title->toBe($newTitle);
});

it('prevents unauthorized users from accessing the edit post page', function () {
    actingAsWithPermissions('post', []);

    $post = Post::factory()->create();

    test()->get(PostResource::getUrl('edit', ['record' => $post]))
        ->assertForbidden()
        ->assertSee('403');
});

it('allows authorized users to delete a post', function () {
    actingAsWithPermissions('post', ['view', 'delete']);

    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableActionExists('delete')
        ->callTableAction('delete', $post->getRouteKey());

    test()->assertModelMissing($post);
});

it('prevents unauthorized users from deleting a post', function () {
    actingAsWithPermissions('post', ['view']);

    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertTableActionDisabled('delete', $post);
});
