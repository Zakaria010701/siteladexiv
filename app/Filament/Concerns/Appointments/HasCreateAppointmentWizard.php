<?php

namespace App\Filament\Concerns\Appointments;

use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use App\Actions\Appointments\CalculateDuration;
use App\Enums\Appointments\AppointmentStatus;
use App\Enums\Appointments\AppointmentType;
use App\Enums\Gender;
use App\Filament\Crm\Resources\Customers\Forms\CustomerForm;
use App\Forms\Components\ItemActions;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Service;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

trait HasCreateAppointmentWizard
{

    protected function getCreateAppointmentSchema(): array
    {
        return [
            $this->getCreateAppointmentWizard(),
        ];
    }
    protected function getCreateAppointmentWizard(): Wizard
    {
        return Wizard::make([
            $this->getCategoryStep(),
            $this->getCustomerStep(),
            $this->getServicesStep(),
            $this->getTimeStep(),
        ])
            ->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button
                    type="submit"
                    size="md"
                >
                    {{ __('filament-actions::create.single.modal.actions.create.label') }}
                </x-filament::button>
            BLADE)))
            ->contained(false);
    }

    protected function getCategoryStep(): Step
    {
        return Step::make(__('Category'))
            ->icon('heroicon-o-folder')
            ->description(fn (Get $get) => Category::find($get('category_id'))?->name)
            ->schema([
                Select::make('type')
                    ->label(__('Type'))
                    ->live()
                    ->options(AppointmentType::class)
                    ->required(),
                Select::make('category_id')
                    ->label(__('Category'))
                    ->live()
                    ->relationship('category', 'name')
                    ->required(fn (Get $get) =>$get('type')?->requiresCustomer() ?? false),
            ]);
    }

    protected function getCustomerStep(): Step
    {
        return Step::make(__('Customer'))
            ->icon('heroicon-o-user')
            ->description(fn (Get $get) => Customer::find($get('customer_id'))?->full_name)
            ->columns(4)
            ->schema([
                Select::make('customer_id')
                    ->live()
                    ->columnSpan(3)
                    ->relationship('customer', 'lastname', modifyQueryUsing: function (Builder $query, Get $get) {
                        $birthday = $get('birthday');
                        if (empty($birthday)) {
                            return $query;
                        }
                        return $query->where('birthday', '=', $birthday);
                    })
                    ->afterStateUpdated(function (int $state, Set $set) {
                        $customer = Customer::find($state);
                        if(! $customer) {
                            return;
                        }

                        $set('contract_select_actions', $customer->contracts()->unused()->get()->mapWithKeys(fn (Contract $contract) => [
                            Str::uuid()->toString() => [
                                'id' => $contract->id,
                                'title' => $contract->label,
                            ]
                        ])->toArray());
                    })
                    ->getOptionLabelFromRecordUsing(fn (Customer $record) => $record->label)
                    ->searchable(['firstname', 'lastname', 'birthday'])
                    ->createOptionForm(fn (Schema $schema) => CustomerForm::compact($schema))
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

    private function getServicesStep()
    {
        return Step::make(__('Services'))
            ->icon('heroicon-o-shopping-bag')
            ->description(fn (Get $get) => Service::whereIn('id', $get('services'))->implode('short_code', ', '))
            ->schema([
                ItemActions::make('contract_select_actions')
                    ->label(__('Select contract'))
                    ->schema([
                        Hidden::make('id'),
                        Hidden::make('title'),
                    ])
                    ->extraItemActions([
                        Action::make('select_contract')
                            ->label(fn (array $arguments, ItemActions $component) => $component->getItemState($arguments['item'])['title'])
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
                            ->hidden(fn (?Appointment $record, Get $get) => $this->getLastAppointment($record, $get) === null)
                            ->action(function (?Appointment $record, Get $get, Set $set) {
                                $lastAppointment = $this->getLastAppointment($record, $get);
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
                    ])
                    ->columnSpanFull(),
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
                    ->default(now()->endOfHour()->addSecond()->addMinutes(general()->default_appointment_time))
                    ->required()
                    ->numeric()
                    ->formatStateUsing(fn ($state, Get $get): int => isset($state) ? Carbon::parse($get('start'))->diffInMinutes($state) : 0)
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

    private function getLastAppointment(?Appointment $record, Get $get, $path = ''): ?Appointment
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
            ->when($record, fn (Builder $query) => $query->where('id', '!=', $record->id))
            ->where('start', '<', $start)
            ->latest('start')
            ->first();
    }

}
