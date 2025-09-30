<?php

namespace App\Filament\Actions\Appointments;

use Filament\Schemas\Components\Wizard;
use Exception;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use App\Actions\Appointments\BookAppointment;
use App\Actions\Appointments\CalculateDuration;
use App\Actions\Calendar\FindCalendarOpenings;
use App\DataObjects\Calendar\CalendarOpening;
use App\Enums\Appointments\AppointmentStatus;
use App\Enums\Appointments\AppointmentType;
use App\Enums\Gender;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use App\Filament\Crm\Resources\Customers\Forms\CustomerForm;
use App\Filament\Schemas\Components\CustomerSelect;
use App\Forms\Components\ItemActions;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Support\Appointment\BookingCalculator;
use App\Support\Calendar\CalendarOpeningCalculator;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\Livewire;

class QuickbookAction extends CreateAction
{
    public static function getDefaultName(): ?string
    {
        return 'quickbook';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Book'));

        $this->authorize(AppointmentResource::canCreate());

        $this->model(Appointment::class);

        $this->modalHeading(__('Book'));

        $this->modalIcon('heroicon-o-arrows-pointing-out');

        $this->schema([
            Wizard::make([
                $this->getCustomerStep(),
                $this->getCategoryStep(),
                $this->getServicesStep(),
                $this->getTimeStep(),
            ])->contained(false),
        ]);

        $this->fillForm([
            'date' => 'today',
        ]);

        $this->extraModalFooterActions(fn (Action $action) => [
            $action->makeModalSubmitAction('createAndEdit', arguments: ['edit' => true])
                ->label(__('Create & Edit')),
        ]);

        $this->modalFooterActionsAlignment(Alignment::End);

        $this->successNotification(Notification::make()
            ->success()
            ->title(__('status.result.success')));

        $this->action(function (array $data, array $arguments) {
            try {
                $appointment = BookAppointment::make(
                    date: CarbonImmutable::parse($data['date'])->setTimeFromTimeString($data['time']),
                    appointmentType: $data['appointment_type'],
                    room: $data['room_id'],
                    category: $data['category_id'],
                    services: $data['services'],
                    customer: $data['customer_id'],
                    user: $data['user_id'],
                    resources: $data['resource_ids'],
                    status: AppointmentStatus::Approved,
                )->execute();
            } catch (Exception $e) {
                Notification::make()
                    ->danger()
                    ->title(__('status.result.error'))
                    ->send();
                $this->halt(shouldRollBackDatabaseTransaction: true);
            }

            if($arguments['edit'] ?? false) {
                $this->redirect(AppointmentResource::getUrl('edit', ['record' => $appointment]));
            }

            $this->success();
        });
    }

