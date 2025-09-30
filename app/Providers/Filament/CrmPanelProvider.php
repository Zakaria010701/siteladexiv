<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\ResourceOverview;
use App\Filament\Admin\Resources\Todos\TodoResource;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;
use App\Filament\Plugins\FilamentFullCalendarPlugin;
use Filament\Actions\Action;
use Filament\Enums\ThemeMode;
use Illuminate\Support\Uri;

class CrmPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('crm')
            ->path('crm')
            ->authGuard('web')
            ->spa()
            ->login(Login::class)
            ->databaseTransactions()
            ->colors([
                'primary' => Color::Indigo,
                'danger' => Color::Red,
                'gray' => Color::Zinc,
                'info' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Amber,
            ])
            ->discoverClusters(in: app_path('Filament/Crm/Clusters'), for: 'App\\Filament\\Crm\\Clusters')
            ->discoverResources(in: app_path('Filament/Crm/Resources'), for: 'App\\Filament\\Crm\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverPages(in: app_path('Filament/Crm/Pages'), for: 'App\\Filament\\Crm\\Pages')
            ->pages([
            ])
            ->discoverWidgets(in: app_path('Filament/Crm/Widgets'), for: 'App\\Filament\\Crm\\Widgets')
            ->widgets([
            ])
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
            ->profile(EditProfile::class, isSimple: false)
            ->plugins([
                SpotlightPlugin::make(),
                FilamentFullCalendarPlugin::make()
                    ->schedulerLicenseKey('CC-Attribution-NonCommercial-NoDerivatives')
                    ->selectable(true)
                    ->editable(true)
                    ->plugins([
                        'resourceDayGrid',
                        'resourceTimeGrid',
                        'scrollGrid',
                    ])
                    ->config([
                        'initialView' => 'resourceDayGridWeek',
                    ]),
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(__('Accounts')),
            ])
            ->topNavigation()
            ->databaseNotifications()
            ->maxContentWidth('7xl')
            ->viteTheme('resources/css/filament/crm/theme.css')
            ->defaultThemeMode(ThemeMode::Light)
            ->renderHook(
                PanelsRenderHook::USER_MENU_PROFILE_AFTER,
                fn (): string => Blade::render('@livewire(\'branch-switch\')')
            );
    }
}
