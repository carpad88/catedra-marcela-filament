<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

Route::redirect('/login', '/admin/login')->name('login');

Route::get('/account/{token}', \App\Filament\Admin\Pages\AccountActivation::class)
    ->name('account.activation')
    ->middleware(\App\Http\Middleware\AccountActivationHasValidSignature::class);
