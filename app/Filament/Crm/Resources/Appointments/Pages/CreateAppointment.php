<?php

namespace App\Filament\Crm\Resources\Appointments\Pages;

use App\Enums\Appointments\AppointmentStatus;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAppointment extends CreateRecord
{

    protected static string $resource = AppointmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = AppointmentStatus::Approved;

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = parent::handleRecordCreation($data);

        return $record;
    }

    /*protected function afterCreate(): void
    {
        if (! $this->record instanceof Appointment) {
            return;
        }

        AfterCreateAppointment::make($this->record)->execute();
    }*/
}
