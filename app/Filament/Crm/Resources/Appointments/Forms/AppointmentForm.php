<?php

namespace App\Filament\Crm\Resources\Appointments\Forms;

use App\Filament\Crm\Resources\Appointments\Forms\Concerns\HasPaymentsRepeater;
use App\Filament\Crm\Resources\Appointments\Forms\Concerns\HasServiceCreditSection;
use App\Filament\Crm\Resources\Appointments\Forms\Concerns\HasSystemResources;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Actions;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Schemas\Components\View;
use App\Enums\Appointments\AppointmentExtraType;
use App\Enums\Appointments\AppointmentItemType;
use App\Enums\Appointments\AppointmentModule;
use App\Enums\Appointments\AppointmentOrderStatus;
use App\Enums\Appointments\AppointmentStatus;
use App\Enums\Appointments\AppointmentType;
use App\Enums\Appointments\CancelReason;
use App\Enums\Appointments\ConsultationStatus;
use App\Enums\Appointments\Extras\HairType;
use App\Enums\Appointments\Extras\PigmentType;
use App\Enums\Appointments\Extras\Satisfaction;
use App\Enums\Contracts\ContractType;
use App\Enums\Customers\ContactMethod;
use App\Enums\Customers\CustomerOption;
use App\Enums\Gender;
use App\Enums\TimeStep;
use App\Enums\Transactions\DiscountType;
use App\Enums\Transactions\PaymentType;
use App\Enums\Verifications\VerificationStatus;
use App\Filament\Actions\Appointments\ContactCustomer;
use App\Filament\Actions\Appointments\CreateContractAction;
use App\Filament\Admin\Pages\ActivityLog;
use App\Filament\Crm\Resources\Appointments\Pages\EditAppointment;
use App\Filament\Crm\Resources\Contracts\Pages\CreateContract;
use App\Filament\Crm\Resources\Customers\Forms\CustomerForm;
use App\Filament\Crm\Resources\Invoices\InvoiceResource;
use App\Filament\Schemas\Components\CustomerSelect;
use App\Forms\Components\CustomerDetails;
use App\Forms\Components\CustomerMediaLibraryFileUpload;
use App\Forms\Components\InfoField;
use App\Forms\Components\ItemActions;
use App\Forms\Components\ItemRepeater;
use App\Forms\Components\NoteRepeater;
use App\Hooks\Contracts\AfterCreateContract;
use App\Models\Appointment;
use App\Models\AppointmentComplaintType;
use App\Models\AppointmentExtra;
use App\Models\AppointmentModuleSetting;
use App\Models\Category;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\DiscountTemplate;
use App\Models\EnergySetting;
use App\Models\Service;
use App\Models\ServiceCredit;
use App\Models\ServicePackage;
use App\Models\TreatmentType;
use App\Settings\GeneralSettings;
use App\Support\Appointment\AppointmentCalculator;
use App\Support\Appointment\AppointmentFormSupport;
use App\Support\Calculator;
use App\Forms\Components\TableRepeater;
use App\Forms\Components\TableRepeater\Header;
use App\Models\AppointmentItem;
use App\Models\Invoice;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Error;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Component;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class AppointmentForm
{
    use HasPaymentsRepeater;
    use HasServiceCreditSection;
    use HasSystemResources;

    private array $extras;

    private array $modules;

    private ?Appointment $lastAppointment;

    private readonly AppointmentFormSupport $support;

    public function __construct(
        private readonly Schema $form,
        private readonly GeneralSettings $general,
    ) {
        $this->extras = $this->getAppointmentExtrasLookup();

        $this->support = new AppointmentFormSupport($form->getRecord());

        $this->modules = AppointmentModuleSetting::all()
            ->mapWithKeys(fn (AppointmentModuleSetting $item): array => [$item->name->value => $item->appointment_types])
            ->toArray();
    }

    public static function make(Schema $schema): Schema
    {
        /** @var AppointmentForm $static */
        $static = app(self::class, ['form' => $schema]);

        return $static->schema();
    }

    public function calculator(): AppointmentCalculator
    {
        $livewire = $this->form->getLivewire();

        return AppointmentCalculator::make($this->form->getRecord(), data_get($livewire, $this->form->getStatePath()), $this->form->getStatePath());
    }

    public function updateData(array $data)
    {
        $livewire = $this->form->getLivewire();
        data_set($livewire, $this->form->getStatePath(), $data);
    }

    public function calculate(): void
    {
        $this->updateData($this->calculator()->calculate()->saveData());
    }

    public function updatedPrices(): void
    {
        $this->updateData($this->calculator()->updatedPrices()->saveData());
    }

    public function updatedServices(): void
    {
        $this->updateData($this->calculator()->updatedServices()->saveData());
    }

    public static function compact(Schema $schema): Schema
    {
        /** @var AppointmentForm $static */
        $static = app(self::class, ['form' => $schema]);

        return $static->compactSchema();
    }

    private function getAppointmentExtrasLookup(): array
    {
        return Cache::rememberForever('appointment_extras_lookup', function () {
            return AppointmentExtra::query()
                ->leftJoin('appointment_extra_category', 'appointment_extras.id', '=', 'appointment_extra_category.appointment_extra_id')
                ->leftJoin('appointment_extra_treatment_type', 'appointment_extras.id', '=', 'appointment_extra_treatment_type.appointment_extra_id')
                ->get()
                ->map(fn (AppointmentExtra $item) => collect($item->appointment_types)->map(fn (string $type): array => [
                    'type' => $item->type->value,
                    'appointment_type' => $type,
                    'category_id' => $item->category_id ?? null,
                    'default' => $item->default,
                    'is_required' => $item->is_required,
                    'split_per_service' => $item->split_per_service,
                    'take_from_last_appointment' => $item->take_from_last_appointment,
                ]))
                ->flatten(1)
                ->mapToGroups(fn (array $item): array => [$item['appointment_type'] => $item])
                ->mapWithKeys(fn (Collection $group, string $key): array => [$key => $group->mapToGroups(fn (array $item): array => [$item['category_id'] => $item])])
                ->mapWithKeys(fn (Collection $group, string $key): array => [
                    $key => $group->mapWithKeys(fn (Collection $type, string $key): array => [
                        $key => $type->mapWithKeys(fn (array $item) => [$item['type'] => $item]),
                    ]),
                ])
                ->toArray();
        });
    }

    private function schema(): Schema
    {
        return $this->form->components([
            Grid::make(4)
                ->schema([
                    $this->getBody()
                        ->columnSpan(3),
                    $this->getSidebar(),
                ])
                ->columnSpanFull(),
        ]);
    }

    // --- Body -------------------------

    private function getBody(): Grid
    {
        return Grid::make(1)
            ->schema([
                $this->getInfoSection(),
                $this->getOrderSection(),
                $this->getOrderOverviewSection(),
            ]);
    }

    private function getInfoSection(): Section
    {
        return Section::make(__('Info'))
            ->icon('heroicon-o-information-circle')
            ->collapsible()
            ->compact()
            ->columns(2)
            ->schema([
                $this->getScheduleFieldset(),
                $this->getParticipantsFieldset(),
                $this->getServiceFieldset(),
                //$this->getSystemResourceFieldset(),
                $this->getDescriptionTextarea(),
                $this->getMediaSection(),
                $this->getStatusFieldset(),
                $this->getComplaintFieldset(),
                $this->getDetailsFieldset(),
                $this->getServiceDetailsRepeater(),
                $this->getConsultationFieldset(),
                $this->getDoneFieldset(),
            ]);
    }

    private function compactSchema(): Schema
    {
        return $this->form->components([
            $this->getBody(),
        ]);
    }

    private function getScheduleFieldset(): Fieldset
    {
        return Fieldset::make(__('Schedule'))
            ->columns(3)
            ->extraAttributes(['class' => 'border-indigo-500'])
            ->schema([
                DateTimePicker::make('start')
                    ->default(now()->endOfHour()->addSecond())
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                TextInput::make('end')
                    ->label(__('Duration'))
                    ->default($this->general->default_appointment_time)
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->formatStateUsing(fn (?Appointment $record): int => $record?->duration ?? general()->default_appointment_time)
                    ->dehydrateStateUsing(fn ($state, Get $get): Carbon => Carbon::parse($get('start'))->addMinutes($state)),
                Select::make('type')
                    ->live(onBlur: true)
                    ->options(AppointmentType::class)
                    ->default(AppointmentType::Treatment->value)
                    ->required()
                    ->afterStateUpdated(fn () => $this->calculate()),
                Select::make('branch_id')
                    ->live(onBlur: true)
                    ->relationship('branch', 'name')
                    ->default(auth()->user()->current_branch_id)
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Select::make('room_id')
                    ->relationship('room', 'name', modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('branch_id', $get('branch_id')))
                    ->required()
                    ->disabled()
                    ->dehydrated(),
                Select::make('user_id')
                    ->relationship('user', 'name', modifyQueryUsing: fn (Builder $query) => $query->provider())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    private function getParticipantsFieldset(): Fieldset
    {
        return Fieldset::make(__('Participants'))
            ->columns(4)
            ->schema([
                CustomerSelect::make(modifyQueryUsing: function (Builder $query, Get $get) {
                        $birthday = $get('birthday');
                        if (empty($birthday)) {
                            return $query;
                        }
                        return $query->where('birthday', '=', $birthday);
                    })
                    ->afterStateUpdated(fn () => $this->updateData($this->calculator()->updatedCustomer()->calculate()->saveData()))
                    ->required(fn (string $operation, Get $get) => $get('type') != AppointmentType::RoomBlock->value && (empty($get('participants')) || $operation != 'create')),
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

    private function moduleInactive(AppointmentModule $module, Get $get): bool
    {
        return false;//! $this->moduleActive($module, $get);
    }

    private function moduleActive(AppointmentModule $module, Get $get): bool
    {
        return true;//in_array($get('type'), $this->modules[$module->value] ?? []);
    }

    private function getServiceFieldset(): Fieldset
    {
        return Fieldset::make(__('Service'))
            ->hiddenJs(<<<'JS'
                !$get('customer_id')
            JS)
            ->schema([
                Fieldset::make(__('Select contract'))
                    ->schema(function (Get $get) {
                        $customer = Customer::find($get('customer_id'));
                        $contracts = $customer?->contracts()?->unused()?->get() ?? collect();
                        $actions = [];
                        foreach($contracts as $contract) {
                            $actions[] = Action::make('select_contract')
                                ->label($contract->label)
                                ->record($contract)
                                ->model(Contract::class)
                                ->button()
                                ->color('primary')
                                ->action(function ( Contract $record, Get $get, Set $set) {
                                    $services = array_merge($get('services'), $record->pluck('service_id')->toArray());
                                    $set('category_id', $record->services->first()->category_id);
                                    $set('services', $services);
                                    $this->updatedServices();
                                });
                        }
                        $actions[] = $this->getSelectLastAppointmentAction();
                        return [
                            Actions::make($actions),
                        ];
                    }),
                Select::make('category_id')
                    ->live(onBlur: true)
                    ->relationship('category', 'name')
                    ->required()
                    ->afterStateUpdated(function (Set $set) {
                        $set('service_packages', []);
                        $set('services', []);
                        $this->updatedServices();
                    }),
                Select::make('treatment_type_id')
                    ->live(onBlur: true)
                    ->relationship('treatmentType', 'name')
                    ->extraInputAttributes(function ($state) {
                        if (empty($state)) {
                            return [];
                        }
                        $color = Cache::rememberForever("treatment_type-$state-color", fn () => TreatmentType::find($state)->color);

                        return ['style' => "border: 1px solid $color; border-radius: .5rem"];
                    }),
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
                    ->getOptionLabelFromRecordUsing(fn (ServicePackage $record) => $record->title)
                    ->columnSpanFull()
                    ->afterStateUpdated(function (?array $state, Get $get, Set $set) {
                        if (empty($state)) {
                            $set('services', []);
                            $this->updatedPrices();

                            return;
                        }
                        $services = Service::whereHas('servicePackages', fn (Builder $query) => $query->whereIn('service_packages.id', $state))->get();
                        $set('services', array_merge($get('services'), $services->pluck('id')->toArray()));
                        $this->updatedServices();
                    }),
                Select::make('services')
                    ->live(onBlur: true)
                    ->multiple()
                    ->searchable()
                    ->preload()
                    // Since we do not have an actual services relationship on the appointment model,
                    // we are using the options() method to get the services via a query.
                    ->options(fn (Get $get) => Service::query()
                        ->withCount([
                            'servicePackages' => fn ($query) => $query
                                ->whereIn('service_packages.id', $get('service_packages') ?? []),
                        ])
                        ->where('category_id', $get('category_id'))
                        ->get()
                        ->map(function (Service $service) {
                            $label = ($service->service_packages_count > 0)
                                ? "<span style=\"color: green\">$service->title</span>"
                                : $service->title;

                            return [
                                'name' => $label,
                                'id' => $service->id,
                            ];
                        })
                        ->pluck('name', 'id')
                    )
                    ->columnSpanFull()
                    ->allowHtml()
                    ->afterStateUpdated(fn () => $this->updatedServices())
                    ->suffixAction($this->getCreateCustomPackageAction()),
                Select::make('contracts')
                    ->multiple()
                    ->columnSpanFull()
                    ->hiddenOn('create')
                    ->hidden(fn (?Appointment $record): bool => $record?->contracts?->isEmpty() ?? true)
                    ->formatStateUsing(fn (?Appointment $record): array => $record?->contracts?->pluck('id')?->toArray() ?? [])
                    // Using the relationship() method to get the contracts seems to not be working,
                    // as it seemingly retrieves the appointment id instead of the contract id.
                    // Because of that we are using the options() method to get the contracts.
                    ->options(fn (?Appointment $record): array => $record?->contracts?->pluck('title', 'id')?->toArray() ?? []),
                Fieldset::make(__('New contract'))
                    ->hiddenOn('create')
                    ->schema([
                        Actions::make([
                            CreateContractAction::make()
                                ->quantity(4)
                                ->after(fn () => $this->updateData($this->calculator()->createdContract()->updatedPrices()->saveData())),
                            CreateContractAction::make()
                                ->quantity(6)
                                ->after(fn () => $this->updateData($this->calculator()->createdContract()->updatedPrices()->saveData())),
                            CreateContractAction::make()
                                ->quantity(8)
                                ->after(fn () => $this->updateData($this->calculator()->createdContract()->updatedPrices()->saveData())),
                            CreateContractAction::make()
                                ->quantity(10)
                                ->after(fn () => $this->updateData($this->calculator()->createdContract()->updatedPrices()->saveData())),
                            CreateContractAction::make()
                                ->quantity(12)
                                ->after(fn () => $this->updateData($this->calculator()->createdContract()->updatedPrices()->saveData())),
                            $this->getCreateCustomPackageAction()
                                ->label(__('Custom'))
                                ->icon(null),
                        ])->hiddenOn('create')->columnSpanFull(),
                    ]),
            ]);
    }

    private function getSelectLastAppointmentAction(): Action
    {
        return Action::make('select_last_appointment')
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
                $this->updatedServices();
            });
    }

    private function getDescriptionTextarea(): Textarea
    {
        return Textarea::make('description');
    }

    private function getMediaSection()
    {
        return Section::make(__('Media'))
            ->extraAttributes(['class' => 'ring-indigo-500 bg-indigo-50'])
            ->icon('heroicon-o-photo')
            ->iconColor('primary')
            ->compact()
            ->collapsed(true)
            ->columnSpan(1)
            ->schema([
                CustomerMediaLibraryFileUpload::make('media')
                    ->hiddenOn('create')
                    ->downloadable(true)
                    ->customProperties(function (Get $get) {
                        return [
                            'date' => $get('start'),
                        ];
                    })
                    ->multiple(),
            ]);
    }

    private function getCreateCustomPackageAction(): Action
    {
        return Action::make('createCustomPackage')
            ->icon('heroicon-o-bookmark-square')
            ->schema(fn (Schema $schema) => $schema
                ->model(ServicePackage::class)
                ->columns(3)
                ->components([
                    TextInput::make('name')
                        ->required()
                        ->disabled()
                        ->dehydrated()
                        ->maxLength(255),
                    TextInput::make('short_code')
                        ->required()
                        ->disabled()
                        ->dehydrated(),
                    Select::make('gender')
                        ->required()
                        ->disabled()
                        ->dehydrated()
                        ->options(Gender::class),
                    Select::make('category_id')
                        ->live(onBlur: true)
                        ->relationship('category', 'name')
                        ->required(),
                    Select::make('services')
                        ->live(onBlur: true)
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->required()
                        ->relationship(
                            name: 'services',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('category_id', $get('category_id'))
                        )
                        ->columnSpan(2)
                        ->afterStateUpdated(function (array $state, Set $set) {
                            $services = Service::query()->whereIn('id', $state)->get();
                            $set('default_price', $services->sum('price'));
                            $template = DiscountTemplate::query()
                                ->where('type', DiscountType::Package)
                                ->where('quantity', '<=', $services->count())
                                ->orderByDesc('quantity')
                                ->first();
                            $set('default_discount_percentage', $template?->percentage);
                            $set('default_discount', $template?->amount);
                        })
                        ->afterStateHydrated(function (array $state, Set $set) {
                            $services = Service::query()->whereIn('id', $state)->get();
                            $set('default_price', $services->sum('price'));
                            $template = DiscountTemplate::query()
                                ->where('type', DiscountType::Package)
                                ->where('quantity', '<=', $services->count())
                                ->orderByDesc('quantity')
                                ->first();
                            $set('default_discount_percentage', $template?->percentage);
                            $set('default_discount', $template?->amount);
                        }),
                    TextInput::make('default_price')
                        ->disabled()
                        ->numeric()
                        ->suffix('€'),
                    TextInput::make('default_discount_percentage')
                        ->disabled()
                        ->numeric()
                        ->suffix('€'),
                    TextInput::make('default_discount')
                        ->disabled()
                        ->numeric()
                        ->suffix('€'),
                    TextInput::make('price')
                        ->numeric()
                        ->suffix('€')
                        ->requiredWithoutAll(['discount_percentage', 'discount']),
                    TextInput::make('discount_percentage')
                        ->label(__('Percentage'))
                        ->numeric()
                        ->requiredWithoutAll(['price', 'discount']),
                    TextInput::make('discount')
                        ->numeric()
                        ->requiredWithoutAll(['discount_percentage', 'price']),
                ])
            )
            ->fillForm(function (Get $get) {
                /** @var Customer $customer */
                $customer = Customer::findOrFail($get('customer_id'));
                $packageNumber = $customer->customPackages()->withTrashed()->count() + 1;

                return [
                    'name' => sprintf('%s %s %s %s', $customer->firstname, $customer->lastname, __('Package'), $packageNumber),
                    'short_code' => sprintf(
                        '%s%sP %s',
                        substr($customer->firstname, 0, 1),
                        substr($customer->lastname, 0, 1),
                        $packageNumber
                    ),
                    'category_id' => $get('category_id'),
                    'services' => $get('services'),
                    'gender' => $customer->gender->value,
                ];
            })
            ->action(function (Action $action, array $data, Schema $schema, Get $get, Set $set) {
                if (empty($get('customer_id'))) {
                    $action->halt();

                    return;
                }
                $data['customer_id'] = $get('customer_id');
                $record = ServicePackage::create($data);
                $schema->model($record)->saveRelationships();

                $this->updatedServices();
            });
    }

    private function getStatusFieldset(): Fieldset
    {
        return Fieldset::make(__('Status'))
            ->columns(4)
            ->hiddenOn('create')
            ->schema([
                Select::make('status')
                    ->required()
                    ->hidden()
                    ->dehydratedWhenHidden()
                    ->options(AppointmentStatus::class),
                Toggle::make('done_at')
                    ->label(__('Done'))
                    ->live(onBlur: true)
                    ->inline(false)
                    ->afterStateUpdated(function (bool $state, Get $get, Set $set) {
                        $this->updatedPrices();
                        if($state) {
                            $set('canceled_at', false);
                            $set('done_by_id', $get('user_id'));
                            if(empty($get('payments'))) {
                                $item = $this->getNewPaymentItem($get);
                                if(!is_null($item)) {
                                    $set('payments', [Str::uuid()->toString() => $item]);
                                }
                            }
                            $this->updateNextAppointment($get, $set);
                        }
                        $this->updatedPrices();
                    })
                    ->dehydrateStateUsing(fn (bool $state, ?Appointment $record) => $state ? ($record?->done_at ?? now()) : null),
                Toggle::make('has_complaint')
                    ->label(__('Complaint'))
                    ->formatStateUsing(fn (?Appointment $record) => !is_null($record?->complaint))
                    ->inline(false),
                Toggle::make('canceled_at')
                    ->label(__('Canceled'))
                    ->live(onBlur: true)
                    ->partiallyRenderComponentsAfterStateUpdated(['done_at'])
                    ->inline(false)
                    ->afterStateUpdated(fn (bool $state, Set $set) => $set('done_at', false))
                    ->dehydrateStateUsing(fn (bool $state, ?Appointment $record) => $state ? ($record?->canceled_at ?? now()) : null),
                Select::make('cancel_reason')
                    ->options(CancelReason::class)
                    ->hiddenJS(<<<'JS'
                        ! $get('canceled_at')
                    JS),
            ]);
    }

    private function updateNextAppointment(Get $get, Set $set): void
    {
        $service = Service::whereIn('id', $get('services'))
            ->whereNotNull('next_appointment_in')
            ->get(['next_appointment_in', 'next_appointment_step'])
            ->sortBy(fn (Service $service): int => match ($service->next_appointment_step) {
                    TimeStep::Days => $service->next_appointment_in,
                    TimeStep::Weeks => $service->next_appointment_in * 7,
                    TimeStep::Months => $service->next_appointment_in * 30,
                    default => 0,
            })->first();

        if(is_null($service)) {
            return;
        }

        $set('next_appointment_in', $service->next_appointment_in);
        $set('next_appointment_step', $service->next_appointment_step);
    }

    private function getDetailsFieldset(): Fieldset
    {
        return Fieldset::make('details')
            ->label(__('Details'))
            ->columns(3)
            ->relationship('appointmentDetail')
            ->hiddenOn('create')
            ->hiddenJs(<<<'JS'
                ! $get('done_at')
            JS)
            ->schema([
                Select::make('hair_type')
                    ->live(onBlur: true)
                    ->options(HairType::class)
                    ->hidden(fn (Get $get) => $this->isExtraHidden(AppointmentExtraType::HairType, $get))
                    ->default(fn (?Appointment $record, Get $get) => $this->getExtraDefaultValue(AppointmentExtraType::HairType, $record, $get))
                    ->required(fn (Get $get) => $this->isExtraRequired(AppointmentExtraType::HairType, $get))
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->updateEnergyValue($get, $set)),
                Select::make('pigment_type')
                    ->live(onBlur: true)
                    ->options(PigmentType::class)
                    ->hidden(fn (Get $get) => $this->isExtraHidden(AppointmentExtraType::PigmentType, $get))
                    ->default(fn (?Appointment $record, Get $get) => $this->getExtraDefaultValue(AppointmentExtraType::PigmentType, $record, $get))
                    ->required(fn (Get $get) => $this->isExtraRequired(AppointmentExtraType::PigmentType, $get))
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->updateEnergyValue($get, $set)),
                Select::make('satisfaction')
                    ->options(Satisfaction::class)
                    ->hidden(fn (Get $get) => $this->isExtraHidden(AppointmentExtraType::Satisfaction, $get))
                    ->default(fn (?Appointment $record, Get $get) => $this->getExtraDefaultValue(AppointmentExtraType::Satisfaction, $record, $get))
                    ->required(fn (Get $get) => $this->isExtraRequired(AppointmentExtraType::Satisfaction, $get)),
                Select::make('skin_type')
                    ->live(onBlur: true)
                    ->options(AppointmentExtraType::SkinType->options())
                    ->hidden(fn (Get $get) => $this->isExtraHidden(AppointmentExtraType::SkinType, $get))
                    ->default(fn (?Appointment $record, Get $get) => $this->getExtraDefaultValue(AppointmentExtraType::SkinType, $record, $get))
                    ->required(fn (Get $get) => $this->isExtraRequired(AppointmentExtraType::SkinType, $get))
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->updateEnergyValue($get, $set)),
                TextInput::make('milliseconds')
                    ->hidden(fn (Get $get) => $this->isExtraHidden(AppointmentExtraType::Milliseconds, $get))
                    ->default(fn (?Appointment $record, Get $get) => $this->getExtraDefaultValue(AppointmentExtraType::Milliseconds, $record, $get))
                    ->required(fn (Get $get) => $this->isExtraRequired(AppointmentExtraType::Milliseconds, $get))
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        $serviceDetails = collect($get('../serviceDetails'))
                            ->mapWithKeys(function (array $item, string $key) use ($state) {
                                $item['milliseconds'] = $state;

                                return [$key => $item];
                            });

                        $set('../serviceDetails', $serviceDetails->toArray());
                    }),
                TextInput::make('wave_length')
                    ->hidden(fn (Get $get) => $this->isExtraHidden(AppointmentExtraType::WaveLength, $get))
                    ->default(fn (?Appointment $record, Get $get) => $this->getExtraDefaultValue(AppointmentExtraType::WaveLength, $record, $get))
                    ->required(fn (Get $get) => $this->isExtraRequired(AppointmentExtraType::WaveLength, $get))
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        $serviceDetails = collect($get('../serviceDetails'))
                            ->mapWithKeys(function (array $item, string $key) use ($state) {
                                $item['wave_length'] = $state;

                                return [$key => $item];
                            });

                        $set('../serviceDetails', $serviceDetails->toArray());
                    }),
                TextInput::make('color')
                    ->hidden(fn (Get $get) => $this->isExtraHidden(AppointmentExtraType::Color, $get))
                    ->default(fn (?Appointment $record, Get $get) => $this->getExtraDefaultValue(AppointmentExtraType::Color, $record, $get))
                    ->required(fn (Get $get) => $this->isExtraRequired(AppointmentExtraType::Color, $get)),
                TextInput::make('li_count')
                    ->live(onBlur: true)
                    ->numeric()
                    ->hidden(fn (Get $get) => $this->isExtraHidden(AppointmentExtraType::LiCount, $get))
                    ->default(fn (?Appointment $record, Get $get) => $this->getExtraDefaultValue(AppointmentExtraType::LiCount, $record, $get))
                    ->required(fn (Get $get) => $this->isExtraRequired(AppointmentExtraType::LiCount, $get))
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        $serviceDetails = collect($get('../serviceDetails'))
                            ->mapWithKeys(function (array $item, string $key) use ($state) {
                                $item['li_count'] = $state;

                                return [$key => $item];
                            });

                        $set('../serviceDetails', $serviceDetails->toArray());
                    }),
                Select::make('spot_size')
                    ->live(onBlur: true)
                    ->hidden(fn (Get $get) => $this->isExtraHidden(AppointmentExtraType::SpotSize, $get))
                    ->default(fn (?Appointment $record, Get $get) => $this->getExtraDefaultValue(AppointmentExtraType::SpotSize, $record, $get))
                    ->required(fn (Get $get) => $this->isExtraRequired(AppointmentExtraType::SpotSize, $get))
                    ->options(fn (Get $get) => $this->getSpotSizeOptions($get))
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        $serviceDetails = collect($get('../serviceDetails'))
                            ->mapWithKeys(function (array $item, string $key) use ($state) {
                                $item['spot_size'] = $state;

                                return [$key => $item];
                            });

                        $set('../serviceDetails', $serviceDetails->toArray());
                        $this->updateEnergyValue($get, $set);
                    }),
                TextInput::make('energy')
                    ->live(onBlur: true)
                    ->numeric()
                    ->hidden(fn (Get $get) => $this->isExtraHidden(AppointmentExtraType::Energy, $get))
                    ->default(fn (?Appointment $record, Get $get) => $this->getExtraDefaultValue(AppointmentExtraType::Energy, $record, $get))
                    ->required(fn (Get $get) => $this->isExtraRequired(AppointmentExtraType::Energy, $get))
                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                        $serviceDetails = collect($get('../serviceDetails'))
                            ->mapWithKeys(function (array $item, string $key) use ($state) {
                                $item['energy'] = $state;

                                return [$key => $item];
                            });

                        $set('../serviceDetails', $serviceDetails->toArray());
                    }),
                KeyValue::make('meta')
                    ->visible(auth()->user()->can('meta_appointment')),
            ]);
    }

    private function getComplaintFieldset()
    {
        return Fieldset::make('complaint')
            ->label(__('Complaint'))
            ->relationship('complaint')
            ->hiddenOn('create')
            ->hiddenJS(<<<'JS'
                ! $get('has_complaint')
            JS)
            ->schema([
                Select::make('appointment_complaint_type_id')
                    ->label("Complaint type")
                    ->relationship('appointmentComplaintType', 'name')
                    ->required(),
                Textarea::make('note')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    private function isExtraHidden(AppointmentExtraType $type, Get $get, $path = '../'): bool
    {
        //dump($this->extras[$get($path.'type')->value][$get($path.'category_id')]);
        return empty($this->extras[$get($path.'type')->value][$get($path.'category_id')][$type->value]);
    }

    private function getExtraDefaultValue(AppointmentExtraType $type, ?Appointment $record, Get $get, $path = '../'): mixed
    {
        if (! $this->takeExtraFromLastAppointment($type, $get, $path)) {
            return $this->getExtraDefault($type, $get, $path);
        }

        $last = $this->getLastAppointment($record, $get, $path);

        if ($last === null) {
            return $this->getExtraDefault($type, $get, $path);
        }

        return $last->appointmentDetail?->getExtraValue($type);
    }

    private function takeExtraFromLastAppointment(AppointmentExtraType $type, Get $get, $path = '../'): bool
    {
        return $this->extras[$get($path.'type')->value][$get($path.'category_id')][$type->value]['take_from_last_appointment'] ?? false;
    }

    private function shouldExtraSplitPerService (AppointmentExtraType $type, Get $get, $path = '../'): bool
    {
        return $this->extras[$get($path.'type')->value][$get($path.'category_id')][$type->value]['split_per_service'] ?? false;
    }

    private function getExtraDefault(AppointmentExtraType $type, Get $get, $path = '../'): mixed
    {
        return $this->extras[$get($path.'type')->value][$get($path.'category_id')][$type->value]['default'] ?? null;
    }

    private function isExtraRequired(AppointmentExtraType $type, Get $get, $path = '../'): bool
    {
        return $this->extras[$get($path.'type')->value][$get($path.'category_id')][$type->value]['is_required'];
    }

    private function getLastAppointment(?Appointment $record, Get $get, $path = ''): ?Appointment
    {
        try {
            return $this->lastAppointment;
        } catch (Error $e) {
            $customer_id = $get($path.'customer_id');
            $start = $get($path.'start') ?? today()->format('Y-m-d H:i');

            if ($customer_id === null) {
                return null;
            }

            $this->lastAppointment = Appointment::query()
                ->with([
                    'appointmentDetail',
                ])
                ->where('customer_id', $customer_id)
                ->when($record, fn (Builder $query) => $query->where('id', '!=', $record->id))
                ->where('start', '<', $start)
                ->latest('start')
                ->first();

            return $this->lastAppointment;
        }
    }


    private function updateEnergyValue(Get $get, Set $set, $path = '../'): void
    {
        if ($this->isExtraHidden(AppointmentExtraType::Energy, $get, $path)) {
            return;
        }

        $treatmentType = $get($path.'treatment_type_id');
        $hairType = ! $this->isExtraHidden(AppointmentExtraType::HairType, $get, $path) ? $get($path.'appointmentDetail.hair_type') : null;
        $pigmentType = ! $this->isExtraHidden(AppointmentExtraType::PigmentType, $get, $path) ? $get($path.'appointmentDetail.pigment_type') : null;
        $skinType = ! $this->isExtraHidden(AppointmentExtraType::SkinType, $get, $path) ? $get($path.'appointmentDetail.skin_type') : null;
        $spotSize = ! $this->isExtraHidden(AppointmentExtraType::SpotSize, $get, $path) ? $get($path.'appointmentDetail.spot_size') : null;

        $energy = EnergySetting::query()
            ->where('treatment_type_id', $treatmentType)
            ->when(
                value: $hairType,
                callback: fn (Builder $query) => $query->where('hair_type', $hairType),
                default: fn (Builder $query) => $query->whereNull('hair_type'))
            ->when(
                value: $pigmentType,
                callback: fn (Builder $query) => $query->where('pigment_type', $pigmentType),
                default: fn (Builder $query) => $query->whereNull('pigment_type'))
            ->when(
                value: $skinType,
                callback: fn (Builder $query) => $query->where('skin_type', $skinType),
                default: fn (Builder $query) => $query->whereNull('skin_type'))
            ->when(
                value: $spotSize,
                callback: fn (Builder $query) => $query->where('spot_size', $spotSize),
                default: fn (Builder $query) => $query->whereNull('spot_size'))
            ->value('energy');

        if (empty($energy)) {
            return;
        }

        $set($path.'appointmentDetail.energy', $energy);

        $serviceDetails = collect($get($path.'serviceDetails'))
            ->mapWithKeys(function (array $item, string $key) use ($energy) {
                $item['energy'] = $energy;

                return [$key => $item];
            });

        $set('../serviceDetails', $serviceDetails->toArray());
    }

    private function getSpotSizeOptions(Get $get, $path = '../'): array
    {
        $state = $get($path.'treatment_type_id');
        if (empty($state)) {
            return [];
        }

        return AppointmentExtraType::getSpotSizeOptions($state);
    }

    private function getServiceDetailsRepeater(): Repeater
    {
        return Repeater::make('serviceDetails')
            ->addable(false)
            ->deletable(false)
            ->reorderable(false)
            ->columnSpanFull()
            ->columns(4)
            ->collapsed()
            ->relationship('appointmentServiceDetails')
            ->itemLabel(function (array $state, Get $get): string {
                $service = Service::find($state['service_id']);

                if (is_null($service)) {
                    return '';
                }

                return $service->name;
            })
            ->hiddenOn('create')
            ->hiddenJS(<<<'JS'
                ! $get('done_at')
            JS)
            ->table(function (Get $get) {
                $headers = [
                    TableColumn::make(__('Service')),
                    TableColumn::make(__('Is completed')),
                    TableColumn::make(__('Use credit')),
                    TableColumn::make(__('Spot size')),
                    TableColumn::make(__('Energy')),
                    TableColumn::make(__('Li count')),
                    TableColumn::make(__('Wave length')),
                    TableColumn::make(__('Milliseconds')),
                ];

                return $headers;
            })
            ->schema([
                Select::make('service_id')
                    ->label(__('Service'))
                    ->disabled()
                    ->dehydrated()
                    ->options(fn (Get $get) => Service::query()
                        ->where('category_id', $get('../../category_id'))
                        ->pluck('name', 'id')
                    ),
                Toggle::make('is_completed')
                    ->live(debounce: '1s')
                    ->afterStateUpdated(fn () => $this->calculate())
                    ->inline(false),
                Toggle::make('use_credit')
                    ->live(debounce: '1s')
                    ->helperText(function (Toggle $component, Get $get) {
                        $service = $get('service_id');
                        $record = $component->getParentRepeater()->getRelationship()->getParent();
                        if(is_null($record) || is_null($record->customer)) {
                            return null;
                        }
                        /** @var ServiceCredit $credit */
                        $credits = $record->customer->serviceCredits()
                            ->where('service_id', $service)
                            ->unused()
                            ->count();

                        return __('Customer has :count credits remaining', ['count' => $credits]);
                    })
                    ->disabled(function (Toggle $component, Get $get) {
                        $service = $get('service_id');

                        $record = $component->getParentRepeater()->getRelationship()->getParent();

                        if(is_null($record) || is_null($record->customer)) {
                            return true;
                        }

                        $credits = $record->customer->serviceCredits()
                            ->where('service_id', $service)
                            ->unused()
                            ->count();

                        // Check if the existing service credits are enough
                        if ($credits > 0) {
                            return false;
                        }

                        return true;
                    })
                    ->afterStateUpdated(fn () => $this->updatedPrices())
                    ->inline(false),
                Select::make('spot_size')
                    ->hidden(fn (Get $get) => $this->isExtraHidden(AppointmentExtraType::SpotSize, $get, '../../') || !$this->shouldExtraSplitPerService(AppointmentExtraType::SpotSize, $get, '../../'))
                    ->default(fn (?Appointment $record, Get $get) => $this->getExtraDefaultValue(AppointmentExtraType::SpotSize, $record, $get, '../../'))
                    ->required(fn (Get $get) => $get('is_completed') && $this->isExtraRequired(AppointmentExtraType::SpotSize, $get, '../../'))
                    ->options(fn (Get $get) => $this->getSpotSizeOptions($get, '../../')),
                TextInput::make('energy')
                    ->numeric()
                    ->hidden(fn (Get $get) => $this->isExtraHidden(AppointmentExtraType::Energy, $get, '../../') || !$this->shouldExtraSplitPerService(AppointmentExtraType::Energy, $get, '../../'))
                    ->default(fn (?Appointment $record, Get $get) => $this->getExtraDefaultValue(AppointmentExtraType::Energy, $record, $get, '../../'))
                    ->required(fn (Get $get) => $get('is_completed') && $this->isExtraRequired(AppointmentExtraType::Energy, $get, '../../')),
                TextInput::make('li_count')
                    ->numeric()
                    ->hidden(fn (Get $get) => $this->isExtraHidden(AppointmentExtraType::LiCount, $get, '../../') || !$this->shouldExtraSplitPerService(AppointmentExtraType::LiCount, $get, '../../'))
                    ->default(fn (?Appointment $record, Get $get) => $this->getExtraDefaultValue(AppointmentExtraType::LiCount, $record, $get, '../../'))
                    ->required(fn (Get $get) => $get('is_completed') && $this->isExtraRequired(AppointmentExtraType::LiCount, $get, '../../')),
                TextInput::make('wave_length')
                    ->numeric()
                    ->hidden(fn (Get $get) => $this->isExtraHidden(AppointmentExtraType::WaveLength, $get, '../../') || !$this->shouldExtraSplitPerService(AppointmentExtraType::WaveLength, $get, '../../'))
                    ->default(fn (?Appointment $record, Get $get) => $this->getExtraDefaultValue(AppointmentExtraType::WaveLength, $record, $get, '../../'))
                    ->required(fn (Get $get) => $get('is_completed') && $this->isExtraRequired(AppointmentExtraType::WaveLength, $get, '../../')),
                TextInput::make('milliseconds')
                    ->numeric()
                    ->hidden(fn (Get $get) => $this->isExtraHidden(AppointmentExtraType::Milliseconds, $get, '../../') || !$this->shouldExtraSplitPerService(AppointmentExtraType::Milliseconds, $get, '../../'))
                    ->default(fn (?Appointment $record, Get $get) => $this->getExtraDefaultValue(AppointmentExtraType::Milliseconds, $record, $get, '../../'))
                    ->required(fn (Get $get) => $get('is_completed') && $this->isExtraRequired(AppointmentExtraType::Milliseconds, $get, '../../')),
            ]);
    }

    private function getConsultationFieldset(): Fieldset
    {
        return Fieldset::make(__('Consultation'))
            ->columns(3)
            ->relationship('appointmentConsultation')
            ->hiddenOn('create')
            ->hiddenJS(<<<'JS'
                ! $get('done_at')
            JS)
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Get $get): array {
                $data['customer_id'] = $get('customer_id');

                return $data;
            })
            ->mutateRelationshipDataBeforeSaveUsing(function (array $data, Get $get): array {
                $data['customer_id'] = $get('customer_id');

                return $data;
            })
            ->schema([
                Select::make('status')
                    ->live(onBlur: true)
                    ->partiallyRenderComponentsAfterStateUpdated(['informed_about_risks'])
                    ->options(ConsultationStatus::class)
                    ->required(),
                Toggle::make('informed_about_risks')
                    ->rules(['accepted'], fn (Get $get) => empty($get('status')) || ! ConsultationStatus::from($get('status'))->isFailure())
                    ->inline(false),
                Toggle::make('has_special_risks')
                    ->inline(false),
                TextInput::make('special_risks')
                    ->hiddenJS(<<<'JS'
                        ! $get('has_special_risks')
                    JS),
                Toggle::make('takes_medicine')
                    ->columnStart(1)
                    ->inline(false),
                TextInput::make('medicine')
                    ->hiddenJS(<<<'JS'
                        ! $get('takes_medicine')
                    JS),
                Toggle::make('individual_responsibility_signed')
                    ->rules(['accepted'], fn (Get $get) => empty($get('status')) || ! ConsultationStatus::from($get('status'))->isFailure())
                    ->columnStart(1)
                    ->inline(false),
                Toggle::make('informed_about_consultation_fee')
                    ->rules(['accepted'], fn (Get $get) => empty($get('status')) || ! ConsultationStatus::from($get('status'))->isFailure())
                    ->inline(false),
            ]);
    }

    private function getDoneFieldset(): Fieldset
    {
        return Fieldset::make(__('Done'))
            ->columns(3)
            ->hiddenOn('create')
            ->hiddenJS(<<<'JS'
                ! $get('done_at')
            JS)
            ->schema([
                Select::make('done_by_id')
                    ->relationship('doneBy', 'name', modifyQueryUsing: fn (Builder $query) => $query->provider())
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('next_appointment_in')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Select::make('next_appointment_step')
                    ->options(TimeStep::class)
                    ->default(TimeStep::None)
                    ->required(),
            ]);
    }

    private function getOrderSection(): Section
    {
        return Section::make(__('Order'))
            ->collapsed(fn (string $operation) => $operation == 'create')
            ->icon('heroicon-o-banknotes')
            ->compact()
            ->schema([
                $this->getItemsRepeater(),
                $this->getDiscountsRepeater(),
                TextEntry::make('gross_total')
                    ->hiddenLabel()
                    ->columnSpanFull()
                    ->extraEntryWrapperAttributes(['class' => 'justify-center'])
                    ->state(function (Get $get) {
                        $base = collect($get('items'))->sum('sub_total');
                        $discount = collect($get('discounts'))->sum('amount');
                        $gross = formatMoney($base - $discount);
                        return $gross;
                    }),
                $this->getPaymentsRepeater(),
                TextEntry::make('paid_total')
                    ->columnSpanFull()
                    ->extraEntryWrapperAttributes(['class' => 'justify-center'])
                    ->extraAttributes(function (Get $get) {
                        $base = collect($get('items'))->sum('sub_total');
                        $discount = collect($get('discounts'))->sum('amount');
                        $gross = $base - $discount;
                        $paid = collect($get('payments'))->sum('amount');
                        return ($gross > $paid) ? ['class' => 'text-red-500'] : ['class' => 'text-green-500'];
                    })
                    ->state(fn (Get $get) => formatMoney(collect($get('payments'))->sum('amount'))),
            ]);
    }

    private function getItemsRepeater(): Repeater
    {
        return Repeater::make('items')
        ->relationship('appointmentItems')
        ->collapsed()
        ->columns(4)
        ->defaultItems(0)
        ->itemLabel(fn (array $state): string => sprintf('%sx %s %s', $state['quantity'], $state['description'], formatMoney($state['sub_total'])))
        //->addAction(fn (Action $action) => $action->icon('heroicon-o-plus')->label(__('Item')))
        ->addable(false)
        ->deletable(false)
        ->afterStateUpdated(function (array $state, Get $get, Set $set) {
            $services = collect($state)->where('purchasable_type', Service::class)->pluck('purchasable_id');
            $set('services', $services->toArray());
            $this->calculate();
        })
        ->table([
            Repeater\TableColumn::make(__('Description')),
            Repeater\TableColumn::make(__('Unit price')),
            Repeater\TableColumn::make(__('Quantity'))
                ->markAsRequired(),
            Repeater\TableColumn::make(__('Used credits')),
            Repeater\TableColumn::make(__('Sub total')),
        ])
        ->schema([
            Hidden::make('purchasable_type'),
            Hidden::make('purchasable_id'),
            Hidden::make('type'),
            Hidden::make('note'),
            Hidden::make('discount_total')
                ->default(0)
                ->required(),
            Hidden::make('purchased')
                ->default(1)
                ->required(),
            TextInput::make('description')
                ->required()
                ->disabled()
                ->dehydrated()
                ->maxLength(255),
            TextInput::make('unit_price')
                ->live(onBlur: true)
                ->required()
                ->disabled()
                ->dehydrated()
                ->suffix('€')
                ->numeric()
                ->afterStateUpdated(function (float $state, Get $get, Set $set) {
                    $set('sub_total', ($state * $get('purchased')));
                    $this->updatedPrices();
                }),
            TextInput::make('quantity')
                ->debounce()
                ->default(1)
                ->required()
                ->minValue(1)
                ->numeric()
                ->afterStateUpdated(function (float $state, Get $get, Set $set) {
                    $purchased = $state - $get('used');
                    $set('purchased', $purchased);
                    $set('sub_total', ($purchased * $get('unit_price')));
                    $this->updatedPrices();
                }),
            TextInput::make('used')
                ->live(onBlur: true)
                ->default(0)
                ->required()
                ->numeric()
                ->disabled(function (TextInput $component, Get $get) {
                    if($get('purchasable_type') !== Service::class) {
                        return true;
                    }
                    $service = $get('purchasable_id');
                    $record = $component->getParentRepeater()->getRelationship()->getParent();
                    if(is_null($record) || is_null($record->customer)) {
                        return true;
                    }
                    $credits = $record->customer->serviceCredits()
                        ->where('service_id', $service)
                        ->unused()
                        ->count();

                    // Check if the existing service credits are enough
                    if ($credits == 0) {
                        return true;
                    }

                    return false;
                })
                ->afterStateUpdated(function (float $state, Get $get, Set $set) {
                    $purchased = $get('quantity') - $state;
                    $set('purchased', $purchased);
                    $set('sub_total', ($purchased * $get('unit_price')));
                    $this->updatedPrices();
                }),
            TextInput::make('sub_total')
                ->hiddenLabel(true)
                ->required()
                ->disabled()
                ->dehydrated()
                ->suffix('€')
                ->numeric(),
        ]);
    }

    private function getDiscountsRepeater(): Repeater
    {
        return Repeater::make('discounts')
        ->relationship('discounts')
        ->collapsed()
        ->columns(4)
        ->defaultItems(0)
        ->itemLabel(fn (array $state): string => sprintf('%s %s', $state['description'], formatMoney($state['amount'])))
        ->afterStateUpdated(fn () => $this->updatedPrices())
        ->addAction(fn (Action $action) => $action
            ->icon('heroicon-o-plus')
            ->label(__('Discount'))
        )
        ->table([
            Repeater\TableColumn::make(__('Description')),
            Repeater\TableColumn::make(__('Percentage')),
            Repeater\TableColumn::make(__('Permanent')),
            Repeater\TableColumn::make(__('Amount')),
        ])
        ->schema([
            Hidden::make('source_type'),
            Hidden::make('source_id'),
            Hidden::make('type')
                ->default(DiscountType::Custom->value)
                ->required(),
            TextInput::make('description')
                ->required()
                ->default(DiscountType::Custom->getLabel())
                ->maxLength(255),
            TextInput::make('percentage')
                ->live(onBlur: true)
                ->suffix('%')
                ->default(0)
                ->numeric()
                ->disabled(fn (Get $get) => $get('type') != DiscountType::Custom->value)
                ->dehydrated(),
            Toggle::make('permanent')
                ->default(false)
                ->hidden(fn (Get $get) => $get('type') != DiscountType::Custom->value)
                ->dehydratedWhenHidden()
                ->inline(false),
            TextInput::make('amount')
                ->live(onBlur: true)
                ->suffix('€')
                ->default(0)
                ->required()
                ->numeric()
                ->disabled(fn (Get $get) => $get('type') != DiscountType::Custom->value)
                ->dehydrated(),
        ]);
    }

    private function getOrderOverviewSection(): Section
    {
        return Section::make(__('Order Overview'))
            ->hidden()
            ->saveRelationshipsWhenHidden()
            ->collapsed(fn (string $operation) => $operation == 'create')
            ->compact()
            ->relationship('appointmentOrder')
            ->dehydrateStateUsing(function (array $state) {

            })
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Get $get): array {
                $data['status'] = ($data['paid_total'] >= $data['gross_total']) ? AppointmentOrderStatus::Paid : AppointmentOrderStatus::Open;
                if (! isset($data['net_total'])) {
                    $data['net_total'] = 0;
                }

                return $data;
            })
            ->mutateRelationshipDataBeforeSaveUsing(function (array $data, ?Appointment $appointment, Get $get): array {
                if (! ($appointment?->status?->isCanceled() ?? false)) {
                    $data['status'] = ($data['paid_total'] >= $data['gross_total']) ? AppointmentOrderStatus::Paid : AppointmentOrderStatus::Open;
                }

                return $data;
            })
            ->schema([
                Select::make('status')
                    ->options(AppointmentOrderStatus::class)
                    ->hidden()
                    ->dehydratedWhenHidden()
                    ->required(),
                TextInput::make('base_total')
                    ->suffix('€')
                    ->dehydratedWhenHidden()
                    ->readOnly()
                    ->numeric()
                    ->required(),
                TextInput::make('discount_total')
                    ->suffix('€')
                    ->dehydratedWhenHidden()
                    ->readOnly()
                    ->numeric()
                    ->required(),
                TextInput::make('net_total')
                    ->suffix('€')
                    ->dehydratedWhenHidden()
                    ->readOnly()
                    ->numeric()
                    ->required()
                    ->hidden(),
                TextInput::make('tax_total')
                    ->suffix('€')
                    ->dehydratedWhenHidden()
                    ->readOnly()
                    ->numeric()
                    ->required()
                    ->hidden(),
                TextInput::make('gross_total')
                    ->suffix('€')
                    ->dehydratedWhenHidden()
                    ->readOnly()
                    ->numeric()
                    ->required(),
                TextInput::make('paid_total')
                    ->suffix('€')
                    ->dehydratedWhenHidden()
                    ->readOnly()
                    ->numeric()
                    ->required(),
            ]);
    }

    private function getSidebar(): Grid
    {
        return Grid::make(1)
            //->hiddenOn('create')
            ->columnSpan(1)
            ->schema([
                $this->getDetailsSection(),
                $this->getCustomerSection(),
                $this->getNoteSection(),
                $this->getServiceCreditSection(),
                $this->getHistorySection(),
                $this->getOpenInvoicesSection(),
                $this->getShoppingCartSection(),
            ]);
    }

    private function getDetailsSection(): Section
    {
        return Section::make(__('Details'))
            //->hiddenOn('create')
            ->collapsible()
            ->compact()
            ->icon('heroicon-o-information-circle')
            ->schema([
                View::make('forms.components.appointments.appointment-details')
                    ->hiddenOn('create'),
                CustomerDetails::make('customer_details')
                    ->customer(fn (Get $get) => $get('customer_id')),
                /*View::make('forms.components.customer-details')
                    ->hidden(fn (?Appointment $record) => is_null($record?->customer)),*/
            ]);
    }

    private function getCustomerSection()
    {
        return Grid::make(1)
            ->relationship('customer')
            ->hiddenOn('create')
            ->hiddenJS(<<<'JS'
                ! $get('customer_id')
            JS)
            ->schema([
                $this->getOptionsSection(),
                $this->getCustomerContactSection(),
            ]);
    }

    private function getOptionsSection()
    {
        return Section::make(__('Customer options'))
            ->icon('heroicon-o-cog-6-tooth')
            ->collapsed()
            ->compact()
            ->schema([
                Select::make('preferedProviders')
                    ->multiple()
                    ->relationship('preferedProviders', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('prefered_contact_method')
                    ->options(ContactMethod::class),
                CheckboxList::make('options')
                    ->options(CustomerOption::class),
            ]);
    }

    private function getNoteSection(): Section
    {
        return Section::make(__('Notes'))
            ->collapsible()
            ->compact()
            ->icon('heroicon-o-pencil-square')
            ->schema([
                Repeater::make('customerNotes')
                    ->relationship('customerNotes', fn (Builder $query) => $query->where('is_important', true))
                    ->collapsible()
                    ->addActionLabel(__('Add note'))
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Get $get): array {
                        $data['customer_id'] = $get('customer_id');
                        $data['user_id'] = auth()->id();

                        return $data;
                    })
                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data, Get $get): array {
                        $data['customer_id'] = $get('customer_id');

                        return $data;
                    })
                    ->extraAttributes(['class' => 'max-w-96'])
                    ->columns(2)
                    ->schema([
                        Toggle::make('edit')
                            ->inline(false)
                            ->default(true),
                        Toggle::make('is_important')
                            ->inline(false)
                            ->default(true),
                        Textarea::make('content')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->visibleJs(<<<'JS'
                                $get('edit')
                            JS),
                        TextEntry::make('text')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->color(fn (Get $get) => $get('is_important') ? 'danger' : null)
                            ->state(fn (Get $get) => $get('content'))
                            ->hiddenJS(<<<'JS'
                                $get('edit')
                            JS),
                        TextEntry::make('created_at')
                            ->hiddenLabel()
                            ->date(getDateFormat()),
                        TextEntry::make('user.name')
                            ->hiddenLabel(),
                    ]),
                Repeater::make('notes')
                    ->relationship('notes', fn (Builder $query) => $query->where('is_important', false))
                    ->collapsible()
                    ->addActionLabel(__('Add note'))
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Get $get): array {
                        $data['customer_id'] = $get('customer_id');
                        $data['user_id'] = auth()->id();

                        return $data;
                    })
                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data, Get $get): array {
                        $data['customer_id'] = $get('customer_id');

                        return $data;
                    })
                    ->columns(2)
                    ->schema([
                        Toggle::make('edit')
                            ->inline(false)
                            ->default(true),
                        Toggle::make('is_important')
                            ->inline(false)
                            ->default(false),
                        Textarea::make('content')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->visibleJs(<<<'JS'
                                $get('edit')
                            JS),
                        TextEntry::make('text')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->color(fn (Get $get) => $get('is_important') ? 'danger' : null)
                            ->state(fn (Get $get) => $get('content'))
                            ->hiddenJS(<<<'JS'
                                $get('edit')
                            JS),
                        TextEntry::make('created_at')
                            ->hiddenLabel()
                            ->date(getDateFormat()),
                        TextEntry::make('user.name')
                            ->hiddenLabel(),
                    ]),
            ]);
    }

    private function getCustomerContactSection(): Section
    {
        return Section::make(__('Contact'))
            ->compact()
            ->collapsible()
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->schema([
                Actions::make([
                    ContactCustomer::make(),
                ]),
                NoteRepeater::make('customerContacts')
                    ->relationship('customerContacts', fn (Builder $query) => $query->latest()->limit(3))
                    ->collapsed()
                    ->itemLabel(fn (array $state): ?string => $state['message'])
                    ->truncateItemLabel(false)
                    ->itemDate(fn (array $state): ?string => $state['created_at'] ?? null)
                    ->addable(false)
                    ->deletable(false)
                    ->extraAttributes(['class' => 'max-w-96'])
                    ->schema([
                        TextInput::make('title')
                            ->disabled(),
                        Textarea::make('message')
                            ->disabled(),
                    ]),
            ]);
    }

    private function getHistorySection(): Section
    {
        return Section::make(__('History'))
            ->icon('heroicon-o-calendar-days')
            ->compact()
            ->collapsible()
            ->hiddenOn('create')
            ->schema([
                View::make('forms.components.appointments.appointment-history'),
            ]);
    }

    private function getOpenInvoicesSection(): Section
    {
        return Section::make(__('Invoices'))
            ->icon('heroicon-o-document-text')
            ->compact()
            ->collapsible()
            ->hiddenOn('create')
            ->schema([
                View::make('forms.components.appointments.appointment-open-invoices'),
            ]);
    }

    private function getShoppingCartSection(): Section
    {
        return Section::make(__('Cart'))
            ->collapsible()
            ->icon('heroicon-o-shopping-cart')
            ->compact()
            ->schema([
                Placeholder::make('cartItems')
                    ->label('')
                    ->content(function (Get $get) {
                        $items = collect($get('items'));

                        return view('forms.components.appointments.appointment-cart', ['items' => $items]);
                    }),
            ]);
    }
}
