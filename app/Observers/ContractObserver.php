<?php

namespace App\Observers;

use App\Hooks\Contracts\AfterCreateContract;
use App\Hooks\Contracts\AfterUpdateContract;
use App\Models\Contract;
use App\Models\ServiceCredit;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class ContractObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Contract "created" event.
     */
    public function created(Contract $contract): void
    {
        AfterCreateContract::make($contract)->execute();
    }

    /**
     * Handle the Contract "updated" event.
     */
    public function updated(Contract $contract): void
    {
        AfterUpdateContract::make($contract)->execute();
    }

    /**
     * Handle the Contract "saved" event.
     */
    public function saved(Contract $contract): void
    {
        AfterUpdateContract::make($contract)->execute();
    }

    /**
     * Handle the Contract "deleted" event.
     */
    public function deleted(Contract $contract): void
    {
        $contract->credits()->delete();
    }

    /**
     * Handle the Contract "restored" event.
     */
    public function restored(Contract $contract): void
    {
        $contract->credits()->onlyTrashed()->restore();
    }

    /**
     * Handle the Contract "force deleted" event.
     */
    public function forceDeleted(Contract $contract): void
    {
        $contract->credits()->forceDelete();
    }
}
