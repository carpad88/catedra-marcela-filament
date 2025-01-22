<?php

use App\Models\User;
use Illuminate\Support\Facades\Password;

use function Pest\Livewire\livewire;

it('can access activation page with valid signature', function () {
    $user = User::factory()->create(['email_verified_at' => null]);

    test()->get((new \App\Notifications\WelcomeEmail($user))->getActivationUrl())
        ->assertSuccessful()
        ->assertSee('Activar cuenta')
        ->assertSee($user->email);
});

it('redirects to login for an invalid signature', function () {
    test()->get('/account/invalid-token')
        ->assertRedirect('/admin/login')
        ->assertSessionHas('filament.notifications');

    $notifications = session('filament.notifications');

    expect($notifications)->toHaveCount(1)
        ->and($notifications[0]['title'])
        ->toBe('El link de activación caducó, solicita uno nuevo.');
});

it('activates account and resets password with valid data', function () {
    $user = User::factory()
        ->has(\App\Models\Group::factory()->hasProjects(2))
        ->create(['email_verified_at' => null]);

    livewire(\App\Filament\Admin\Pages\AccountActivation::class, ['token' => Password::broker()->createToken($user)])
        ->fillForm([
            'email' => $user->email,
            'password' => 'newpassword',
            'passwordConfirmation' => 'newpassword',
        ])
        ->call('activateAccount')
        ->assertHasNoFormErrors();

    $notifications = session('filament.notifications');

    expect($notifications)->toHaveCount(1)
        ->and($notifications[0]['title'])->toBe('Bienvenido a Cátedra Marcela. Tu cuenta ha sido activada.')
        ->and(auth()->check())->toBeTrue()
        ->and($user->fresh()->email_verified_at)->not()->toBeNull()
        ->and($user->works->count())->toBe(2);
});
