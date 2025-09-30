<?php

namespace App\Providers\Filament;

use Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use App\Filament\Admin\Clusters\AdminSettings\AdminSettingsCluster;
use App\Filament\Admin\Clusters\Notification\NotificationCluster;
use App\Filament\Admin\Clusters\Settings\SettingsCluster;
use App\Filament\Admin\Pages\ActivityLog;
use App\Filament\Admin\Pages\ResourceOverview;
use App\Filament\Admin\Resources\Availabilities\AvailabilityResource;
use App\Filament\Admin\Resources\SystemResources\SystemResourceResource;
use App\Filament\Admin\Resources\Todos\TodoResource;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Filament\Pages\Auth\Login;
use App\Filament\Plugins\FilamentFullCalendarPlugin;
use App\Filament\Admin\Resources\Roles\RoleResource;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('web')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Indigo,
                'danger' => Color::Red,
                'gray' => Color::Zinc,
                'info' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Amber,
            ])
            ->font('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;700&display=swap')
            ->brandLogo(asset('images/image.png'))
            ->discoverClusters(in: app_path('Filament/Admin/Clusters'), for: 'App\\Filament\\Admin\\Clusters')
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
            ->plugins([
                SpotlightPlugin::make(),
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                FilamentFullCalendarPlugin::make()
                    ->schedulerLicenseKey('CC-Attribution-NonCommercial-NoDerivatives')
                    /*->selectable(true)
                    ->editable(true)*/
                    ->plugins([
                        'resourceTimeline',
                        'scrollGrid',
                    ])
                    ->config([
                        'initialView' => 'resourceDayGridWeek',
                    ]),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups([
                    NavigationGroup::make()
                        ->label(__('Staff'))
                        ->items([
                            ...UserResource::getNavigationItems(),
                            ...RoleResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make()
                        ->label(__('Management'))
                        ->items([
                            ...SystemResourceResource::getNavigationItems(),
                            ...AvailabilityResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make()
                        ->label(__('Settings'))
                        ->items([
                            ...NotificationCluster::getNavigationItems(),
                            ...SettingsCluster::getNavigationItems(),
                            ...AdminSettingsCluster::getNavigationItems(),
                        ]),
                ])
                ->items([
                    ...TodoResource::getNavigationItems(),
                    ...ResourceOverview::getNavigationItems(),
                ]);
            })
            ->topNavigation()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->defaultThemeMode(ThemeMode::Light)
            ->maxContentWidth('7xl');

    }
}
