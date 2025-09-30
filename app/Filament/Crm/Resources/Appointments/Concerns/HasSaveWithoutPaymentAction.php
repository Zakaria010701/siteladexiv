<?php

namespace App\Filament\Crm\Resources\Appointments\Concerns;

use App\Support\Appointment\AppointmentCalculator;
use Filament\Actions\Action;

trait HasSaveWithoutPaymentAction
{
    private function getSaveWithoutPaymentAction(): Action
    {
        return Action::make('saveWithoutPayment')
            ->requiresConfirmation()
            ->color('warning')
            ->action(function () {
                $this->data['payments'] = $this->getRecord()->payments()->get()->toArray();

                $this->data = AppointmentCalculator::make($this->record, $this->data)->updatedCustomer()->updatedPrices()->saveData();

                $this->save(options: ['saveWithoutPaymentValidation' => true]);
            });
    }
}
