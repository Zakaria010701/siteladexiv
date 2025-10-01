<?php

namespace App\Livewire;

use Filament\Forms\Contracts\HasForms;
use Illuminate\Http\Request;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Flex;
use Filament\Forms\Components\Hidden;
use Throwable;
use App\Actions\Appointments\BookAppointment;
use App\Actions\Customers\FindOrCreateCustomer;
use App\Enums\Appointments\AppointmentType;
use App\Enums\Gender;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Support\Appointment\BookingCalculator;
use Carbon\CarbonImmutable;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Livewire\Component;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

/**
 * @property \Filament\Schemas\Schema $form
 */
class Booking extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function render(): View
    {
        return view('livewire.booking');
    }

    public function mount(): void
    {
        $this->form->fill();
    
        $serviceId = request('service_id');
        if ($serviceId) {
            $service = Service::find($serviceId);
            if ($service) {
                $this->form->fill([
                    'category_id' => $service->category_id,
                    'services' => [$service->id],
                ]);
            }
        }
    
        $cartServices = request('cart_services', '');
        $cartPackages = request('cart_packages', '');

        $serviceIds = [];
        $packageIds = [];

        if ($cartServices) {
            $serviceIds = explode(',', $cartServices);
        }
        if ($cartPackages) {
            $packageIds = explode(',', $cartPackages);
        }

        $allServices = $serviceIds;
        if (!empty($packageIds)) {
            $packages = ServicePackage::whereIn('id', $packageIds)->with('services')->get();
            foreach ($packages as $package) {
                $allServices = array_merge($allServices, $package->services->pluck('id')->toArray());
            }
            $this->form->fill(['service_packages' => $packageIds]);
        }

        if (!empty($serviceIds)) {
            // Convert service IDs to service_selections format
            $serviceSelections = [];
            foreach ($serviceIds as $serviceId) {
                $serviceSelections[] = [
                    'service_id' => $serviceId,
                    'quantity' => 1,
                ];
            }
            $this->form->fill(['service_selections' => $serviceSelections]);
        }

        if (!empty($allServices)) {
            $firstService = Service::find($allServices[0]);
            if ($firstService) {
                $this->form->fill(['category_id' => $firstService->category_id]);
            }
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make(__('Branch'))
                        ->icon('heroicon-o-building-office')
                        ->description(fn (Get $get) => Branch::find($get('branch_id'))?->name)
                        ->schema([
                            Select::make('branch_id')
                                ->label(__('Branch'))
                                ->live(onBlur: true)
                                ->relationship('branch', 'name')
                                ->required(),
                            Select::make('appointment_type')
                                ->label(__('Type'))
                                ->live(onBlur: true)
                                ->options(AppointmentType::getBookingTypes())
                                ->required(),
                            Select::make('category_id')
                                ->label(__('Category'))
                                ->live(onBlur: true)
                                ->relationship('category', 'name')
                                ->required(),
                        ]),
                    Step::make(__('Services'))
                        ->icon('heroicon-o-shopping-bag')
                        ->description(fn (Get $get) => $this->getSelectedServicesDescription($get('service_selections')))
                        ->schema([
                            Select::make('service_packages')
                                ->live(onBlur: true)
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->options(fn (Get $get) => ServicePackage::query()
                                    ->where('category_id', $get('category_id'))
                                    ->pluck('name', 'id')
                                )
                                ->columnSpanFull()
                                ->afterStateUpdated(function (?array $state, Get $get, Set $set) {
                                    if (empty($state)) {
                                        $set('services', []);

                                        return;
                                    }
                                    $services = Service::whereHas('servicePackages', fn (Builder $query) => $query->whereIn('service_packages.id', $state))->get();
                                    $set('services', $services->pluck('id')->toArray());
                                }),
                            Repeater::make('service_selections')
                                ->label('Services')
                                ->schema([
                                    Select::make('service_id')
                                        ->label('Service')
                                        ->options(fn (Get $get) => Service::query()->where('category_id', $get('../../category_id'))->pluck('name', 'id'))
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (?string $state, Get $get, Set $set) {
                                            if ($state) {
                                                $service = Service::find($state);
                                                if ($service) {
                                                    $set('../unit_price', $service->price);
                                                }
                                            }
                                        }),
                                    TextInput::make('quantity')
                                        ->label('Quantity')
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (?string $state, Get $get, Set $set) {
                                            $quantity = (int) $state;
                                            $unitPrice = (float) $get('../unit_price');
                                            $set('../subtotal', $quantity * $unitPrice);
                                        }),
                                    TextInput::make('unit_price')
                                        ->label('Unit Price')
                                        ->disabled()
                                        ->dehydrated(false),
                                    TextInput::make('subtotal')
                                        ->label('Subtotal')
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->prefix('€'),
                                ])
                                ->columns(4)
                                ->columnSpanFull()
                                ->default([])
                                ->live(onBlur: true, debounce: '1s')
                                ->afterStateUpdated(function (?array $state, Get $get, Set $set) {
                                    // Update available time slots when services change
                                    $set('../services', collect($state)->pluck('service_id')->filter()->toArray());
                                }),
                        ]),
                    Step::make(__('Time'))
                        ->icon('heroicon-o-calendar-days')
                        ->description(fn (Get $get) => ! (is_null($get('date')) || is_null($get('time')))
                            ? Carbon::parse($get('date'))->setTimeFromTimeString($get('time'))->format(getDateTimeFormat())
                            : null
                        )
                        ->schema([
                            Flex::make([
                                DatePicker::make('date')
                                    ->grow(false)
                                    ->live(onBlur: true),
                                ToggleButtons::make('time')
                                    ->live()
                                    ->options(fn (Get $get) => BookingCalculator::make(
                                        start: Carbon::parse($get('date'))->startOfDay(),
                                        end: Carbon::parse($get('date'))->endOfDay(),
                                        appointmentType: $get('appointment_type'),
                                        branch: $get('branch_id'),
                                        category: $get('category_id'),
                                        services: $this->getSelectedServiceIds($get('service_selections')),
                                        providers: null,
                                    )->openOptions())
                                    ->columns([
                                        'default' => 2,
                                        'sm' => 4,
                                        'lg' => 6,
                                        'xl' => 8,
                                    ])
                                    ->hidden(fn (Get $get) => empty($get('date')))
                                    ->required(),
                            ])->from('md'),
                            Hidden::make('room_id'),
                            Hidden::make('user_id'),
                        ]),
                    Step::make(__('Contact'))
                        ->icon('heroicon-o-clipboard-document-list')
                        ->schema([
                            Select::make('gender')
                                ->options(Gender::class)
                                ->required(),
                            TextInput::make('firstname')
                                ->required(),
                            TextInput::make('lastname')
                                ->required(),
                            TextInput::make('email')
                                ->required(),
                            PhoneInput::make('phone_number'),
                        ]),
                ])
                    ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                    <x-filament::button
                        type="submit"
                    >
                        {{ __('Submit') }}
                    </x-filament::button>
                BLADE))),
            ])
            ->model(Appointment::class)
            ->statePath('data');
    }

    private function getSelectedServiceIds(?array $serviceSelections): array
    {
        if (empty($serviceSelections)) {
            return [];
        }

        $serviceIds = [];
        foreach ($serviceSelections as $selection) {
            if (isset($selection['service_id']) && isset($selection['quantity'])) {
                // Add the service ID multiple times based on quantity
                for ($i = 0; $i < (int)$selection['quantity']; $i++) {
                    $serviceIds[] = $selection['service_id'];
                }
            }
        }

        return $serviceIds;
    }

    private function getSelectedServicesDescription(?array $serviceSelections): string
    {
        if (empty($serviceSelections)) {
            return '';
        }

        $descriptions = [];
        foreach ($serviceSelections as $selection) {
            if (isset($selection['service_id']) && isset($selection['quantity'])) {
                $service = Service::find($selection['service_id']);
                if ($service) {
                    $quantity = (int) $selection['quantity'];
                    $descriptions[] = $quantity > 1
                        ? "{$service->short_code} ({$quantity}x)"
                        : $service->short_code;
                }
            }
        }

        return implode(', ', $descriptions);
    }

    public function book(): void
    {
        $data = $this->form->getState();

        try {
            $customer = FindOrCreateCustomer::make($data)->execute();

            // Extract services from service_selections
            $services = [];
            if (!empty($data['service_selections'])) {
                foreach ($data['service_selections'] as $selection) {
                    if (isset($selection['service_id']) && isset($selection['quantity'])) {
                        $service = Service::find($selection['service_id']);
                        if ($service) {
                            // Add the service multiple times based on quantity
                            for ($i = 0; $i < (int)$selection['quantity']; $i++) {
                                $services[] = $service;
                            }
                        }
                    }
                }
            }

            $appointment = BookAppointment::make(
                date: CarbonImmutable::parse($data['date'])->setTimeFromTimeString($data['time']),
                appointmentType: $data['appointment_type'],
                room: $data['room_id'],
                category: $data['category_id'],
                services: $services,
                customer: $customer,
                user: $data['user_id'],
                status: \App\Enums\Appointments\AppointmentStatus::Pending,
            )
                ->execute();
        } catch (Throwable $e) {
            Notification::make()
                ->title(__('Something went wrong!'))
                ->body(__('Please try again later.'))
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title(__('Your appointment has been booked!'))
            ->success()
            ->send();
    }
}
