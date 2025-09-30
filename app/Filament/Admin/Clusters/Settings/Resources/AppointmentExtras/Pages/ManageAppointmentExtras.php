<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\AppointmentExtras\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\Settings\Resources\AppointmentExtras\AppointmentExtraResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAppointmentExtras extends ManageRecords
{
    protected static string $resource = AppointmentExtraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
