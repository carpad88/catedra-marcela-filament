<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

Route::redirect('/login', '/admin/login')->name('login');

Route::get('/account/{token}', \App\Filament\Pages\AccountActivation::class)
    ->name('account.activation');
