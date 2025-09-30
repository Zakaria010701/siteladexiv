<?php

namespace App\Hooks\Contracts;

use App\Hooks\Contracts\Concerns\HandlesServiceCredits;
use App\Models\Appointment;
use App\Models\Contract;
use App\Models\ContractService;

class AfterCreateContract
{
    use HandlesServiceCredits;

    public function __construct(private Contract $contract) {}

    public static function make(Contract $contract): self
    {
        return new self($contract);
    }

    public function execute(): Contract
    {
        $this->createMissingCredits();

        $this->markCreditedAppointmentAsUsed();

        return $this->contract;
    }

    private function markCreditedAppointmentAsUsed()
    {
        if(is_null($this->contract->creditedAppointment)) return;

        $this->contract->contractServices->each(function (ContractService $service) {
            $this->contract->credits()->where('service_id', $service->service_id)->limit(1)->update([
                'usage_type' => Appointment::class,
                'usage_id' => $this->contract->creditedAppointment->id,
                'used_at' => now(),
            ]);
        });
    }
}
