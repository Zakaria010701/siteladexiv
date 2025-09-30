<?php

namespace App\Filament\Admin\Resources\SystemResources\Pages;

use Filament\Support\Enums\Width;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\SystemResources\Widgets\SystemResourceDependenciesWidget;
use App\Filament\Admin\Clusters\Settings\Resources\Services\ServiceResource;
use App\Filament\Admin\Resources\SystemResources\SystemResourceResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Actions;

class ListSystemResourceDependencies extends Page
{
    use InteractsWithRecord;

    protected static string $resource = SystemResourceResource::class;

    protected string $view = 'filament.admin.resources.system-resource-resource.pages.list-system-resource-dependencies';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->url(SystemResourceResource::getUrl('edit', ['record' => $this->record])),
            CreateAction::make()
                ->url(SystemResourceResource::getUrl('create')),
        ];
    }

    public function getWidgets(): array
    {
        return [
            SystemResourceDependenciesWidget::make(['systemResourceTypeId' => $this->record->system_resource_type_id]),
        ];
    }
}
