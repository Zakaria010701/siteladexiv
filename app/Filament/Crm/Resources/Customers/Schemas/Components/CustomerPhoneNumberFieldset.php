<?php

namespace App\Filament\Crm\Resources\Customers\Schemas\Components;

use App\Enums\Gender;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use App\Models\EmailAddress;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\HtmlString;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class CustomerPhoneNumberFieldset
{
    public static function make(): Fieldset
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
                Repeater::make('phoneNumbers')
                    ->label(__('Extra numbers'))
                    ->relationship('phoneNumbers')
                    ->defaultItems(0)
                    ->extraItemActions([
                        Action::make('makePrimary')
                            ->icon('heroicon-m-star')
                            ->color('primary')
                            ->action(function (array $arguments, Repeater $component, Get $get, Set $set) {
                                $primary = $get('phone_number');
                                $state = $component->getState();
                                $set('phone_number', $state[$arguments['item']]['phone_number']);
                                $state[$arguments['item']]['phone_number'] = $primary;
                                $component->state($state);
                            }),
                    ])
                    ->table([
                        TableColumn::make(__('Phone number')),
                        TableColumn::make(__('Contact')),
                    ])
                    ->schema([
                        PhoneInput::make('phone_number')
                            ->displayNumberFormat(PhoneInputNumberType::INTERNATIONAL)
                            ->defaultCountry('DE'),
                        Checkbox::make('is_contact'),
                    ]),
            ]);
    }
}
