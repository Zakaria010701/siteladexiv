<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\EnergySettings\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\Settings\Resources\EnergySettings\EnergySettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEnergySettings extends ManageRecords
{
    protected static string $resource = EnergySettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
