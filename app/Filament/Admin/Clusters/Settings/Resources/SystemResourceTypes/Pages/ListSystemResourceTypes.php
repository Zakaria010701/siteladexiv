<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypes\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypes\SystemResourceTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSystemResourceTypes extends ListRecords
{
    protected static string $resource = SystemResourceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
