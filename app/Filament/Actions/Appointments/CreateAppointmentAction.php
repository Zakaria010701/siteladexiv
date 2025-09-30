<?php

namespace App\Filament\Actions\Appointments;

use Filament\Support\Enums\Width;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Section;
use App\Actions\Appointments\BookAppointment;
use App\Actions\Appointments\CalculateDuration;
use App\Enums\Appointments\AppointmentStatus;
use App\Enums\Appointments\AppointmentType;
use App\Enums\Gender;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Filament\Crm\Resources\Customers\Forms\CustomerForm;
use App\Filament\Schemas\Components\CustomerSelect;
use App\Forms\Components\ItemActions;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\WorkTime;
use App\Support\Appointment\AppointmentCalculator;
use App\Support\Appointment\BookingCalculator;
use App\Support\AvailabilitySupport;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class CreateAppointmentAction extends CreateAction
{
    public static function getDefaultName(): ?string
    {
        return 'create-appointment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Create'));

        $this->authorize(AppointmentResource::canCreate());

        $this->model(Appointment::class);

        $this->modalHeading(__('Book'));

        $this->modalIcon('heroicon-m-plus');

        $this->modalWidth(Width::SevenExtraLarge);

        $this->mountUsing(
            function (Schema $schema, array $arguments) {
                $schema->fill([
                    'status' => AppointmentStatus::Pending->value,
                    'type' => AppointmentType::Treatment->value,
                    'start' => $arguments['start'] ?? null,
                    'end' => general()->default_appointment_time,
                    'branch_id' => auth()->user()->current_branch_id ?? Branch::first()->id,
                    'room_id' => $arguments['resource']['id'] ?? null,
                    'user_id' => AvailabilitySupport::findCalendarAvailabilityEventForTime(
                        time: CarbonImmutable::parse($arguments['start']),
                        room: $arguments['resource']['id']
                    )?->availability?->planable_id,
                ]);
            }
        );

        $this->mutateDataUsing(function (array $data): array {
            $data['status'] = AppointmentStatus::Pending;

            return $data;
        });

        $this->schema([
            Grid::make(4)->schema([
                Wizard::make([
                    $this->getCustomerStep(),
                    $this->getServicesStep(),
                    $this->getTimeStep(),
                ])->contained(false)->columnSpan(3),
                $this->getSidebar()
            ]),
        ]);

        $this->successNotification(Notification::make()
            ->success()
            ->title(__('status.result.success')));

        $this->before(function (array $data) {
            $start = Carbon::parse($data['start']);
            $end = Carbon::parse($data['end']);
            /** @var null|Appointment */
            $overlapp = Appointment::query()
                ->notCanceled()
                ->where('start', '<', $end)
                ->where('end', '>', $start)
                ->where('room_id', $data['room_id'])
                ->first();

            if(empty($overlapp)) {
                return;
            }

            Notification::make()
                ->warning()
                ->color('warning')
                ->title(__('Overlapp detected'))
                ->body(__('Overlapps with :overlapp', ['overlapp' => $overlapp->title]))
                ->send();

            //$action->halt();
        });

        $this->action(function (array $data, array $arguments, Schema $schema) {
            $model = $this->getModel();

            $record = $this->process(function (array $data, HasActions $livewire) use ($model): Appointment {
                $record = new Appointment;
                $record->fill($data);

                if ($relationship = $this->getRelationship()) {
                    /** @phpstan-ignore-next-line */
                    $relationship->save($record);

                    return $record;
                }

                $record->save();

                return $record;
            });

            $this->record($record);
            $schema->model($record)->saveRelationships();

            $details = AppointmentCalculator::make($record, [
                'services' => $data['services'] ?? [],
                'type' => AppointmentType::from($data['type']),
                'customer_id' => $record->customer->id,
            ])->updatedCustomer()->updatedServices()->saveData();

            $record->appointmentItems()->createMany($details['items']);
            $record->appointmentServiceDetails()->createMany($details['serviceDetails']);
            $record->discounts()->createMany($details['discounts']);
            $record->appointmentOrder()->create($details['appointmentOrder']);

            if ($arguments['another'] ?? false) {
                $this->callAfter();
                $this->sendSuccessNotification();

                $this->record(null);

                // Ensure that the form record is anonymized so that relationships aren't loaded.
                $schema->model($model);

                $schema->fill();

                $this->halt();

                return;
            }

            $this->success();
        });
    }

    protected function getCustomerStep(): Step
    {
        return Step::make(__('Customer'))
            ->icon('heroicon-o-user')
            ->description(fn (Get $get) => Customer::find($get('customer_id'))?->full_name)
            ->columns(4)
            ->schema([
                Select::make('type')
                    ->label(__('Type'))
                    ->live()
                    ->options(AppointmentType::class)
                    ->default(AppointmentType::Treatment)
                    ->required(),
                CustomerSelect::make(modifyQueryUsing: function (Builder $query, Get $get) {
                        $birthday = $get('birthday');
                        if (empty($birthday)) {
                            return $query;
                        }
                        return $query->where('birthday', '=', $birthday);
                    })
                    ->live()
                    ->columnSpan(3)
                    ->columnStart(1)
                    ->afterStateUpdated(fn (?int $state, Get $get, Set $set) => $this->afterCustomerUpdated($state, $get, $set))
                    ->required(fn (Get $get) => $get('type')?->requiresCustomer() ?? false),
                DatePicker::make('birthday')
                    ->live(),
                Repeater::make('participants')
                    ->label('')
                    ->relationship('participants')
                    ->columnSpanFull()
                    ->collapsed()
                    ->columns(3)
                    ->defaultItems(0)
                    ->itemLabel(fn (array $state): string => sprintf('%s %s', $state['lastname'], $state['email']))
                    ->addAction(fn (Action $action) => $action->icon('heroicon-o-plus')->label(__('Participant')))
                    ->schema([
                        Select::make('gender')
                            ->required()
                            ->default(Gender::Female->value)
                            ->options(Gender::class),
                        TextInput::make('firstname')
                            ->live(onBlur: true)
                            ->required(),
                        TextInput::make('lastname')
                            ->live(onBlur: true)
                            ->required(),
                        TextInput::make('email')
                            ->live(onBlur: true)
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required(),
                        PhoneInput::make('phone_number')
                            ->displayNumberFormat(PhoneInputNumberType::INTERNATIONAL)
                            ->defaultCountry('DE'),
                    ]),
            ]);
    }

    private function afterCustomerUpdated(?int $state, Get $get, Set $set) {
        if(is_null($state)) {
            return;
        }
        /** @var ?Customer */
        $customer = Customer::find($state);
        if(! $customer) {
            return;
        }

        $serviceCredits = $this->getServiceCredits($customer);

        $gender = $customer?->gender;

        $openCredits = $serviceCredits->pluck('id');
        $servicePackageCredits = ServicePackage::query()
            ->with('services')
            ->whereHas('services', fn (Builder $query) => $query->whereIn('services.id', $openCredits))
            ->whereDoesntHave('services', fn (Builder $query) => $query->whereNotIn('services.id', $openCredits))
            ->where(fn (Builder $query) => $query
                ->whereNull('customer_id')
                ->orWhere('customer_id', $customer->id)
            )
            ->when(
                $gender != Gender::NonBinary && ! is_null($gender),
                fn (Builder $query) => $query->whereIn('gender', [$gender->value, Gender::NonBinary->value])
            )
            ->get()
            ->map(fn (ServicePackage $service) => [
                'package' => $service->id,
                'category' => $service->category_id,
                'name' => sprintf('%s (%s)', $service->short_code, $service->services->implode('short_code', ', ')),
                'services' => $service->services->pluck('id')->toArray(),
            ]);

        $set('credit_services_select_actions', $serviceCredits->toArray());

        $set('previous_services_select_actions', $customer->appointments()
            ->status(AppointmentStatus::Done)->get()
            ->map(fn (Appointment $record) => $record->getServices())
            ->flatten()->unique()
            ->mapWithKeys(fn (Service $record) => [
                Str::uuid()->toString() => [
                    'id' => $record->id,
                    'title' => $record->name,
                ]
            ])
            ->toArray());
        $set('contract_select_actions', $customer->contracts()->unused()->get()->mapWithKeys(fn (Contract $contract) => [
            Str::uuid()->toString() => [
                'id' => $contract->id,
                'title' => $contract->label,
            ]
        ])->toArray());

        $lastAppointment = $this->getLastAppointment($get);
        if(isset($lastAppointment)) {
            $set('category_id', $lastAppointment->category_id);
            $set('services', $lastAppointment->getServices()->pluck('id')->toArray());
        }
    }

    private function getServiceCredits(Customer $customer)
    {
        return Service::query()
        ->withCount([
            /* @phpstan-ignore-next-line */
            'serviceCredits' => fn (Builder $query) => $query->where('customer_id', $customer->id)->unused(),
        ])
        ->whereHas('serviceCredits', fn (Builder $query) => $query->where('customer_id', $customer->id)->unused())
        //->orWhereHas('appointmentItems', fn (Builder $query) => $query)
        ->get()
        ->map(fn (Service $service) => [
            'id' => $service->id,
            'category' => $service->category_id,
            'title' => $service->name,
            'open' => $service->service_credits_count,
        ])
        ->reject(fn (array $item) => $item['open'] <= 0)
        ->sortBy('open', descending: true);
    }

    private function getServicesStep()
    {
        return Step::make(__('Services'))
            ->icon('heroicon-o-shopping-bag')
            ->description(fn (Get $get) => Service::whereIn('id', $get('services'))->implode('short_code', ', '))
            ->schema([
                Select::make('category_id')
                    ->label(__('Category'))
                    ->live()
                    ->relationship('category', 'name')
                    ->required(fn (Get $get) => $get('type')?->requiresCustomer() ?? false),
                Select::make('service_packages')
                    ->live(onBlur: true)
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->relationship(
                        name: 'servicePackages',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query, Get $get) {
                            /** @var ?Gender $gender */
                            $gender = ! is_null($get('customer_id')) ? Customer::find($get('customer_id'))?->gender : null;

                            return $query
                                ->where('category_id', $get('category_id'))
                                ->where(fn (Builder $query) => $query
                                    ->whereNull('customer_id')
                                    ->orWhere('customer_id', $get('customer_id'))
                                )
                                ->when(
                                    $gender != Gender::NonBinary && ! is_null($gender),
                                    fn (Builder $query) => $query->whereIn('gender', [$gender->value, Gender::NonBinary->value])
                                );
                        }
                    )
                    ->columnSpanFull()
                    ->afterStateUpdated(function (?array $state, Get $get, Set $set) {
                        if (empty($state)) {
                            $set('services', []);

                            return;
                        }
                        $services = Service::whereHas('servicePackages', fn (Builder $query) => $query->whereIn('service_packages.id', $state))->get();
                        $set('services', $services->pluck('id')->toArray());
                        $duration = CalculateDuration::make($get('type'), $services)->execute();
                        $set('duration', $duration);
                    }),
                CheckboxList::make('services')
                    ->live(onBlur: true, debounce: '1s')
                    ->options(fn (Get $get) => Service::query()->where('category_id', $get('category_id'))->pluck('name', 'id'))
                    ->afterStateUpdated(function (array $state, Get $get, Set $set) {
                        $duration = CalculateDuration::make($get('type'), $state)->execute();
                        $set('duration', $duration);
                    })
                    ->columns(4)
                    ->required(fn (Get $get) => $get('type')?->requiresCustomer() ?? false),
            ]);
    }

    private function getTimeStep() : Step
    {
        return Step::make(__('Time'))
            ->icon('heroicon-o-calendar-days')
            ->description(fn (Get $get) => ! is_null($get('start'))
                ? Carbon::parse($get('start'))->format(getDateTimeFormat())
                : null
            )
            ->columns(2)
            ->schema([
                DateTimePicker::make('start')
                    ->default(now()->endOfHour()->addSecond())
                    ->required(),
                TextInput::make('end')
                    ->label(__('Duration'))
                    ->default(general()->default_appointment_time)
                    ->required()
                    ->numeric()
                    ->dehydrateStateUsing(fn ($state, Get $get): Carbon => Carbon::parse($get('start'))->addMinutes($state)),
                Select::make('branch_id')
                    ->label(__('Branch'))
                    ->live(onBlur: true)
                    ->relationship('branch', 'name')
                    ->required(),
                Select::make('room_id')
                    ->relationship('room', 'name', modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('branch_id', $get('branch_id')))
                    ->required()
                    ->dehydrated(),
                Select::make('user_id')
                    ->live()
                    ->relationship('user', 'name', modifyQueryUsing: fn (Builder $query) => $query->provider())
                    ->searchable()
                    ->preload(),
                Textarea::make('description'),
            ]);
    }

    private function getSidebar(): Grid
    {
        return Grid::make(1)
            ->columnSpan(1)
            //->hiddenOn('create')
            ->grow(false)
            ->schema([
                Section::make(__('Credits'))
                    ->compact()
                    ->schema([
                        ItemActions::make('credit_services_select_actions')
                            ->label(__('Credit Services'))
                            ->schema([
                                Hidden::make('id'),
                                Hidden::make('title'),
                                Hidden::make('category'),
                                Hidden::make('open'),
                            ])
                            ->extraItemActions([
                                Action::make('select_service')
                                    ->label(fn (array $arguments, ItemActions $component) => $component->getItemState($arguments['item'])['title'] ?? '')
                                    ->button()
                                    ->badge(fn (array $arguments, ItemActions $component): null|string|bool => $component->getItemState($arguments['item'])['open'] ?? 0)
                                    ->color('primary')
                                    ->action(function (array $arguments, ItemActions $component, Get $get, Set $set) {
                                        $id = $component->getItemState($arguments['item'])['id'];

                                        $services = array_merge($get('services'), [$id]);
                                        $set('services', $services);
                                    }),
                            ]),
                        ItemActions::make('contract_select_actions')
                            ->label(__('Select contract'))
                            ->schema([
                                Hidden::make('id'),
                                Hidden::make('title'),
                            ])
                            ->extraItemActions([
                                Action::make('select_contract')
                                    ->label(fn (array $arguments, ItemActions $component) => $component->getItemState($arguments['item'])['title'] ?? '')
                                    ->button()
                                    ->color('primary')
                                    ->action(function (array $arguments, ItemActions $component, Get $get, Set $set) {
                                        $id = $component->getItemState($arguments['item'])['id'];
                                        $contract = Contract::find($id);

                                        $services = array_merge($get('services'), $contract->contractServices->pluck('service_id')->toArray());
                                        $set('category_id', $contract->services->first()->category_id);
                                        $set('services', $services);
                                    }),
                            ])
                            ->extraActions([
                                Action::make('select_last_appointment')
                                    ->label(__('Last appointment'))
                                    ->button()
                                    ->hidden(fn (Get $get) => $this->getLastAppointment($get) === null)
                                    ->action(function (Get $get, Set $set) {
                                        $lastAppointment = $this->getLastAppointment($get);
                                        if (is_null($lastAppointment)) {
                                            return;
                                        }

                                        $services = array_merge($get('services'), $lastAppointment->appointmentItems()
                                            ->where('purchasable_type', Service::class)
                                            ->pluck('purchasable_id')
                                            ->toArray());
                                        $set('category_id', $lastAppointment->category_id);
                                        $set('services', $services);
                                    }),
                            ]),
                ]),
            ]);
    }

    private function getLastAppointment(Get $get, $path = ''): ?Appointment
    {
        $customer_id = $get($path.'customer_id');
        $start = $get($path.'start') ?? today()->format('Y-m-d H:i');

        if ($customer_id === null) {
            return null;
        }

        return Appointment::query()
            ->with([
                'appointmentDetail',
            ])
            ->where('customer_id', $customer_id)
            ->where('start', '<', $start)
            ->latest('start')
            ->first();
    }
}
