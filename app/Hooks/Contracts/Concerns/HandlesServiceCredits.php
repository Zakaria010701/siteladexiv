<?php

namespace App\Hooks\Contracts\Concerns;

use App\Models\Contract;
use App\Models\Appointment;
use App\Models\ContractService;

/** @property Contract $contract */
trait HandlesServiceCredits
{
    private function deleteUnusedCreditsForMissingServices(): void
    {
        $this->contract->credits()
            ->whereNotIn('service_id', $this->contract->contractServices->pluck('service_id')->toArray())
            ->unused()
            ->delete();
    }

    private function createMissingCredits(): void
    {
        $this->contract->contractServices->each(fn (ContractService $service) => $this->createMissingCreditsForService($service));
    }

    private function createMissingCreditsForService(ContractService $service, ?int $count = null): void
    {
        if ($count === null) {
            $count = $this->contract->credits()->where('service_id', $service->service_id)->count();
        }

        if ($count < $this->contract->treatment_count) {
            $add = $this->contract->treatment_count - $count;
            $insert = [];
            //$source = $this->getSource($service);
            for ($i = 0; $i < $add; $i++) {
                $insert[] = [
                    'service_id' => $service->service->id,
                    'customer_id' => $this->contract->customer->id,
                    'contract_service_id' => $service->id,
                    'price' => $service->price,
                    'source_type' => isset($this->contract->appointment) ? Appointment::class : null,
                    'source_id' => isset($this->contract->appointment) ? $this->contract->appointment->id : null,
                ];
            }
            $this->contract->credits()->createMany($insert);
        }
    }

    private function deleteSurplusCredits(): void
    {
        $this->contract->contractServices->each(fn (ContractService $service) => $this->deleteSurplusCreditsForService($service));
    }

    private function deleteSurplusCreditsForService(ContractService $service, ?int $count = null): void
    {
        if ($count === null) {
            $count = $this->contract->credits()->where('service_id', $service->service_id)->count();
        }

        if ($count > $this->contract->treatment_count) {
            $delete = $count - $this->contract->treatment_count;
            $this->contract->credits()->where('service_id', $service->service_id)->unused()->limit(intval($delete))->delete();
        }
    }

    private function updateCreditPrices(): void
    {
        $this->contract->contractServices->each(fn (ContractService $service) => $this->updateCreditPriceForService($service));
    }

    private function updateCreditPriceForService(ContractService $service): void
    {
        $this->contract->credits()->where('service_id', $service->service_id)->unused()->update(['price' => $service->price]);
    }
}
