<?php

namespace App\Observers;

use App\Enums\Transactions\DiscountType;
use App\Models\Appointment;
use App\Models\AppointmentItem;
use App\Models\Customer;
use App\Models\CustomerDiscount;
use App\Models\Discount;
use App\Models\Service;

class DiscountObserver
{
    /**
     * Handle the Discount "created" event.
     */
    public function created(Discount $discount): void
    {
        $this->createCustomerDiscount($discount);
    }

    private function createCustomerDiscount(Discount $discount): void
    {
        if (! $discount->permanent || $discount->type != DiscountType::Custom) {
            return;
        }

        if (! $discount->discountable instanceof Appointment) {
            return;
        }

        /** @var Appointment $appointment */
        $appointment = $discount->discountable;
        /** @var Customer|null $customer */
        $customer = $appointment->customer;

        if (is_null($customer)) {
            return;
        }

        /** @var CustomerDiscount $cDiscount */
        $cDiscount = $customer->customerDiscounts()->create([
            'source_id' => $discount->id,
            'description' => $discount->description,
            'percentage' => $discount->percentage,
            'amount' => empty($discount->percentage) ? $discount->amount : null,
        ]);

        $services = $appointment->appointmentItems()
            ->where('purchasable_type', Service::class)
            ->get()
            ->mapWithKeys(fn (AppointmentItem $item) => [
                $item->purchasable_id => [
                    'quantity' => $item->quantity,
                ],
            ])
            ->toArray();
        $cDiscount->services()->attach($services);
    }

    /**
     * Handle the Discount "updated" event.
     */
    public function updated(Discount $discount): void
    {
        $this->updateCustomerDiscount($discount);
    }

    private function updateCustomerDiscount(Discount $discount): void
    {
        if (is_null($discount->customerDiscount)) {
            $this->createCustomerDiscount($discount);

            return;
        }

        if (! $discount->permanent || $discount->type != DiscountType::Custom) {
            $this->deleteCustomerDiscount($discount);

            return;
        }

        $discount->customerDiscount()->update([
            'description' => $discount->description,
            'percentage' => $discount->percentage,
            'amount' => empty($discount->percentage) ? $discount->amount : null,
        ]);

        /** @var Appointment $appointment */
        $appointment = $discount->discountable;

        $services = $appointment->appointmentItems()
            ->where('purchasable_type', Service::class)
            ->get()
            ->mapWithKeys(fn (AppointmentItem $item) => [
                $item->purchasable_id => [
                    'quantity' => $item->quantity,
                ],
            ])
            ->toArray();
        $discount->customerDiscount->services()->sync($services);
    }

    /**
     * Handle the Discount "deleted" event.
     */
    public function deleted(Discount $discount): void
    {
        $this->deleteCustomerDiscount($discount);
    }

    private function deleteCustomerDiscount(Discount $discount): void
    {
        $discount->customerDiscount?->services()?->detach();
        $discount->customerDiscount()->delete();
    }

    /**
     * Handle the Discount "restored" event.
     */
    public function restored(Discount $discount): void
    {
        //
    }

    /**
     * Handle the Discount "force deleted" event.
     */
    public function forceDeleted(Discount $discount): void
    {
        //
    }
}
