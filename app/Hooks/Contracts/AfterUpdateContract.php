<?php

namespace App\Hooks\Contracts;

use App\Hooks\Contracts\Concerns\HandlesServiceCredits;
use App\Models\Contract;

class AfterUpdateContract
{
    use HandlesServiceCredits;

    public function __construct(private Contract $contract) {}

    public static function make(Contract $contract): self
    {
        return new self($contract);
    }

    public function execute(): Contract
    {
        $this->deleteUnusedCreditsForMissingServices();
        $this->createMissingCredits();
        $this->deleteSurplusCredits();
        $this->updateCreditPrices();

        return $this->contract;
    }
}
