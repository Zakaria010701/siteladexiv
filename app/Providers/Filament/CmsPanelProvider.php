<?php

namespace App\Providers\Filament;

use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CmsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('cms')
            ->path('cms')
            ->authGuard('web')
            ->colors([
                'primary' => Color::Teal,
            ])
            ->discoverResources(in: app_path('Filament/Cms/Resources'), for: 'App\Filament\Cms\Resources')
            ->discoverPages(in: app_path('Filament/Cms/Pages'), for: 'App\Filament\Cms\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Cms/Widgets'), for: 'App\Filament\Cms\Widgets')
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups([
                    NavigationGroup::make()
                        ->label(__('Content'))
                        ->items([
                            ...\App\Filament\Cms\Resources\CmsPages\CmsPageResource::getNavigationItems(),
                            ...\App\Filament\Cms\Resources\CmsMenuItems\CmsMenuItemResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make()
                        ->label(__('Media'))
                        ->items([
                            ...\App\Filament\Cms\Resources\MediaResource::getNavigationItems(),
                            ...\App\Filament\Cms\Resources\AllMediaResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make()
                        ->label(__('Settings'))
                        ->items([
                            ...\App\Filament\Cms\Resources\HeaderContactResource::getNavigationItems(),
                        ]),
                ]);
            })
            ->viteTheme('resources/css/filament/cms/theme.css')
            ->defaultThemeMode(ThemeMode::Light);
    }
}
