<?php

namespace App\Providers;

use Filament\Forms;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LoginResponse::class, \App\Http\Responses\LoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme('https');

        Model::unguard();

        Forms\Components\Select::configureUsing(function (Forms\Components\Select $entry): void {
            $entry->native(false);
        });

        FilamentIcon::register([
            'panels::pages.dashboard.navigation-item' => 'phosphor-house-duotone',
            'panels::topbar.open-database-notifications-button' => 'phosphor-bell-duotone',
            'panels::sidebar.group.collapse-button' => 'phosphor-caret-up-duotone',
            'panels::user-menu.profile-item' => 'phosphor-user-circle-duotone',
            'panels::user-menu.logout-button' => 'phosphor-sign-out-duotone',
            'actions::action-group' => 'phosphor-dots-three-outline-vertical-duotone',
            'actions::view-action' => 'phosphor-eye-duotone',
            'actions::edit-action' => 'phosphor-pencil-duotone',
            'actions::delete-action' => 'phosphor-trash-duotone',
            'actions::detach-action' => 'phosphor-user-minus-duotone',
        ]);

        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_FOOTER,
            fn (): string => Blade::render("<div class='text-xs text-gray-400 p-2'>{{ app()->version() }} :: {{ \Composer\InstalledVersions::getPrettyVersion('filament/filament') }}</div>"),
        );
    }
}
