<?php

namespace App\Filament\Crm\Concerns;

use Validator;
use App\Enums\Customers\CustomerOption;
use App\Enums\Invoices\InvoiceStatus;
use App\Enums\Invoices\InvoiceType;
use App\Filament\Actions\Customer\MergeAction;
use App\Models\Customer;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait CheckCustomerValid
{
    protected function checkCustomerValid(Customer $customer): void
    {
        if (! $this->checkCustomerEmailValid($customer)) {
            Notification::make()
                ->warning()
                ->title(__('Customer email is invalid'))
                ->body(__('The email :email is not a valid email', ['email' => $customer->email]))
                ->send();
        }

        if ($this->checkIfDuplicateCustomerExists($customer)) {
            Notification::make()
                ->title(__('Duplicate customer found'))
                ->body(__('Please check if customers can be merged'))
                ->warning()
                ->persistent()
                ->actions([
                    MergeAction::make()
                        ->button(),
                ])
                ->send();
        }

        if ($this->checkIfOpenInvoiceExists($customer)) {
            Notification::make()
                ->title(__('Customer has open invoices'))
                ->warning()
                ->send();
        }

        if ($this->checkIfCustomerMarkedAsNoFurtherAppointments($customer)) {
            Notification::make()
                ->title(__('No Further Appointments'))
                ->danger()
                ->color('danger')
                ->persistent()
                ->send();
        }
    }

    protected function checkCustomerEmailValid(Customer $customer): bool
    {
        $validator = Validator::make([
            'email' => $customer->email,
        ], [
            'email' => 'required|email:rfc,dns',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return false;
        }

        if (Str::endsWith($customer->email, ['@hatkeine.de', '@hatkeineemail', '@nomail.de', '@noemail.de'])) {
            return false;
        }

        return true;
    }

    private function checkIfDuplicateCustomerExists(Customer $customer): bool
    {
        return Customer::query()
            ->where('id', '!=', $customer->id)
            ->where(fn (Builder $query) => $query
                ->where(fn (Builder $query) => $query
                    ->where('firstname', $customer->firstname)
                    ->where('lastname', $customer->lastname)
                )
                ->orWhereIn('email', $customer->emailAddresses->pluck('email'))
                ->orWhereIn('phone_number', $customer->phoneNumbers->pluck('phone_number'))
                ->orWhereHas('emailAddresses', fn (Builder $query) => $query->where('email', $customer->email))
                ->orWhereHas('phoneNumbers', fn (Builder $query) => $query->where('phone_number', $customer->phone_number))
            )
            ->exists();
    }

    private function checkIfOpenInvoiceExists(Customer $customer): bool
    {
        return $customer->invoices()->status(InvoiceStatus::Open)->type(InvoiceType::Invoice)->exists();
    }

    private function checkIfCustomerMarkedAsNoFurtherAppointments(Customer $customer): bool
    {
        return $customer->hasOption(CustomerOption::NoFurtherAppointments);
    }
}
