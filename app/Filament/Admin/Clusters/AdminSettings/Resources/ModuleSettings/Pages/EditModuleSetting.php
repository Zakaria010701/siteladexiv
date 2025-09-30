<?php

namespace App\Filament\Admin\Clusters\AdminSettings\Resources\ModuleSettings\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Admin\Clusters\AdminSettings\Resources\ModuleSettings\ModuleSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditModuleSetting extends EditRecord
{
    protected static string $resource = ModuleSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
