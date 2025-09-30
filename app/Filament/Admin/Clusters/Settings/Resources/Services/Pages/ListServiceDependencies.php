<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\Services\Pages;

use Filament\Support\Enums\Width;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\Settings\Resources\Services\Widgets\ServiceDependenciesWidget;
use App\Filament\Admin\Clusters\Settings\Resources\Services\ServiceResource;
use Filament\Resources\Pages\Page;
use Filament\Actions;

class ListServiceDependencies extends Page
{
    protected static string $resource = ServiceResource::class;

    protected string $view = 'filament.admin.clusters.settings.resources.service-resource.pages.list-service-dependencies';

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('index')
                ->color('gray')
                ->url(ServiceResource::getUrl('index')),
            CreateAction::make(),
        ];
    }

    public function getWidgets(): array
    {
        return [
            ServiceDependenciesWidget::class,
        ];
    }
}
