<?php

namespace App\Actions\Vouchers;

use Exception;
use App\Models\Customer;
use App\Models\Voucher;

class RedeemVoucher
{
    public function __construct(private Voucher $voucher, private Customer $customer) {}

    public static function make(Voucher $voucher, ?Customer $customer): self
    {
        if (is_null($customer)) {
            if (isset($voucher->customer)) {
                $customer = $voucher->customer;
            } else {
                throw new Exception('Voucher needs to have a customer');
            }
        }

        return new self($voucher, $customer);
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        if (is_null($this->voucher->customer)) {
            $this->voucher->customer = $this->customer;
            $this->voucher->save();
        }

        if (isset($this->voucher->customerCredit)) {
            return;
        }

        $this->voucher->customer->customerCredits()->create([
            'source_type' => Voucher::class,
            'source_id' => $this->voucher->id,
            'amount' => $this->voucher->amount,
            'description' => $this->voucher->description,
        ]);
    }
}
