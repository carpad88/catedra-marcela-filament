<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

Route::redirect('/admin/login', '/app/login')->name('login');

Route::get('/account/{token}', \App\Filament\App\Pages\AccountActivation::class)
    ->name('account.activation')
    ->middleware(\App\Http\Middleware\AccountActivationHasValidSignature::class);
