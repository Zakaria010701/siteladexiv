<?php

namespace App\Filament\Crm\Resources\Invoices\Schemas\Components;

use App\Forms\Components\TableRepeater\Header;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Support\Calculator;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Contracts\Database\Eloquent\Builder;

class InvoiceItemsRepeater
{
    public static function make(): Repeater
    {
        return Repeater::make('items')
            ->relationship('items')
            ->minItems(1)
            ->itemLabel(fn (array $state) => $state['title'])
            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data) => self::mutateItemData($data))
            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data) => self::mutateItemData($data))
            ->table([
                Repeater\TableColumn::make(__('Title'))
                    ->markAsRequired(),
                Repeater\TableColumn::make(__('Description')),
                Repeater\TableColumn::make(__('Quantity')),
                Repeater\TableColumn::make(__('Unit')),
                Repeater\TableColumn::make(__('Unit price'))
                    ->markAsRequired(),
                Repeater\TableColumn::make(__('Tax percentage'))
                    ->markAsRequired(),
                Repeater\TableColumn::make(__('Sub total'))
                    ->markAsRequired(),
            ])
            ->schema([
                Hidden::make('invoicable_type'),
                Hidden::make('invoicable_id'),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('description')
                    ->maxLength(255),
                TextInput::make('quantity')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updatedItem($get, $set))
                    ->default(1)
                    ->numeric(),
                TextInput::make('unit')
                    ->maxLength(255),
                TextInput::make('unit_price')
                    ->required()
                    ->live(onBlur: true)
                    ->suffix('€')
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updatedItem($get, $set))
                    ->numeric(),
                TextInput::make('tax_percentage')
                    ->label(__('Tax'))
                    ->required()
                    ->default(invoice()->default_tax)
                    ->suffix('%')
                    ->numeric(),
                TextInput::make('sub_total')
                    ->required()
                    ->readOnly()
                    ->suffix('€')
                    ->numeric(),
            ]);
    }

    private static function mutateItemData(array $data): array
    {
        $quantity = $data['quantity'];
        $unit_price = $data['unit_price'];
        $sub_total = $quantity * $unit_price;
        $data['sub_total'] = $sub_total;
        $tax = Calculator::getTaxAmmount($sub_total, $data['tax_percentage'], 2, false);
        $data['tax'] = $tax;

        return $data;
    }

    private static function updatedItem(Get $get, Set $set): void
    {
        $quantity = $get('quantity');
        $unit_price = $get('unit_price');
        $sub_total = $quantity * $unit_price;
        $set('sub_total', $sub_total);
    }
}