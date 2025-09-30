<?php

namespace App\Filament\Crm\Resources\Appointments\Concerns;

use App\Enums\Appointments\CancelReason;
use App\Models\Appointment;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;

trait HasCancelAppointmentAction
{
    private function getCancelAppointmentAction()
    {
        return Action::make('cancelAppointment')
            ->label(__('Cancel Appointment'))
            ->color('danger')
            ->schema([
                Select::make('cancel_reason')
                    ->required()
                    ->options(CancelReason::class),
                Toggle::make('send_notification'),
            ])
            ->action(function (array $data, Appointment $record) {
                $old = clone $record;
                $record = $record->markCanceled($data['send_notification'], CancelReason::tryFrom($data['cancel_reason']));

                Notification::make()
                    ->success()
                    ->title(__(':model canceled', ['model' => __('Appointment')]))
                    ->send();
            })
            ->visible(fn (Appointment $record) => ! $record->status->isCanceled())
            ->requiresConfirmation();
    }
}
