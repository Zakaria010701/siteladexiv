<?php

namespace App\Filament\Admin\Resources\Users\Schemas\Components;

use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class UserEmailFieldset {

    public static function make(): Fieldset
    {
        return Fieldset::make(__('Email'))
            ->columnSpan(1)
            ->columns(1)
            ->columnStart(1)
            ->schema([
                TextInput::make('email')
                    ->label(__('Primary Email'))
                    ->autocomplete(false)
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Repeater::make('emailAddresses')
                    ->label(__('Extra Emails'))
                    ->relationship('emailAddresses')
                    ->defaultItems(0)
                    ->extraItemActions([
                        Action::make('makePrimary')
                            ->icon('heroicon-m-star')
                            ->color('primary')
                            ->action(function (array $arguments, Repeater $component, Get $get, Set $set) {
                                $primary = $get('email');
                                $state = $component->getState();
                                $set('email', $state[$arguments['item']]['email']);
                                $state[$arguments['item']]['email'] = $primary;
                                $component->state($state);
                            }),
                    ])
                    ->table([
                        TableColumn::make(__('Email')),
                    ])
                    ->schema([
                        TextInput::make('email')
                            ->required()
                            ->email()
                            ->autocomplete('extra-email')
                            ->maxLength(255),
                    ]),
                ]);
    }
}
