<?php

namespace App\Filament\Crm\Resources\Customers\Forms;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Actions\Action;
use Filament\Schemas\Components\Actions;
use App\Enums\Customers\ContactMethod;
use App\Enums\Customers\CustomerOption;
use App\Enums\Gender;
use App\Filament\Actions\Appointments\ContactCustomer;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use App\Forms\Components\NoteRepeater;
use App\Models\Customer;
use App\Models\EmailAddress;
use App\Forms\Components\TableRepeater;
use App\Forms\Components\TableRepeater\Header;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class CustomerForm
{
    public function __construct(
        private readonly Schema $form,
    ) {}

    public static function make(Schema $schema): Schema
    {
        /** @var CustomerForm $static */
        $static = app(self::class, ['form' => $schema]);

        return $static->schema();
    }

    private function schema(): Schema
    {
        return $this->form->components([
            Flex::make([
                $this->getBody(),
                $this->getSidebar(),
            ])
                ->columnSpanFull()
                ->from('md'),
        ]);
    }

    public static function compact(Schema $schema): Schema
    {
        /** @var CustomerForm $static */
        $static = app(self::class, ['form' => $schema]);

        return $static->compactSchema();
    }

    private function compactSchema(): Schema
    {
        return $this->form->components([
            $this->getBody(),
        ]);
    }

    private function getBody(): Grid
    {
        return Grid::make(1)
            ->schema([
                Section::make(__('Customer'))
                    ->columns(2)
                    ->schema([
                        $this->getNameFieldset(),
                        $this->getEmailFieldset(),
                        $this->getPhoneNumberFieldset(),
                        DatePicker::make('birthday')
                            ->format(getDateFormat()),
                        Select::make('preferedProviders')
                            ->multiple()
                            ->relationship('preferedProviders', 'name')
                            ->searchable()
                            ->preload(),
                        $this->getAddressFieldset(),
                        $this->getMediaSection(),
                        $this->getAccountsFieldset(),
                        Select::make('parent_id')
                            ->label(__('Parent Customer'))
                            ->relationship('parent', 'lastname')
                            ->getOptionLabelFromRecordUsing(fn (Customer $record) => $record->full_name)
                            ->searchable(['firstname', 'lastname']),
                        CheckboxList::make('options')
                            ->options(CustomerOption::class),
                        Select::make('prefered_contact_method')
                            ->options(ContactMethod::class),
                        KeyValue::make('meta')
                            ->visible(auth()->user()->can('meta_customer')),
                    ]),
            ]);
    }

    private function getNameFieldset(): Fieldset
    {
        return Fieldset::make(__('Name'))
            ->schema([
                TextInput::make('title')
                    ->maxLength(255),
                Select::make('gender')
                    ->required()
                    ->options(Gender::class),
                TextInput::make('firstname')
                    ->required()
                    ->maxLength(255),
                TextInput::make('lastname')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    private function getEmailFieldset(): Fieldset
    {
        return Fieldset::make(__('Email'))
            ->columnSpan(1)
            ->columns(1)
            ->schema([
                TextInput::make('email')
                    ->label(__('Primary email'))
                    ->disabled(fn (Get $get) => $get('no_primary_email'))
                    ->dehydrated()
                    ->required(fn (Get $get) => ! $get('no_primary_email'))
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Toggle::make('no_primary_email')
                    ->live()
                    ->formatStateUsing(fn (string $operation, Get $get): bool => $operation != 'create' && is_null($get('email')))
                    ->afterStateUpdated(fn (bool $state, Set $set) => $set('email', null)),
                TableRepeater::make('emailAddresses')
                    ->label(__('Extra emails'))
                    ->relationship('emailAddresses')
                    ->defaultItems(0)
                    ->extraItemActions([
                        Action::make('makePrimary')
                            ->icon('heroicon-m-star')
                            ->color('primary')
                            ->action(function (array $arguments, TableRepeater $component, Get $get, Set $set) {
                                $primary = $get('email');
                                $state = $component->getState();
                                $set('email', $state[$arguments['item']]['email']);
                                $state[$arguments['item']]['email'] = $primary;
                                $component->state($state);
                            }),
                    ])
                    ->headers([
                        Header::make(__('Email')),
                        Header::make(__('Contact')),
                    ])
                    ->schema([
                        TextInput::make('email')
                            ->required()
                            ->email()
                            ->autocomplete('extra-email')
                            ->helperText(function (?string $state, ?EmailAddress $record) {
                                if (is_null($record)) {
                                    return null;
                                }
                                $customer = $record->customer;
                                if (is_null($customer)) {
                                    return null;
                                }

                                $url = CustomerResource::getUrl('edit', ['record' => $customer]);
                                $name = $customer->full_name;
                                $link = "<a class=\"underline\" href=\"$url\">$name</a>";
                                $text = __('Email is used by :customer', ['customer' => $link]);

                                return new HtmlString("<span class=\"text-warning-600\">$text</span>");
                            })
                            ->maxLength(255),
                        Checkbox::make('is_contact'),
                    ]),
            ]);
    }

    private function getPhoneNumberFieldset(): Fieldset
    {
        return Fieldset::make(__('Phone number'))
            ->columnSpan(1)
            ->columns(1)
            ->schema([
                PhoneInput::make('phone_number')
                    ->label('Primary number')
                    ->displayNumberFormat(PhoneInputNumberType::INTERNATIONAL)
                    ->defaultCountry('DE')
                    ->dehydrated()
                    ->disabled(fn (Get $get) => $get('no_primary_phone_number'))
                    ->required(fn (Get $get) => ! $get('no_primary_phone_number')),
                Toggle::make('no_primary_phone_number')
                    ->live()
                    ->formatStateUsing(fn (string $operation, Get $get): bool => $operation != 'create' && is_null($get('phone_number')))
                    ->afterStateUpdated(fn (bool $state, Set $set) => $set('phone_number', null)),
                TableRepeater::make('phoneNumbers')
                    ->label(__('Extra numbers'))
                    ->relationship('phoneNumbers')
                    ->defaultItems(0)
                    ->extraItemActions([
                        Action::make('makePrimary')
                            ->icon('heroicon-m-star')
                            ->color('primary')
                            ->action(function (array $arguments, TableRepeater $component, Get $get, Set $set) {
                                $primary = $get('phone_number');
                                $state = $component->getState();
                                $set('phone_number', $state[$arguments['item']]['phone_number']);
                                $state[$arguments['item']]['phone_number'] = $primary;
                                $component->state($state);
                            }),
                    ])
                    ->headers([
                        Header::make(__('Phone number')),
                        Header::make(__('Contact')),
                    ])
                    ->schema([
                        PhoneInput::make('phone_number')
                            ->displayNumberFormat(PhoneInputNumberType::INTERNATIONAL)
                            ->defaultCountry('DE'),
                        Checkbox::make('is_contact'),
                    ]),
            ]);
    }

    private function getAddressFieldset(): Fieldset
    {
        return Fieldset::make(__('Address'))
            ->columns(1)
            ->schema([
                TableRepeater::make('address')
                    ->label('')
                    ->relationship('addresses')
                    ->defaultItems(0)
                    ->headers([
                        Header::make(__('Location')),
                        Header::make(__('Zip code')),
                        Header::make(__('Address')),
                    ])
                    ->schema([
                        TextInput::make('location'),
                        TextInput::make('postcode'),
                        TextInput::make('address'),
                    ]),
            ]);
    }

    private function getAccountsFieldset(): Fieldset
    {
        return Fieldset::make(__('Accounts'))
            ->columns(1)
            ->columnSpan(1)
            ->schema([
                TableRepeater::make('accounts')
                    ->label('')
                    ->relationship('accounts')
                    ->headers([
                        Header::make(__('Name')),
                        Header::make(__('Iban')),
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('iban')
                            ->required()
                            ->maxLength(34)
                            ->unique(ignoreRecord: true),
                    ]),
            ]);
    }

    private function getMediaSection()
    {
        return Section::make(__('Media'))
            ->columnSpan(1)
            ->compact()
            ->collapsed(true)
            ->schema([
                SpatieMediaLibraryFileUpload::make('media')
                    ->disk('public')
                    ->hiddenOn('create')
                    ->downloadable(true)
                    ->multiple(),
            ]);
    }

    private function getSidebar(): Grid
    {
        return Grid::make(1)
            ->grow(false)
            ->schema([
                Section::make(__('Notes'))
                    ->collapsible()
                    ->compact()
                    ->schema([
                        NoteRepeater::make('notes')
                            ->relationship('notes', fn (Builder $query) => $query
                                ->where(fn (Builder $query) => $query
                                    ->where(fn (Builder $query) => $query->whereNull('notable_type'))
                                    ->orWhere('is_important', true)
                                ))
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['content'])
                            ->truncateItemLabel(false)
                            ->defaultItems(0)
                            ->itemDate(fn (array $state): ?string => $state['created_at'] ?? null)
                            ->color(fn (array $state): ?string => $state['is_important'] ? 'danger' : null)
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, Get $get): array {
                                $data['user_id'] = auth()->id();

                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data, Get $get): array {
                                return $data;
                            })
                            ->extraAttributes(['class' => 'max-w-96'])
                            ->schema([
                                Textarea::make('content'),
                                Toggle::make('is_important'),
                            ]),
                    ]),
                Section::make(__('Contact'))
                    ->compact()
                    ->collapsible()
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
                    ]),
            ]);
    }
}
