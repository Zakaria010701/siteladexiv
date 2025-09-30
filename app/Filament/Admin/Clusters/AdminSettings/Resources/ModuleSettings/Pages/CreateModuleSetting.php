<?php

namespace App\Filament\Admin\Clusters\AdminSettings\Resources\ModuleSettings\Pages;

use App\Filament\Admin\Clusters\AdminSettings\Resources\ModuleSettings\ModuleSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateModuleSetting extends CreateRecord
{
    protected static string $resource = ModuleSettingResource::class;
}
