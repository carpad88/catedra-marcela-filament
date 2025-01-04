<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountActivationHasValidSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Filament::auth()->check()) {
            return redirect()->intended(Filament::getUrl());
        }

        if (! $request->hasValidSignature()) {
            Notification::make()
                ->title('El link de activación caducó, solicita uno nuevo.')
                ->danger()
                ->send();

            return redirect('/admin/login');
        }

        return $next($request);
    }
}
