<?php

namespace App\Filament\App\Pages;

use App\Actions\Users\CreateUserWorks;
use App\Enums\Status;
use App\Http\Responses\LoginResponse;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Attributes\Locked;

class AccountActivation extends SimplePage
{
    use InteractsWithFormActions;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.app.pages.account-activation';

    protected static ?string $title = 'Activar cuenta';

    public ?string $email = null;

    public ?string $password = '';

    public ?string $passwordConfirmation = '';

    #[Locked]
    public ?string $token = null;

    public function mount(Request $request, $token, $email = null): void
    {
        $this->token = $token ?? request()->query('token');

        $this->form->fill([
            'email' => request()->query('email'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->hiddenLabel()
                    ->disabled()
                    ->autofocus(),
                TextInput::make('password')
                    ->label(__('filament-panels::pages/auth/password-reset/reset-password.form.password.label'))
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->rule(PasswordRule::default())
                    ->same('passwordConfirmation')
                    ->validationAttribute(__('filament-panels::pages/auth/password-reset/reset-password.form.password.validation_attribute')),
                TextInput::make('passwordConfirmation')
                    ->label(__('filament-panels::pages/auth/password-reset/reset-password.form.password_confirmation.label'))
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->dehydrated(false),
            ]);
    }

    public function activateAccount(CreateUserWorks $createUserWorks)
    {
        $data = $this->form->getState();

        $data['email'] = $this->email;
        $data['token'] = $this->token;

        $status = Password::broker(Filament::getAuthPasswordBroker())->reset(
            $data,
            function (CanResetPassword|Model|Authenticatable $user) use ($data) {
                $user->forceFill([
                    'password' => Hash::make($data['password']),
                    'remember_token' => Str::random(60),
                    'email_verified_at' => now(),
                ])->save();
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            Notification::make()
                ->title('Bienvenido a Cátedra Marcela. Tu cuenta ha sido activada.')
                ->success()
                ->send();

            Filament::auth()->attempt([
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $user = auth()->user();

            if ($user->hasRole('student')) {
                $group = $user->groups()->where('status', Status::Active)->first();
                $createUserWorks->handle($group, $user);
            }

            session()->regenerate();

            return app(LoginResponse::class);
        }

        Notification::make()
            ->title(__($status))
            ->danger()
            ->send();

        return null;
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('activateAccount')
                ->label('Activar cuenta')
                ->submit('activateAccount'),
        ];
    }
}
