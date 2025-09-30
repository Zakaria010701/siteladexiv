<?php

namespace App\Forms\Components;

use Filament\Actions\Action;
use Filament\Support\Enums\Size;
use App\Enums\Verifications\VerificationStatus;
use App\Models\Customer;
use Closure;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CustomerDetails extends Field
{
    protected string $view = 'forms.components.customer-details';

    protected int|Customer|Closure $customer;

    protected null|Customer $cachedCustomer = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerActions([
            fn (CustomerDetails $component): Action => $component->getVerifyAction(),
        ]);
    }

    public function getCustomer(): ?Customer
    {
        if(!is_null($this->cachedCustomer)) {
            return $this->cachedCustomer;
        }

        $customer = $this->evaluate($this->customer) ?? null;

        if (is_int($customer) || is_string($customer)) {
            $customer = Customer::findOrFail($customer);
        }

        $this->cachedCustomer = $customer;

        return $customer;
    }

    public function customer(int|Customer|Closure $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getVerifyActionName(): string
    {
        return 'verify';
    }

    public function getVerifyAction(): Action
    {
        return Action::make('verify')
            ->label(__('Verify'))
            ->icon('heroicon-o-check')
            ->size(Size::ExtraSmall)
            ->color(fn (CustomerDetails $component) => $component->getCustomer()->verificationStatus()->getColor())
            ->schema([
                Textarea::make('note'),
            ])
            ->action(function (array $data, CustomerDetails $component, Action $action) {
                $customer = $component->getCustomer();
                if(!isset($customer)) {
                    $action->failure();
                    return;
                }
                $customer->verifications()->create([
                    'user_id' => auth()->user()->id,
                    'status' => VerificationStatus::Pass,
                    'note' => $data['note'] ?? null,
                ]);

                $action->success();
            });
    }
}
