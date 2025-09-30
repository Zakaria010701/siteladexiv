<?php

namespace App\Observers;

use App\Actions\Vouchers\RedeemVoucher;
use App\Models\Voucher;

class VoucherObserver
{
    /**
     * Handle the Voucher "created" event.
     */
    public function created(Voucher $voucher): void
    {
        if (isset($voucher->customer)) {
            RedeemVoucher::make($voucher, $voucher->customer)->execute();
        }
    }

    /**
     * Handle the Voucher "updated" event.
     */
    public function updated(Voucher $voucher): void
    {
        if (isset($voucher->customer) && is_null($voucher->customerCredit)) {
            RedeemVoucher::make($voucher, $voucher->customer)->execute();
        }
    }

    /**
     * Handle the Voucher "deleted" event.
     */
    public function deleted(Voucher $voucher): void
    {
        //
    }

    /**
     * Handle the Voucher "restored" event.
     */
    public function restored(Voucher $voucher): void
    {
        //
    }

    /**
     * Handle the Voucher "force deleted" event.
     */
    public function forceDeleted(Voucher $voucher): void
    {
        //
    }
}
