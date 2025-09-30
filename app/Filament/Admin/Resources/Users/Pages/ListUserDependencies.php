<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use Filament\Support\Enums\Width;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\Settings\Resources\Services\Widgets\ServiceDependenciesWidget;
use App\Filament\Admin\Clusters\Settings\Resources\Services\ServiceResource;
use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Resources\Pages\Page;
use Filament\Actions;

class ListUserDependencies extends Page
{
    protected static string $resource = UserResource::class;

    protected string $view = 'filament.admin.resources.user-resource.pages.list-user-dependencies';

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('index')
                ->color('gray')
                ->url(UserResource::getUrl('index')),
            CreateAction::make()
                ->url(UserResource::getUrl('create')),
        ];
    }

    public function getWidgets(): array
    {
        return [
            ServiceDependenciesWidget::class,
        ];
    }
}
