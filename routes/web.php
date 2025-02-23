<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('student')) {
            return redirect(Filament::getPanel('app')->getUrl());
        }

        return redirect(Filament::getPanel('admin')->getUrl());
    }

    return view('welcome');
})->name('welcome');

Route::redirect('/admin/login', '/app/login')->name('login');

Route::get('/account/{token}', \App\Filament\App\Pages\AccountActivation::class)
    ->name('account.activation')
    ->middleware(\App\Http\Middleware\AccountActivationHasValidSignature::class);
