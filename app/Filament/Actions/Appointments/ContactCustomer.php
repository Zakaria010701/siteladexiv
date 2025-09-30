<?php

namespace App\Filament\Actions\Appointments;

use Filament\Actions\Action;
use App\Models\Customer;
use App\Notifications\Customers\CustomerNotification;
use App\Support\TemplateSupport;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ContactCustomer extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'contact-customer';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Contact'));

        $this->modalHeading(function (Model $record): string {
            if ($record instanceof Customer) {
                return __('Contact :customer', ['customer' => $record->full_name]);
            }

            if (isset($record->customer) && $record->customer instanceof Customer) {
                return __('Contact :customer', ['customer' => $record->customer->full_name]);
            }

            return __('Contact');
        });

        $this->icon('heroicon-o-envelope');

        $this->fillForm(function (Model $record): array {
            $customer = $this->getCustomer($record);

            if (is_null($customer)) {
                return [];
            }

            return [
                'email' => $customer->email,
            ];

        });

        $this->form([
            Select::make('email')
                ->options(function (Model $record) {
                    $customer = $this->getCustomer($record);

                    if (is_null($customer)) {
                        return [];
                    }

                    return $customer->emailAddresses
                        ->pluck('email', 'email')
                        ->when(fn () => ! empty($customer->email), fn (Collection $collection) => $collection->prepend($customer->email, $customer->email))
                        ->toArray();
                })
                ->required(),
            TextInput::make('subject')
                ->required(),
            RichEditor::make('content')
                ->columnSpanFull()
                ->json()
                ->required(),
        ]);

        $this->action(fn (array $data, Model $record) => $this->send($data, $this->getCustomer($record), $record));
    }

    private function send(array $data, ?Customer $customer, Model $record): void
    {
        if (is_null($customer)) {
            return;
        }

        $subject = ($record instanceof Customer) ? null : $record;

        \Illuminate\Support\Facades\Notification::routes([
            'mail' => [$data['email'] => $customer->full_name],
        ])->notify(new CustomerNotification($data['subject'], $data['content'], $customer, $subject));

        Notification::make()
            ->title(__('Notification send'))
            ->success()
            ->send();
    }

    private function getCustomer(Model $record): ?Customer
    {
        if ($record instanceof Customer) {
            return $record;
        }

        if (isset($record->customer) && $record->customer instanceof Customer) {
            return $record->customer;
        }

        return null;
    }
}
