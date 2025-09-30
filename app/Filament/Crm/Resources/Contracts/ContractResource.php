<?php

namespace App\Filament\Crm\Resources\Contracts;

use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ExportAction;
use App\Filament\Crm\Resources\Contracts\RelationManagers\CreditsRelationManager;
use App\Filament\Crm\Resources\Contracts\RelationManagers\VerificationsRelationManager;
use App\Filament\Crm\Resources\Contracts\Pages\ListContracts;
use App\Filament\Crm\Resources\Contracts\Pages\CreateContract;
use App\Filament\Crm\Resources\Contracts\Pages\EditContract;
use App\Enums\Contracts\ContractType;
use App\Enums\Transactions\DiscountType;
use App\Enums\Transactions\PaymentType;
use App\Enums\Verifications\VerificationStatus;
use App\Filament\Actions\Table\NeedsVerificationAction;
use App\Filament\Actions\Table\VerifyAction;
use App\Filament\Crm\Resources\ContractResource\Pages;
use App\Filament\Crm\Resources\ContractResource\RelationManagers;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use App\Filament\Crm\Resources\Customers\Forms\CustomerForm;
use App\Filament\Schemas\Components\CustomerSelect;
use App\Forms\Components\TableRepeater;
use App\Forms\Components\TableRepeater\Header;
use App\Models\Appointment;
use App\Models\Category;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\DiscountTemplate;
use App\Models\Service;
use App\Support\Calculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-currency-euro';

    public static function getNavigationGroup(): ?string
    {
        return __('Accounts');
    }

    public static function getModelLabel(): string
    {
        return __('Contract');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Contracts');
    }

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        /** @phpstan-ignore-next-line */
        return $record->title;
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        /** @phpstan-ignore-next-line */
        return $record->title;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'date',
            'customer.firstname',
            'customer.lastname',
            'customer.email',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('Customer') => $record->customer?->full_name,
            __('Services') => $record->services->pluck('short_code')->unique()->implode(', '),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                CustomerSelect::make()
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                DatePicker::make('date')
                    ->required(),
                Select::make('type')
                    ->required()
                    ->columnStart(1)
                    ->options(ContractType::class),
                TextInput::make('treatment_count')
                    ->label(__('Purchased treatments'))
                    ->live(onBlur: true)
                    ->required()
                    ->numeric()
                    ->afterStateUpdated(function (int $state,Get $get, Set $set) {
                        $set('treatments', $state-1);
                        self::updatedTreatmentCount($get, $set);
                    }),
                TextInput::make('treatments')
                    ->live(onBlur: true)
                    ->numeric()
                    ->visible(fn (Get $get) => $get('credit_last_appointment'))
                    ->afterStateUpdated(function (int $state,Get $get, Set $set) {
                        $set('treatment_count', $state+1);
                        self::updatedTreatmentCount($get, $set);
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
                            'services' => $get('services'),
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
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updatedForm($get, $set, '../../')),
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
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updatedForm($get, $set))
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
                Select::make('services')
                    ->options(fn () => Service::query()->pluck('name', 'id'))
                    ->preload()
                    ->live()
                    ->required()
                    ->multiple()
                    ->searchable()
                    ->columnSpan(3)
                    ->formatStateUsing(function (?Contract $record) {
                        return isset($record) ? $record->contractServices->pluck('service_id')->toArray() : null;
                    })
                    ->afterStateUpdated(function (array $state, Get $get, Set $set) {
                        $services = collect($get('contractServices'))
                            ->reject(fn (array $service) => ! in_array($service['service_id'], $state));

                        $newServices = array_diff($state, $services->pluck('service_id')->toArray());

                        $add = Service::query()
                            ->whereIn('id', $newServices)
                            ->get()
                            ->map(fn (Service $service) => [
                                'service_id' => $service->id,
                                'default_price' => $service->price,
                                'price' => $service->price,
                            ]);

                        $services = $services->merge($add);
                        $set('contractServices', $services->toArray());
                        $set('price', $services->sum('price') * $get('treatment_count'));
                    }),
                Repeater::make('contractServices')
                    ->label(__('Services'))
                    ->required()
                    ->relationship('contractServices')
                    ->hidden()
                    ->dehydratedWhenHidden()
                    ->columnSpanFull()
                    ->columns(3)
                    ->addable(false)
                    ->deletable(false)
                    ->schema([
                        Select::make('service_id')
                            ->required()
                            ->relationship('service', 'name')
                            ->disabled()
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
                                $set('../../price', collect($get('../../contractServices'))->sum('price') * $get('../../treatment_count'));
                            }),
                    ]),
                Textarea::make('description')
                    ->columnSpanFull(),
                KeyValue::make('meta')
                    ->columnSpanFull()
                    ->hidden(fn () => auth()->user()->cannot('admin', Contract::class))
                    ->disabled(),
            ]);
    }

    private static function updatedTreatmentCount(Get $get, Set $set): void
    {
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
        self::updatedForm($get, $set);
    }

    private static function updatedForm(Get $get, Set $set, string $path = ''): void
    {
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('deleted_at')
                    ->dateTime(getDateTimeFormat())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime(getDateTimeFormat())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime(getDateTimeFormat())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('customer.full_name')
                    ->numeric()
                    ->sortable()
                    ->url(fn (Contract $record): string => isset($record->customer)
                        ? CustomerResource::getUrl('edit', ['record' => $record->customer])
                        : null)
                    ->searchable(['firstname', 'lastname'], isIndividual: true),
                TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('date')
                    ->date(getDateFormat())
                    ->sortable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('contractServices.badge')
                    ->label(__('Services'))
                    ->badge(),
                TextColumn::make('treatment_count')
                    ->sortable()
                    ->toggledHiddenByDefault(),
                IconColumn::make('used')
                    ->boolean()
                    ->state(fn (Contract $record): bool => $record->isUsed()),
                TextColumn::make('default_price')
                    ->money('eur', locale: 'de')
                    ->sortable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('discount_percentage')
                    ->formatStateUsing(fn ($state) => sprintf("%s%%", $state))
                    ->sortable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('price')
                    ->money('eur', locale: 'de')
                    ->sortable()
                    ->summarize(Sum::make()->formatStateUsing(fn ($state) => formatMoney($state))),
                TextColumn::make('price')
                    ->money('eur', locale: 'de')
                    ->sortable()
                    ->summarize(Sum::make()->formatStateUsing(fn ($state) => formatMoney($state))),
                IconColumn::make('paid')
                    ->boolean()
                    ->state(fn (Contract $record): bool => $record->isPaid()),
                TextColumn::make('appointment.start')
                    ->numeric()
                    ->placeholder(__('No Appointment'))
                    ->url(fn (Contract $record) => ! is_null($record->appointment)
                        ? AppointmentResource::getUrl('edit', ['record' => $record->appointment])
                        : null)
                    ->dateTime(getDateTimeFormat())
                    ->sortable(),
                IconColumn::make('credited_appointment_id')
                    ->label(__('Credit last appointment'))
                    ->toggledHiddenByDefault()
                    ->boolean()
                    ->state(fn (Contract $record): bool => !is_null($record->credited_appointment_id)),
                IconColumn::make('verified')
                    ->state(fn (Contract $record): VerificationStatus => $record->verificationStatus())
                    ->tooltip(fn (Contract $record) => $record->currentVerification?->created_at?->format(getDateTimeFormat())),
            ])
            ->defaultSort('date', 'desc')
            ->defaultGroup('date')
            ->groups([
                Group::make('type')
                    ->label(__('Type')),
                Group::make('customer_id')
                    ->label(__('Customer'))
                    ->getTitleFromRecordUsing(fn (Contract $record): string => $record->customer?->full_name ?? __('Universal'))
                    ->titlePrefixedWithLabel(false),
                Group::make('user_id')
                    ->label(__('User'))
                    ->getTitleFromRecordUsing(fn (Contract $record): string => $record->user?->name ?? __('No User'))
                    ->titlePrefixedWithLabel(false),
                Group::make('date')
                    ->label(__('Date'))
                    ->getTitleFromRecordUsing(fn (Contract $record): string => formatDate($record->date))
                    ->titlePrefixedWithLabel(false),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(ContractType::class),
                SelectFilter::make('services')
                    ->relationship('services', 'name')
                    ->multiple(),
                TernaryFilter::make('paid')
                    ->nullable()
                    ->queries(
                        true: fn (Builder $query) => $query->paid(),
                        false: fn (Builder $query) => $query->notPaid(),
                        blank: fn (Builder $query) => $query,
                    ),
                TernaryFilter::make('used')
                    ->nullable()
                    ->queries(
                        true: fn (Builder $query) => $query->used(),
                        false: fn (Builder $query) => $query->unused(),
                        blank: fn (Builder $query) => $query,
                    ),
                TernaryFilter::make('verified')
                    ->nullable()
                    ->queries(
                        true: fn (Builder $query) => $query->verified(),
                        false: fn (Builder $query) => $query->notVerified(),
                        blank: fn (Builder $query) => $query,
                    ),
                Filter::make('date')
                    ->schema([
                        DatePicker::make('date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (isset($data['date'])) {
                            return formatDate($data['date']);
                        }

                        return null;
                    }),
                TrashedFilter::make(),
                QueryBuilder::make()
                    ->constraints([
                        DateConstraint::make('date')
                            ->label(__('Date')),
                        DateConstraint::make('created_at')
                            ->label(__('Created at')),
                        DateConstraint::make('updated_at')
                            ->label(__('Updated at')),
                        SelectConstraint::make('type')
                            ->label(__('Type'))
                            ->options(ContractType::class),
                        NumberConstraint::make('price')
                            ->label(__('Price')),
                        NumberConstraint::make('treatment_count')
                            ->label(__('Treatment count')),
                    ]),
                ], layout: FiltersLayout::AboveContentCollapsible)
            ->recordActions([
                EditAction::make(),
                ActionGroup::make([
                    Action::make('Download')
                        ->icon('heroicon-m-arrow-down-tray')
                        ->action(fn (Contract $record) => response()->streamDownload(function () use ($record) {
                            echo Pdf::loadView('pdf.contract', ['contract' => $record])->stream();
                        }, 'contract.pdf')),
                    VerifyAction::make(),
                    NeedsVerificationAction::make(),
                    DeleteAction::make(),
                    ForceDeleteAction::make(),
                    RestoreAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ExportAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CreditsRelationManager::class,
            VerificationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContracts::route('/'),
            'create' => CreateContract::route('/create'),
            'edit' => EditContract::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