    private function getCustomerStep()
    {
        return Step::make(__('Customer'))
            ->icon('heroicon-o-building-office')
            ->live(onBlur: true)
            //->description(fn (Get $get) => Customer::find($get('customer_id'))?->full_name)
            ->schema([
                CustomerSelect::make()
                    ->afterStateUpdated(function (?int $state, Set $set) {
                        if(is_null($state)) {
                            return;
                        }

                        $lastAppointment = Appointment::query()
                            ->where('customer_id', $state)
                            ->status(AppointmentStatus::Done)
                            ->where('start', '<=', today()->endOfDay())
                            ->latest('start')
                            ->first();

                        if(is_null($lastAppointment)) {
                            return;
                        }

                        $set('user_id', $lastAppointment->done_by_id ?? $lastAppointment->user_id);
                        //$set('branch_id', $lastAppointment->branch_id);
                        $set('category_id', $lastAppointment->category_id);
                        $set('appointment_type', AppointmentType::Treatment);
                        $set('service_packages', $lastAppointment->servicePackages->pluck('id')->toArray());
                        $set('services', $lastAppointment->getServices()->pluck('id')->toArray());
                        $duration = CalculateDuration::make(AppointmentType::Treatment, $lastAppointment->getServices()->pluck('id')->toArray())->execute();
                        $set('duration', $duration);
                    })
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    private function getCategoryStep(): Step
    {
        return Step::make(__('Category'))
            ->icon('heroicon-o-building-office')
            ->description(fn (Get $get) => Category::find($get('category_id'))?->name)
            ->schema([
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
            ]);
    }

    private function getServicesStep()
    {
        return Step::make(__('Services'))
            ->icon('heroicon-o-shopping-bag')
            ->description(fn (Get $get) => Service::whereIn('id', $get('services'))->implode('short_code', ', '))
            ->schema([
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
                        $duration = CalculateDuration::make($get('appointment_type'), $services)->execute();
                        $set('duration', $duration);
                    }),
                CheckboxList::make('services')
                    ->live(onBlur: true, debounce: '1s')
                    ->options(fn (Get $get) => Service::query()->where('category_id', $get('category_id'))->pluck('name', 'id'))
                    ->afterStateUpdated(function (array $state, Get $get, Set $set) {
                        $duration = CalculateDuration::make($get('appointment_type'), $state)->execute();
                        $set('duration', $duration);
                        $this->findCalendarOpenings($get, $set);
                    })
                    ->columns(4)
                    ->required(),
            ]);
    }

    private function getTimeStep()
    {
        return Step::make(__('Time'))
            ->icon('heroicon-o-calendar-days')
            ->description(fn (Get $get) => ! (is_null($get('date')) || is_null($get('time')))
                ? Carbon::parse($get('date'))->setTimeFromTimeString($get('time'))->format(getDateTimeFormat())
                : null
            )
            ->columns(2)
            ->schema([
                Select::make('user_id')
                    ->live()
                    ->relationship('user', 'name', modifyQueryUsing: fn (Builder $query) => $query->provider())
                    ->searchable()
                    ->preload()
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->findCalendarOpenings($get, $set)),
                Select::make('branch_id')
                    ->live()
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload()
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->findCalendarOpenings($get, $set)),
                TimePicker::make('time')
                    ->required()
                    ->readOnly(),
                TextInput::make('duration')
                    ->integer()
                    ->live()
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->findCalendarOpenings($get, $set)),
                Hidden::make('room_id'),
                Hidden::make('resource_ids'),
                DatePicker::make('date')
                    ->native(false)
                    ->displayFormat(getDateFormat())
                    ->columnSpanFull()
                    ->prefixActions([
                        Action::make('subMonth')
                            ->icon('heroicon-o-chevron-double-left')
                            ->action(function ($state, Get $get, Set $set) {
                                $set('date', Carbon::parse($state)->subMonth()->toDateString());
                                $this->findCalendarOpenings($get, $set);
                            }),
                        Action::make('subWeek')
                            ->icon('heroicon-o-chevron-left')
                            ->action(function ($state, Get $get, Set $set) {
                                $set('date', Carbon::parse($state)->subWeek()->toDateString());
                                $this->findCalendarOpenings($get, $set);
                            }),
                        Action::make('subDay')
                            ->icon('heroicon-o-minus')
                            ->action(function ($state, Get $get, Set $set) {
                                $set('date', Carbon::parse($state)->subDay()->toDateString());
                                $this->findCalendarOpenings($get, $set);
                            }),
                    ])
                    ->suffixActions([
                        Action::make('addDay')
                            ->icon('heroicon-o-plus')
                            ->action(function ($state, Component $livewire, Get $get, Set $set) {
                                $set('date', Carbon::parse($state)->addDay()->toDateString());
                                //$livewire->ref
                            }),
                        Action::make('addWeek')
                            ->icon('heroicon-o-chevron-right')
                            ->action(function ($state, Get $get, Set $set) {
                                $set('date', Carbon::parse($state)->addWeek()->toDateString());
                                $this->findCalendarOpenings($get, $set);
                            }),
                        Action::make('addMonth')
                            ->icon('heroicon-o-chevron-double-right')
                            ->action(function ($state, Get $get, Set $set) {
                                $set('date', Carbon::parse($state)->addMonth()->toDateString());
                                $this->findCalendarOpenings($get, $set);
                            }),
                    ])
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->findCalendarOpenings($get, $set)),
                Fieldset::make()
                    ->columns(3)
                    ->schema(function (Get $get) {
                        if(empty($get('date')) || empty($get('appointment_type'))) {
                            return [];
                        }
                        $openings = CalendarOpeningCalculator::make(
                            start: Carbon::parse($get('date'))->startOfDay(),
                            end: Carbon::parse($get('date'))->endOfDay(),
                            appointmentType: $get('appointment_type'),
                            services: $get('services'),
                        )->findCalendarSlots();

                        $actions = [];

                        /** @var CalendarOpening $opening */
                        foreach($openings as $opening) {
                            $actions[] = Action::make("opening")
                                ->label(sprintf("(%s) %s %s",
                                    $opening->start->format('H:i'),
                                    $opening->room->branch->name,
                                    $opening->user->lastname,
                                ))
                                ->color('primary')
                                ->action(function (Set $set) use ($opening) {
                                    $set('time',  $opening->start->format('H:i'));
                                    $set('user_id', $opening->user->id);
                                    $set('branch_id', $opening->room->branch_id);
                                    $set('room_id', $opening->room->id);
                                    $set('resource_ids', collect($opening->resources)->pluck('id')->toArray());
                                });
                        }

                        return $actions;
                    }),
            ]);
    }

    private function findCalendarOpenings(Get $get, Set $set) {
        $openings = CalendarOpeningCalculator::make(
            start: Carbon::parse($get('date'))->startOfDay(),
            end: Carbon::parse($get('date'))->endOfDay(),
            appointmentType: $get('appointment_type'),
            services: $get('services'),
        )->findCalendarSlots();

        $set('time_options', $openings->map(fn (CalendarOpening $opening) => [
            'title' => sprintf("%s-%s %s %s",
                $opening->start->format('H:i'),
                $opening->end->format('H:i'),
                $opening->room->branch->name,
                $opening->user->full_name,
            ),
            'start' => $opening->start,
            'end' => $opening->end,
            'user_id' => $opening->user->id,
            'room_id' => $opening->room->id,
            'branch_id' => $opening->room->branch_id,
            'resource_ids' => collect($opening->resources)->pluck('id')->toArray(),
        ]));
    }
}
