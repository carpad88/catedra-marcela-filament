<?php

namespace App\Providers;

use Filament\Forms;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Database\Eloquent\Model;
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
        Model::unguard();

        Forms\Components\Select::configureUsing(function (Forms\Components\Select $entry): void {
            $entry->native(false);
        });
    }
}
