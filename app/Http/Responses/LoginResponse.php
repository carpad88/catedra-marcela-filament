<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse extends \Filament\Http\Responses\Auth\LoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = auth()->user();
        $user->update(['last_login_at' => now()]);

        if ($user->hasRole('student')) {
            return redirect('/app');
        }

        if ($user->hasAnyRole(['teacher', 'super_admin'])) {
            return redirect('/admin');
        }

        return parent::toResponse($request);
    }
}
