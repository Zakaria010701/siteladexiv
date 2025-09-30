<?php

namespace App\Filament\Actions\Appointments;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Enums\Appointments\AppointmentStatus;
use App\Enums\Contracts\ContractType;
use App\Enums\Transactions\DiscountType;
use App\Enums\Transactions\PaymentType;
use App\Forms\Components\TableRepeater;
use App\Forms\Components\TableRepeater\Header;
use App\Hooks\Contracts\AfterCreateContract;
use App\Models\Appointment;
use App\Models\Category;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\DiscountTemplate;
use App\Models\Payment;
use App\Models\Service;
use App\Notifications\Customers\CustomerNotification;
use App\Support\Calculator;
use App\Support\TemplateSupport;
use Barryvdh\DomPDF\Facade\Pdf;
use Closure;
use Exception;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CreateContractAction extends Action
{
    private int|Closure $quantity;

    public static function getDefaultName(): ?string
    {
        return 'create-contract';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (CreateContractAction $action) => __('Contract :quantity', ['quantity' => $action->getQuantity()]));

        $this->form(fn (Schema $schema) => $this->getContractForm($schema));

        $this->fillForm(fn (Appointment $record, Get $get) => $this->fillContractForm($record, $get('category_id'), $get('services')));

        $this->extraModalFooterActions(fn () => [
            $this->getDownloadAction(),
        ]);

        $this->action(fn (array $data, ?Appointment $record, Get $get, Set $set) => $this->storeContract($data, $record, $get, $set));
    }

    private function getContractForm(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                Select::make('type')
                    ->required()
                    ->options(ContractType::class),
                TextInput::make('treatment_count')
                    ->label(__('Purchased treatments'))
                    ->live(onBlur: true)
                    ->required()
                    ->numeric()
                    ->afterStateUpdated(function (int $state,Get $get, Set $set) {
                        $set('treatments', $state-1);
                        $this->updatedTreatmentCount($get, $set);
                    }),
                TextInput::make('treatments')
                    ->live(onBlur: true)
                    ->numeric()
                    ->visible(fn (Get $get) => $get('credit_last_appointment'))
                    ->afterStateUpdated(function (int $state,Get $get, Set $set) {
                        $set('treatment_count', $state+1);
                        $this->updatedTreatmentCount($get, $set);
                    }),
                Hidden::make('previous_id'),
                Toggle::make('credit_last_appointment')
                    ->live()
                    ->visible(fn (Get $get) => !is_null($get('previous_id')))
                    ->inline(false),
                Placeholder::make('previous_info')
                    ->label(__('Last appointment'))
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => $get('credit_last_appointment'))
                    ->content(function (Get $get) {
                        $appointment = Appointment::find($get('previous_id'));
                        if(is_null($appointment)) {
                            return null;
                        }
                        return view('forms.components.contracts.previous-appointment-info', [
                            'appointment' => $appointment,
                            'category' => $get('category_id'),
                            'services' => $get('service_ids'),
                        ]);
                    }),
                TableRepeater::make('credit_payments')
                    ->deletable(false)
                    ->reorderable(false)
                    ->addable(false)
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => $get('credit_last_appointment'))
                    ->headers([
                            Header::make(__('Type')),
                            Header::make(__('Amount')),
                            Header::make(__('Credit')),
                    ])
                    ->schema([
                        Hidden::make('id'),
                        Select::make('type')
                            ->disabled()
                            ->options(PaymentType::class),
                        TextInput::make('amount')
                            ->disabled()
                            ->dehydrated()
                            ->suffix('€')
                            ->required()
                            ->numeric(),
                        Toggle::make('credit')
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => $this->updatedForm($get, $set, '../../')),
                    ]),
                TextInput::make('default_price')
                    ->columnStart(1)
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->suffix('€'),
                TextInput::make('discount_percentage')
                    ->live(onBlur: true)
                    ->numeric()
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->updatedForm($get, $set))
                    ->suffix('%'),
                TextInput::make('sub_total')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->visible(fn (Get $get) => $get('credit_last_appointment'))
                    ->suffix('€'),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->suffix('€'),
                Select::make('category_id')
                    ->label(__('Category'))
                    ->columnStart(1)
                    ->live(onBlur: true)
                    ->searchable()
                    ->preload()
                    ->options(Category::query()->pluck('name', 'id'))
                    ->required(),
                Select::make('service_ids')
                    ->label(__('Services'))
                    ->live(onBlur: true)
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->columnSpan(3)
                    ->options(fn (Get $get) => Service::query()->where('category_id', $get('category_id'))->pluck('name', 'id'))
                    ->afterStateUpdated(function (array $state, Get $get, Set $set) {
                        $services = Service::query()->whereIn('id', $state)->get()->map(fn (Service $item) => [
                            'service_id' => $item->id,
                            'default_price' => $item->price,
                            'price' => Calculator::applyDiscount($item->price, $get('discount_percentage')),
                        ]);
                        $set('services', $services->toArray());
                        $this->updatedForm($get, $set);
                    })
                    ->required(),
                Repeater::make('services')
                    ->required()
                    ->columnSpanFull()
                    ->columns(3)
                    ->hidden()
                    ->dehydrated()
                    ->dehydratedWhenHidden()
                    ->reorderable(false)
                    ->schema([
                        Select::make('service_id')
                            ->required()
                            ->options(Service::query()->pluck('name', 'id'))
                            ->dehydrated()
                            ->searchable(),
                        TextInput::make('default_price')
                            ->disabled()
                            ->numeric(),
                        TextInput::make('price')
                            ->live()
                            ->required()
                            ->numeric()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $set('../../price', collect($get('../../services'))->sum('price') * $get('../../treatment_count'));
                            }),
                    ]),
                Textarea::make('description')
                    ->columnSpanFull(),
                Placeholder::make('contract_discounts')
                    ->label(__('Contract discounts'))
                    ->columnSpanFull()
                    ->content(function (Get $get) {
                        $services = collect($get('services'));
                        return view('forms.components.contracts.contract-discounts', [
                            'discounts' => DiscountTemplate::query()
                                ->where('type', DiscountType::Quantity)
                                ->orderBy('quantity')
                                ->get()
                                ->map(fn(DiscountTemplate $item) => [
                                    'unit_price' => $services->sum('default_price'),
                                    'quantity' => $item->quantity,
                                    'total' => $services->sum('default_price') * $item->quantity,
                                    'percentage' => $item->percentage,
                                    'amount' => $item->amount,
                                    'contract_price' => $services->sum('price') * $item->quantity,
                                    'saving' => $services->sum('default_price') * $item->quantity - $services->sum('price') * $item->quantity,
                                ])
                                ->toArray(),
                        ]);
                    }),
            ]);
    }

    private function updatedTreatmentCount(Get $get, Set $set) {
        $treatment_count = $get('treatment_count');
        if(is_null($treatment_count) || $treatment_count <= 0) {
            $treatment_count = 1;
            $set('treatment_count', $treatment_count);
        }

        $discount = DiscountTemplate::query()
            ->where('type', DiscountType::Quantity)
            ->when($treatment_count, fn (Builder $query) => $query->where('quantity', '<=', $treatment_count))
            ->orderByDesc('quantity')
            ->first();

        $services = collect($get('services'));

        if(isset($discount)) {
            $services = $services->map(fn (array $item) => [
                'service_id' => $item['service_id'],
                'default_price' => $item['default_price'],
                'price' => Calculator::applyDiscount($item['default_price'], $discount?->percentage),
            ]);
            $set('percentage', $discount?->percentage);
            $set('services', $services->toArray());
        }
        $this->updatedForm($get, $set);
    }

    private function updatedForm(Get $get, Set $set, string $path = '') {
        $treatment_count = $get($path.'treatment_count');

        $services = collect($get($path.'services'));

        $services = $services->map(fn (array $item) => [
            'service_id' => $item['service_id'],
            'default_price' => $item['default_price'],
            'price' => Calculator::applyDiscount($item['default_price'], $get($path.'discount_percentage')),
        ]);

        $set($path.'default_price', $services->sum('default_price') * $treatment_count);
        $price = $services->sum('price') * $treatment_count;
        $set($path.'sub_total', $price);

        if($get($path.'credit_last_appointment')) {
            $reduce = collect($get($path.'credit_payments'))->where('credit', true)->sum('amount');
            $set($path.'price', $price - $reduce);
        } else {
            $set($path.'price', $price);
        }
    }

    private function fillContractForm(Appointment $record, int $category_id, array $service_ids): array
    {
        $quantity = $this->getQuantity();

        $discount = DiscountTemplate::query()
            ->where('type', DiscountType::Quantity)
            ->when($quantity, fn (Builder $query) => $query->where('quantity', '<=', $quantity))
            ->orderByDesc('quantity')
            ->first();

        $services = Service::query()
            ->whereIn('id', $service_ids)
            ->get()
            ->map(fn (Service $item) => [
                'service_id' => $item->id,
                'default_price' => $item->price,
                'price' => isset($discount) ? (
                    isset($discount->percentage) ? Calculator::applyDiscount($item->price, $discount->percentage) : $item->price - $discount->amount
                ) : $item->price,
            ]);

        $prev = $record->customer->appointments()
            ->paid()
            ->status(AppointmentStatus::Done)
            ->where('start', '<', $record->start)
            ->latest('start')
            ->first();

        if(isset($prev) && !$record->customer->contracts()->where('credited_appointment_id', $prev->id)->exists()) {
            $payments = $prev->payments()
                ->doesntHave('customerCredit')
                ->doesntHave('creditable')
                ->get()
                ->map(fn (Payment $payment) => [
                    'id' => $payment->id,
                    'type' => $payment->type->value,
                    'amount' => $payment->amount,
                    'credit' => false,
                ])
                ->toArray();
        }

        return [
            'type' => ContractType::VK->value,
            'treatment_count' => $quantity,
            'treatments' => $quantity-1,
            'discount_percentage' => $discount?->percentage ?? 0,
            'default_price' => $services->sum('default_price') * $quantity,
            'price' => $services->sum('price') * $quantity,
            'sub_total' => $services->sum('price') * $quantity,
            'category_id' => $category_id,
            'service_ids' => $service_ids,
            'services' => $services->toArray(),
            'previous_id' => $prev?->id ?? null,
            'credit_payments' => $payments ?? null,
        ];
    }

    private function storeContract(array $data, ?Appointment $record, Get $get, Set $set): void
    {
        $data['appointment_id'] = $record?->id;
        $data['customer_id'] = $get('customer_id');
        if($data['credit_last_appointment'] && !is_null($data['previous_id'])) {
            $data['credited_appointment_id'] = $data['previous_id'];
        }
        $data['date'] = Carbon::parse($get('start'))->format('Y-m-d');
        $contract = new Contract();
        $contract->fill($data);
        $contract->saveQuietly();
        $contract->contractServices()->createMany($data['services']);
        $payment_ids = collect($data['credit_payments'])->where('credit', true)->pluck('id');
        $payments = Payment::whereIn('id', $payment_ids)->get();
        $contract->creditedPayments()->saveMany($payments);
        AfterCreateContract::make($contract)->execute();

        $this->success();
        $this->callAfter();
    }

    private function getDownloadAction()
    {
        return Action::make('download-contract-discounts')
            ->button()
            ->label(__('Download'))
            /*->url(function () use ($get) {

                return route('contracts.discounts.download');
            }, shouldOpenInNewTab: true)*/
            ->action(function (Get $get) {
                $services = collect($get('services'));

                $discounts = DiscountTemplate::query()
                    ->where('type', DiscountType::Quantity)
                    ->orderBy('quantity')
                    ->get()
                    ->map(fn(DiscountTemplate $item) => [
                        'unit_price' => $services->sum('default_price'),
                        'quantity' => $item->quantity,
                        'total' => $services->sum('default_price') * $item->quantity,
                        'percentage' => $item->percentage,
                        'amount' => $item->amount,
                        'contract_price' => $services->sum('price') * $item->quantity,
                        'saving' => $services->sum('default_price') * $item->quantity - $services->sum('price') * $item->quantity,
                    ])
                    ->toArray();
                return response()->streamDownload(function () use ($discounts) {
                    echo Pdf::loadView('pdf.contract-discounts', ['discounts' => $discounts])->stream();
                }, 'contract.pdf');
            });
    }

    public function quantity(int|Closure $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->evaluate($this->quantity ?? 1);
    }

    public function getName(): ?string
    {
        if (blank($this->name)) {
            $actionClass = static::class;

            throw new Exception("Action of class [$actionClass] must have a unique name, passed to the [make()] method.");
        }

        $quantity = $this->getQuantity();
        return $this->name . "-$quantity";
    }
}
