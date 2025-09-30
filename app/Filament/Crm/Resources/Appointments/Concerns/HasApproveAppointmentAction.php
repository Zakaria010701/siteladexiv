<?php

namespace App\Filament\Crm\Resources\Appointments\Concerns;

use App\Models\Appointment;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

trait HasApproveAppointmentAction
{
    private function getApproveAppointmentAction()
    {
        return Action::make('approveAppointment')
            ->label(__('Approve'))
            ->action(function (Appointment $record) {
                $old = clone $record;
                $record = $record->markApproved();

                Notification::make()
                    ->success()
                    ->title(__(':model approved', ['model' => __('Appointment')]))
                    ->send();
            })
            ->visible(fn (Appointment $record) => $record->status->isPending())
            ->requiresConfirmation();
    }
}
