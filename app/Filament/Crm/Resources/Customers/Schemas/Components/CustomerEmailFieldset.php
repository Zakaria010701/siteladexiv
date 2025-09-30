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

class CustomerEmailFieldset
{
    public static function make(): Fieldset
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
                Repeater::make('emailAddresses')
                    ->label(__('Extra emails'))
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
                        TableColumn::make(__('Contact')),
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
                        Toggle::make('is_contact'),
                    ]),
                ]);
    }
}
