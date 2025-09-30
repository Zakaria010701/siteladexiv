<?php

namespace App\Actions\Customers;

use App\Models\Customer;

class FindCustomer
{
    public function __construct(
        private string $email,
        private ?string $phone_number,
    ) {}

    public static function make(
        array $data,
    ): self {
        return new self(
            email: $data['email'],
            phone_number: $data['phone_number'],
        );
    }

    public function execute(): ?Customer
    {
        // Check if a customer already has the specified email
        $customer = Customer::where('email', $this->email)
            ->withTrashed()
            ->first();
        if (isset($customer)) {
            return $customer;
        }

        if (! empty($this->phone_number)) {
            $customer = Customer::firstWhere('phone_number', $this->phone_number);
            if (isset($customer)) {
                return $customer;
            }
        }

        return null;
    }
}
