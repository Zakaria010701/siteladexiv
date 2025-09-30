<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\AppointmentComplaintTypes\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\Settings\Resources\AppointmentComplaintTypes\AppointmentComplaintTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAppointmentComplaintTypes extends ManageRecords
{
    protected static string $resource = AppointmentComplaintTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
