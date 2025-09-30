<?php

namespace App\Filament\Actions\Customer;

use App\Filament\Actions\Concerns\PerformsMerge;
use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class MergeAction extends Action
{
    use PerformsMerge;

    public static function getDefaultName(): ?string
    {
        return 'merge';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Merge'));

        /** @phpstan-ignore-next-line */
        $this->modalHeading(function (Model $record): string {
            if ($record instanceof Customer) {
                return __('Merge :customer', ['customer' => $record->full_name]);
            }

            if (isset($record->customer) && $record->customer instanceof Customer) {
                return __('Merge :customer', ['customer' => $record->customer->full_name]);
            }

            return __('Merge');
        });

        $this->icon('heroicon-o-arrows-right-left');

        /** @phpstan-ignore-next-line */
        $this->fillForm(function (Model $record): array {
            if ($record instanceof Customer) {
                return $this->getCustomerData($record);
            }

            if (isset($record->customer) && $record->customer instanceof Customer) {
                return $this->getCustomerData($record->customer);
            }

            return [];
        });

        /** @phpstan-ignore-next-line */
        $this->steps($this->getSteps());

        $this->action(function (array $data, Model $record): void {
            if ($record instanceof Customer) {
                $this->merge($data, $record);

                return;
            }

            if (isset($record->customer) && $record->customer instanceof Customer) {
                $this->merge($data, $record->customer);
            }
        });
    }

    private function getCustomerData(Customer $record): array
    {
        $duplicates = Customer::query()
            ->where('id', '!=', $record->id)
            ->where(fn (Builder $query) => $query
                ->where(fn (Builder $query) => $query
                    ->where('firstname', $record->firstname)
                    ->where('lastname', $record->lastname)
                )
                ->orWhereIn('email', $record->emailAddresses->pluck('email'))
                ->orWhereIn('phone_number', $record->phoneNumbers->pluck('phone_number'))
                ->orWhereHas('emailAddresses', fn (Builder $query) => $query->where('email', $record->email))
                ->orWhereHas('phoneNumbers', fn (Builder $query) => $query->where('phone_number', $record->phone_number))
            )
            ->get()
            ->map(fn (Customer $customer): array => [
                'id' => $customer->id,
                'merge' => false,
                'firstname' => $customer->firstname,
                'lastname' => $customer->lastname,
                'email' => $customer->email,
                'phone_number' => $customer->phone_number,
            ])
            ->toArray();

        return [
            'merge' => $duplicates,
            'customer_firstname' => $record->firstname,
            'customer_lastname' => $record->lastname,
            'customer_email' => $record->email,
            'customer_phone_number' => $record->phone_number,
            'firstname' => $record->firstname,
            'lastname' => $record->lastname,
            'email' => $record->email,
            'phone_number' => $record->phone_number,
        ];
    }

    private function getSteps(): array
    {
        return [
            Step::make(__('Merge'))
                ->schema([
                    Fieldset::make(__('Customer'))
                        ->schema([
                            TextInput::make('customer_firstname')
                                ->label(__('Firstname'))
                                ->disabled(),
                            TextInput::make('customer_lastname')
                                ->label(__('Lastname'))
                                ->disabled(),
                            TextInput::make('customer_email')
                                ->label(__('Email'))
                                ->email()
                                ->disabled(),
                            PhoneInput::make('customer_phone_number')
                                ->label(__('Phone Number'))
                                ->displayNumberFormat(PhoneInputNumberType::INTERNATIONAL)
                                ->defaultCountry('DE')
                                ->disabled(),
                        ]),
                    Repeater::make('merge')
                        ->reorderable(false)
                        ->addable(false)
                        ->deletable(false)
                        ->columns(2)
                        ->schema([
                            Hidden::make('id'),
                            Toggle::make('merge')
                                ->live()
                                ->afterStateUpdated(function (bool $state, Set $set) {
                                    $set('merge_appointments', $state);
                                }),
                            Toggle::make('merge_appointments')
                                ->hidden(fn (Get $get) => ! $get('merge')),
                            TextInput::make('firstname')
                                ->columnStart(1)
                                ->disabled(),
                            TextInput::make('lastname')
                                ->disabled(),
                            TextInput::make('email')
                                ->email()
                                ->disabled(),
                            PhoneInput::make('phone_number')
                                ->displayNumberFormat(PhoneInputNumberType::INTERNATIONAL)
                                ->defaultCountry('DE')
                                ->disabled(),
                        ]),
                ]),
            Step::make(__('Details'))
                ->columns(2)
                ->schema([
                    TextInput::make('firstname')
                        ->required(),
                    Select::make('firstname_options')
                        ->label(__('Options'))
                        ->live()
                        ->afterStateUpdated(fn ($state, Set $set) => $set('firstname', $state))
                        ->options(fn (Get $get) => $this->getOptions('firstname', $get)),
                    TextInput::make('lastname')
                        ->required(),
                    Select::make('lastname_options')
                        ->label(__('Options'))
                        ->live()
                        ->afterStateUpdated(fn ($state, Set $set) => $set('lastname', $state))
                        ->options(fn (Get $get) => $this->getOptions('lastname', $get)),
                    TextInput::make('email')
                        ->email()
                        ->required(),
                    Select::make('email_options')
                        ->label(__('Options'))
                        ->live()
                        ->afterStateUpdated(fn ($state, Set $set) => $set('email', $state))
                        ->options(fn (Get $get) => $this->getOptions('email', $get)),
                    PhoneInput::make('phone_number')
                        ->required()
                        ->displayNumberFormat(PhoneInputNumberType::INTERNATIONAL)
                        ->defaultCountry('DE'),
                    Select::make('phone_number_options')
                        ->label(__('Options'))
                        ->live()
                        ->afterStateUpdated(fn ($state, Set $set) => $set('phone_number', $state))
                        ->options(fn (Get $get) => $this->getOptions('phone_number', $get)),
                ]),
        ];
    }

    private function getOptions(string $field, Get $get)
    {
        $options = collect($get('merge'))
            ->where('merge', true)
            ->reject(fn ($item) => empty($item[$field]))
            ->pluck($field, $field);

        $old = $get("customer_$field");
        if (! empty($old)) {
            $options->prepend($old, $old);
        }

        return $options;
    }

    private function merge(array $data, Customer $customer)
    {
        $mergers = collect($data['merge'])
            ->where('merge', true)
            ->mapWithKeys(fn (array $item) => [$item['id'] => $item]);

        $customers = Customer::query()
            ->whereIn('id', $mergers->pluck('id'))
            ->get();

        $customers->each(function (Customer $merger) use ($data, $mergers, $customer) {
            if ($mergers[$merger->id]['merge_appointments']) {
                $this->mergeAppointments($customer, $merger);
            }
            $merger->customerDiscounts()->update(['customer_id' => $customer->id]);
            $merger->serviceCredits()->update(['customer_id' => $customer->id]);
            $merger->payments()->update(['customer_id' => $customer->id]);
            $merger->notes()->update(['customer_id' => $customer->id]);

            if ($merger->email != $data['email'] && $merger->email != null) {
                $customer->emailAddresses()->create([
                    'email' => $merger->email,
                ]);
            }
            if ($merger->phone_number != $data['phone_number'] && $merger->phone_number != null) {
                $customer->phoneNumbers()->create([
                    'phone_number' => $merger->phone_number,
                ]);
            }

            $merger->email = null;
            $merger->phone_number = null;
            $merger->save();
            $merger->delete();
        });

        $customer->firstname = $data['firstname'];
        $customer->lastname = $data['lastname'];
        $customer->email = $data['email'];
        $customer->phone_number = $data['phone_number'];
        $customer->save();

        Notification::make()
            ->title(__('Customer Merged'))
            ->success()
            ->send();
    }

    private function mergeAppointments(Customer $customer, Customer $merger): void
    {
        $merger->appointments()->update(['customer_id' => $customer->id]);
    }
}
