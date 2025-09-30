<?php

namespace App\Actions\Customers;

use App\Enums\Gender;
use App\Models\Customer;

class CreateCustomer
{
    public function __construct(
        private Gender $gender,
        private string $firstname,
        private string $lastname,
        private string $email,
        private ?string $phone_number,
    ) {}

    public static function make(
        array $data
    ): self {
        return new self(
            gender: Gender::from($data['gender']),
            firstname: $data['firstname'],
            lastname: $data['lastname'],
            email: $data['email'],
            phone_number: $data['phone_number'],
        );
    }

    public function execute(): Customer
    {
        return Customer::create([
            'gender' => $this->gender,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
        ]);
    }
}
