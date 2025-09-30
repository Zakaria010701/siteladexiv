<?php

namespace App\Filament\Admin\Pages;

use Filament\Support\Enums\Width;
use App\Filament\Admin\Clusters\Settings\Resources\Services\Widgets\ServiceDependenciesWidget;
use App\Filament\Admin\Resources\SystemResources\Widgets\SystemResourceDependenciesWidget;
use App\Filament\Admin\Resources\Users\Widgets\UserDependenciesWidget;
use App\Filament\Admin\Widgets\ResourceTimelineCalendarWidget;
use App\Models\SystemResourceType;
use Filament\Pages\Page;

class ResourceOverview extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.admin.pages.resource-overview';

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public function getWidgets(): array
    {
        $widgets = [
            ResourceTimelineCalendarWidget::class,
            ServiceDependenciesWidget::class,
            UserDependenciesWidget::class,
        ];

        $resourceWidgets = SystemResourceType::all()->map(fn (SystemResourceType $type) =>
            SystemResourceDependenciesWidget::make(['systemResourceTypeId' => $type->id])
        )->toArray();

        return array_merge($widgets, $resourceWidgets);
    }
}
