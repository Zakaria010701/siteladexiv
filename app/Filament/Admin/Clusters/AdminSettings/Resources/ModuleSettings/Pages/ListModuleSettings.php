<?php

namespace App\Filament\Admin\Clusters\AdminSettings\Resources\ModuleSettings\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\AdminSettings\Resources\ModuleSettings\ModuleSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListModuleSettings extends ListRecords
{
    protected static string $resource = ModuleSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
