<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\AppointmentModules\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\Settings\Resources\AppointmentModules\AppointmentModuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAppointmentModules extends ManageRecords
{
    protected static string $resource = AppointmentModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
