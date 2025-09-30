<?php

namespace App\Filament\Crm\Resources\Customers\Schemas;

use App\Enums\Customers\ContactMethod;
use App\Enums\Customers\CustomerOption;
use App\Enums\Gender;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use App\Filament\Crm\Resources\Customers\Schemas\Components\CustomerAccountsRepeater;
use App\Filament\Crm\Resources\Customers\Schemas\Components\CustomerAddressRepeater;
use App\Filament\Crm\Resources\Customers\Schemas\Components\CustomerContactSection;
use App\Filament\Crm\Resources\Customers\Schemas\Components\CustomerEmailFieldset;
use App\Filament\Crm\Resources\Customers\Schemas\Components\CustomerMediaSection;
use App\Filament\Crm\Resources\Customers\Schemas\Components\CustomerNameFieldset;
use App\Filament\Crm\Resources\Customers\Schemas\Components\CustomerNotesSection;
use App\Filament\Crm\Resources\Customers\Schemas\Components\CustomerPhoneNumberFieldset;
use App\Models\Customer;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompactCustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                CustomerNameFieldset::make(),
                CustomerEmailFieldset::make(),
                CustomerPhoneNumberFieldset::make(),
                DatePicker::make('birthday')
                    ->format(getDateFormat()),
                Select::make('preferedProviders')
                    ->multiple()
                    ->relationship('preferedProviders', 'name')
                    ->searchable()
                    ->preload(),
                CustomerAddressRepeater::make()
                    ->columnSpanFull(),
                CustomerAccountsRepeater::make(),
                CustomerMediaSection::make(),
                Select::make('parent_id')
                    ->label(__('Parent Customer'))
                    ->relationship('parent', 'lastname')
                    ->getOptionLabelFromRecordUsing(fn (Customer $record) => $record->full_name)
                    ->searchable(['firstname', 'lastname']),
                CheckboxList::make('options')
                    ->options(CustomerOption::class),
                Select::make('prefered_contact_method')
                    ->options(ContactMethod::class),
                Section::make(__('Meta'))
                    ->collapsed()
                    ->compact()
                    ->columnSpan(1)
                    ->visible(CustomerResource::can('meta'))
                    ->schema([
                        KeyValue::make('meta'),
                    ]),
            ]);
    }
}
