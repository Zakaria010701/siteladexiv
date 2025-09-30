<?php

namespace App\Actions\Appointments;

use App\Models\Appointment;
use App\Models\AppointmentItem;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

class ValidateServiceCredits
{
    public function __construct(private Appointment $appointment) {}

    public static function make(Appointment $appointment): self
    {
        return new self($appointment);
    }

    public function execute()
    {
        $this->appointment->appointmentItems
            ->each(fn (AppointmentItem $item) => $this->validateAppointmentItem($item));
    }

    private function validateAppointmentItem(AppointmentItem $item)
    {
        if (! $item->isService()) {
            return;
        }

        $missing = $this->getMissingCreditsForItem($item);
        if ($missing <= 0) {
            return;
        }
        $open = $this->getOpenCreditsForItem($item);
        if ($missing > $open) {
            Notification::make()
                ->warning()
                ->title(__("Customer doesn't have sufficient service credit"))
                ->body(__('For :description used services :used can not be met by orderd :quantity and open :open credit!', [
                    'description' => $item->description,
                    'used' => intval($item->used),
                    'quantity' => intval($item->quantity),
                    'open' => $open,
                ]))
                ->send();

            $halt = new Halt;
            $halt->rollBackDatabaseTransaction(true);
            throw $halt;
        }
    }

    private function getOpenCreditsForItem(AppointmentItem $item): int
    {
        return $this->appointment->customer->serviceCredits()
            ->where('service_id', $item->purchasable_id)
            ->unused()
            ->count();
    }

    private function getMissingCreditsForItem(AppointmentItem $item): int
    {
        $used = $item->used - $item->usedServiceCredits()->count();
        $ordered = $item->quantity;

        return $used - $ordered;
    }
}
