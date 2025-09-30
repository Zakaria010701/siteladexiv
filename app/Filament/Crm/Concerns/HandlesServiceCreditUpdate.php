<?php

namespace App\Filament\Crm\Concerns;

use App\Models\Appointment;
use App\Models\AppointmentItem;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceCredit;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

trait HandlesServiceCreditUpdate
{
    /**
     * @throws Halt
     */
    private function validateSufficientServiceCredits(Appointment $appointment): void
    {
        $appointment->appointmentItems
            ->each(function (AppointmentItem $item) {
                if (
                    $item->purchasable_type !== Service::class
                    || is_null($item->purchasable_id)
                ) {
                    return;
                }

                $used = $item->used - $item->usedServiceCredits()->count();
                $ordered = $item->quantity - $item->orderedServiceCredits()->count();
                $missing = $used - $ordered;
                if ($missing <= 0) {
                    return;
                }
                $open = $this->getRecord()->customer->serviceCredits()
                    ->where('service_id', $item->purchasable_id)
                    ->unused()
                    ->count();
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
            });
    }

    private function handleServiceCreditUpdate(Appointment $record): void
    {
        $record->appointmentItems
            ->where('purchasable_type', Service::class)
            ->whereNotNull('purchasable_id')
            ->each(function (AppointmentItem $item) use ($record) {
                // Get the current counts from the database.
                $ordered = $item->orderedServiceCredits()->count();
                $used = $item->usedServiceCredits()->count();
                // Create new Service Credits. Do this first as the other operations depend on it.
                $this->createMissingOrderedServiceCredits($record, $item, $record->customer, $ordered);
                // Associate existing Service Credits with the item.
                $this->addMissingUsedServiceCredits($record, $item, $record->customer, $used);
                // Dissasociate Service Credits from the item.
                $this->removeSurplusUsedServiceCredits($item, $used);
                // Delete Service Credits. Do this last as it depends on the other operations.
                $this->deleteSurplusOrderedServiceCredits($item, $ordered);
                // Finaly adjust the saved values to be the same as
                // the actual count to prevent it from going out of sync.
                $this->adjustOrderedServiceCredit($item);
                $this->adjustUsedServiceCredit($item);
            });
    }

    private function deleteSurplusOrderedServiceCredits(AppointmentItem $item, int $current): void
    {
        if ($current > $item->quantity) {
            $delete = $current - $item->quantity;
            $item->orderedServiceCredits()->unused()->limit(intval($delete))->delete();
        }
    }

    private function createMissingOrderedServiceCredits(Appointment $record, AppointmentItem $item, Customer $customer, int $current): void
    {
        // Only create new service credits if they have been paid
        if (! $record->appointmentOrder->status->isPaid()) {
            return;
        }

        if ($current < $item->quantity) {
            $add = $item->quantity - $current;
            $insert = [];
            for ($i = 0; $i < $add; $i++) {
                $insert[] = [
                    'service_id' => $item->purchasable_id,
                    'customer_id' => $customer->id,
                    'price' => $item->unit_price,
                ];
            }
            $item->orderedServiceCredits()->createMany($insert);
        }
    }

    private function adjustOrderedServiceCredit(AppointmentItem $item): void
    {
        $current = $item->orderedServiceCredits()->count();
        if ($current != $item->quantity) {
            $item->quantity = $current;
            $item->save();
        }
    }

    private function removeSurplusUsedServiceCredits(AppointmentItem $item, int $current): void
    {
        if ($current > $item->used) {
            $dissociate = $current - $item->used;
            $item->usedServiceCredits()
                ->orderBy('used_at')
                ->limit(intval($dissociate))
                ->update([
                    'used_at' => null,
                    'usage_type' => null,
                    'usage_id' => null,
                ]);
        }
    }

    private function addMissingUsedServiceCredits(Appointment $record, AppointmentItem $item, Customer $customer, int $current): void
    {
        if (! $record->status->isDone()) {
            return;
        }

        if ($current < $item->used) {
            $associate = $item->used - $current;
            $credits = $customer->serviceCredits()
                ->where('service_id', $item->purchasable_id)
                ->unused()
                ->oldest()
                ->limit(intval($associate))
                ->get()
                ->map(function (ServiceCredit $credit) {
                    $credit->used_at = now();

                    return $credit;
                });
            $item->usedServiceCredits()->saveMany($credits);
        }
    }

    private function adjustUsedServiceCredit(AppointmentItem $item): void
    {
        $current = $item->usedServiceCredits()->count();
        if ($current != $item->used) {
            $item->used = $current;
            $item->save();
        }
    }
}
