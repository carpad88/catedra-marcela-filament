<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\Auth\RequestPasswordReset;
use Blade;
use DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
            ->login()
            ->profile(isSimple: false)
            ->passwordReset(RequestPasswordReset::class)
            ->viteTheme('resources/css/filament/app/theme.css')
            ->colors([
                'primary' => Color::Neutral,
            ])
            ->font('Fira Sans',
                'https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap')
            ->brandLogo(asset('img/logo.svg'))
            ->brandLogoHeight('3rem')
            ->breadcrumbs(false)
            ->darkMode(false)
            ->topNavigation()
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn (
                ): string => Blade::render('<div class="px-6 py-8 text-xs text-gray-400 text-center">{{ now()->format("Y") }} © Marcela Ramírez | Todos los derechos reservados</div>'),
            )
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling(false)
            ->plugins([
                AuthUIEnhancerPlugin::make()
                    ->mobileFormPanelPosition('bottom')
                    ->formPanelPosition()
                    ->formPanelWidth('40%')
                    ->emptyPanelBackgroundImageUrl(asset('img/bg-login.webp')),
            ]);
    }
}
