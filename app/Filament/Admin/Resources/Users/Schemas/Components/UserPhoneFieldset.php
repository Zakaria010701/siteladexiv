<?php

namespace App\Filament\Admin\Resources\Users\Schemas\Components;

use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class UserPhoneFieldset {

    public static function make(): Fieldset
    {
        return Fieldset::make(__('Phone Number'))
            ->columnSpan(1)
            ->columns(1)
            ->schema([
                PhoneInput::make('phone_number')
                    ->label('Primary Number')
                    ->displayNumberFormat(PhoneInputNumberType::INTERNATIONAL)
                    ->defaultCountry('DE'),
                Repeater::make('phoneNumbers')
                    ->label(__('Extra Numbers'))
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
                        TableColumn::make(__('Phone Number')),
                    ])
                    ->schema([
                        PhoneInput::make('phone_number')
                            ->displayNumberFormat(PhoneInputNumberType::INTERNATIONAL)
                            ->defaultCountry('DE'),
                    ]),
                ]);
    }
}
