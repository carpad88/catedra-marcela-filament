<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/account/{token}', \App\Filament\Pages\AccountActivation::class)
    ->name('account.activation');
