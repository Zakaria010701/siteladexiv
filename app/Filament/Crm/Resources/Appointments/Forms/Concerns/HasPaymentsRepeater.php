<?php

namespace App\Filament\Crm\Resources\Appointments\Forms\Concerns;

use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\Action;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Schemas\Components\Utilities\Set;
use App\Enums\Appointments\AppointmentOrderStatus;
use App\Enums\Transactions\PaymentType;
use App\Filament\Crm\Resources\Invoices\InvoiceResource;
use App\Models\Appointment;
use App\Models\Contract;
use App\Models\CustomerCredit;
use App\Models\Invoice;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

trait HasPaymentsRepeater
{
    private function getPaymentsRepeater(): Repeater
    {
        return Repeater::make('payments')
        ->relationship('payments')
        ->columns(4)
        ->defaultItems(0)
        ->hiddenOn('create')
        ->afterStateUpdated(fn () => $this->calculate())
        ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Get $get): array {
            $data['customer_id'] = $get('customer_id');

            return $data;
        })
        ->mutateRelationshipDataBeforeSaveUsing(function (array $data, Get $get): array {
            $data['customer_id'] = $get('customer_id');

            return $data;
        })
        ->addAction(fn (Action $action, Get $get) => $this->getPaymentsRepeaterAddAction($action, $get))
        ->deleteAction(fn (Action $action) => $this->getPaymentsRepeaterDeleteAction($action))
        ->extraItemActions([
            $this->getPaymentsRepeaterDownloadAction(),
            $this->getPaymentsRepeaterConvertAction(),
        ])
        ->table(fn (Get $get) => $this->getPaymentsRepeaterHeaders($get))
        ->schema([
            Hidden::make('reference_type'),
            Hidden::make('reference_id'),
            $this->getPaymentsRepeaterTypeSelect(),
            Toggle::make('pays_at_next_appointment')
                ->inline(false)
                ->helperText(__('Pays at next appointment'))
                ->visible(fn (Get $get) => $get('type')?->getReferenceType() === Invoice::class),
            Select::make('credit')
                ->live(debounce: 3)
                ->visible(fn (Get $get) => $get('type') === PaymentType::Credit)
                ->required(fn (Get $get) => $get('type') === PaymentType::Credit)
                ->formatStateUsing(fn (Get $get) => $get('reference_type') == CustomerCredit::class ? $get('reference_id') : null)
                ->afterStateUpdated(function (int $state, Set $set) {
                    $set('reference_type', CustomerCredit::class);
                    $set('reference_id', $state);
                })
                ->options(fn (Get $get) => CustomerCredit::query()
                    ->where('customer_id', $get('../../customer_id'))
                    ->get()
                    ->mapWithKeys(fn (CustomerCredit $credit) => [
                        $credit->id => sprintf('%s %s', formatMoney($credit->open_amount), formatDate($credit->created_at)),
                    ])
                ),
            TextInput::make('note')
                ->hidden()
                ->dehydrated()
                ->dehydratedWhenHidden()
                ->maxLength(255),
            TextInput::make('amount')
                ->live(onBlur: true)
                ->suffix('€')
                ->required()
                ->numeric(),
            KeyValue::make('meta')
                ->hidden(),
        ]);
    }

    private function getPaymentsRepeaterTypeSelect(): Select
    {
        return Select::make('type')
            ->live(debounce: 3)
            ->required()
            ->options(PaymentType::class)
            ->disableOptionWhen(fn (string $value) => in_array($value, [
                PaymentType::Contract->value,
                PaymentType::Debit->value,
                PaymentType::Goodwill->value,
                PaymentType::PayPal->value,
                PaymentType::PriceChange->value,
                PaymentType::Proforma->value,
                PaymentType::Split->value,
                PaymentType::Transaction->value,
            ]))
            ->helperText(function (?PaymentType $state, Get $get) {
                $reference = $get('reference_id');
                if(empty($reference)) {
                    return null;
                }

                if($state == PaymentType::Invoice) {
                    $invoice = Invoice::find($reference);

                    if(is_null($invoice)) {
                        return __('Pays at next appointment');
                    }

                    $url = InvoiceResource::getUrl('edit', ['record' => $invoice]);

                    return view('forms.components.appointments.invoice-payment-helper-text', [
                        'url' => $url,
                        'invoice' => $invoice->invoice_number,
                    ]);
                }
            })
            ->afterStateUpdated(function (?PaymentType $state, Set $set) {
                if (! is_null($state?->getReferenceType())) {
                    $set('reference_type', $state->getReferenceType());
                    $set('reference_id', null);
                    $set('credit', null);
                } else {
                    $set('reference_type', null);
                    $set('reference_id', null);
                    $set('credit', null);
                }
            });
    }

    private function getPaymentsRepeaterAddAction(Action $action, Get $get) : Action
    {
        return $action
            ->icon('heroicon-o-plus')
            ->label(__('Payment'))
            ->action(function (Repeater $component) use ($get): void {
                $newUuid = $component->generateUuid();

                $items = $component->getState();

                if ($newUuid) {
                    $items[$newUuid] =  [];
                } else {
                    $items[] = [];
                }

                $component->state($items);

                $component->getChildComponentContainer($newUuid ?? array_key_last($items))->fill($this->getNewPaymentItem($get, $items) ?? []);

                $component->collapsed(false, shouldMakeComponentCollapsible: false);

                $component->callAfterStateUpdated();
            });
    }

    private function getPaymentsRepeaterDeleteAction(Action $action): Action
    {
        return $action->requiresConfirmation(function (array $arguments, Repeater $component) {
            $item = $component->getState()[$arguments['item']];
            if(isset($item)) {
                return !empty($item['reference_id']);
            }
            return false;
        });
    }

    private function getPaymentsRepeaterDownloadAction(): Action
    {
        return Action::make('download')
            ->icon('heroicon-m-arrow-down-tray')
            ->iconButton()
            ->visible(function (array $arguments, Get $get, Repeater $component) {
                if(empty($arguments['item'])) {
                    return false;
                }

                /** @var ?Payment */
                $payment = $component->getCachedExistingRecords()[$arguments['item']] ?? null;

                if(is_null($payment) || is_null($payment->reference_id)) {
                    return false;
                }

                if($payment->type != PaymentType::Invoice) {
                    return false;
                }

                return true;
            })
            ->action(function (array $arguments, Appointment $record, Repeater $component) {
                /** @var Payment */
                $payment = $component->getCachedExistingRecords()[$arguments['item']];

                /** @var Invoice */
                $invoice = $payment->reference;
                return response()->streamDownload(function () use ($invoice) {
                    echo Pdf::loadView('pdf.invoice', ['invoice' => $invoice])->stream();
                }, "$invoice->invoice_number.pdf");
            });
    }

    private function getPaymentsRepeaterConvertAction(): Action
    {
        return Action::make('convertToCredit')
            ->icon('heroicon-m-arrows-right-left')
            ->color('primary')
            ->iconButton()
            ->requiresConfirmation()
            ->visible(function (array $arguments, Get $get, Repeater $component) {
                if($get('type') == PaymentType::Credit) {
                    return false;
                }

                if(empty($arguments['item'])) {
                    return false;
                }

                /** @var ?Payment */
                $payment = $component->getCachedExistingRecords()[$arguments['item']] ?? null;

                if(empty($payment)) {
                    return false;
                }

                if(isset($payment->customerCredit)) {
                    return false;
                }

                return true;
            })
            ->action(function (array $arguments, Appointment $record, Repeater $component) {
                /** @var Payment */
                $payment = $component->getCachedExistingRecords()[$arguments['item']];

                $credit = $record->customer->customerCredits()->create([
                    'source_id' => $payment->id,
                    'source_type' => Payment::class,
                    'amount' => $payment->amount,
                ]);

                Notification::make()
                    ->title(__('status.result.success'))
                    ->success()
                    ->send();
            });
    }

    private function getPaymentsRepeaterHeaders(Get $get): array
    {
        /*$color = match($get('appointmentOrder.status')) {
            AppointmentOrderStatus::Paid => 'text-green-500',
            AppointmentOrderStatus::Open => 'text-red-500',
            default => 'text-gray-500',
        };
        $paid = formatMoney($get('appointmentOrder.paid_total'));
        $trans = [
            'paid' => __('Paid total'),
        ];*/
        return [
            Repeater\TableColumn::make(__('Type')),
            Repeater\TableColumn::make(__('Pays at next appointment'))
                ->wrapHeader(),
            Repeater\TableColumn::make(__('Credit')),
            Repeater\TableColumn::make(__('Note')),
            Repeater\TableColumn::make(__('Amount')),
        ];
    }

    private function getNewPaymentItem(Get $get, array $items = []): ?array
    {
        $credit = CustomerCredit::query()
            ->where('customer_id', $get('customer_id'))
            ->notSpent()
            ->whereNotIn('id', collect($items)->where('reference_type', CustomerCredit::class)->pluck('reference_id')->toArray())
            ->latest()
            ->first();

        $amount = $get('appointmentOrder.gross_total') - $get('appointmentOrder.paid_total');

        if($amount < 0) {
            return null;
        }

        if(empty($credit)) {
            return [
                'amount' => $amount,
            ];
        }

        if($amount > $credit->open_amount) {
            $amount = $credit->open_amount;
        }

        return [
            'type' => PaymentType::Credit->value,
            'reference_type' => CustomerCredit::class,
            'reference_id' => $credit->id,
            'credit' => $credit->id,
            'amount' => $amount,
        ];
    }
}
