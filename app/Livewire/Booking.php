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
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Flex;
use Filament\Forms\Components\TextInput;
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
            $this->form->fill(['services' => $serviceIds]);
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
                        ->description(fn (Get $get) => Service::whereIn('id', $get('services'))->implode('short_code', ', '))
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
                            CheckboxList::make('services')
                                ->live(onBlur: true, debounce: '1s')
                                ->options(fn (Get $get) => Service::query()->where('category_id', $get('category_id'))->pluck('name', 'id'))
                                ->columns(4)
                                ->required(),
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
                                        services: $get('services'),
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

    public function book(): void
    {
        $data = $this->form->getState();

        try {
            $customer = FindOrCreateCustomer::make($data)->execute();
            $appointment = BookAppointment::make(
                date: CarbonImmutable::parse($data['date'])->setTimeFromTimeString($data['time']),
                appointmentType: $data['appointment_type'],
                branch: $data['branch_id'],
                category: $data['category_id'],
                services: $data['services'],
                customer: $customer,
                providers: $data['providers'] ?? null,
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
