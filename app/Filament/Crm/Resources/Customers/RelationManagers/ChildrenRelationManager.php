<?php

namespace App\Filament\Crm\Resources\Customers\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DissociateAction;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use App\Models\Customer;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('lastname')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('lastname')
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('gender')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('firstname')
                    ->searchable(),
                TextColumn::make('lastname')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                PhoneColumn::make('phone_number')
                    ->displayFormat(PhoneInputNumberType::INTERNATIONAL),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (Customer $record) => CustomerResource::getUrl('edit', ['record' => $record])),
                DissociateAction::make()
                    ->using(function (Customer $record) {
                        $record->parent()->dissociate();
                        $record->save();
                    }),
            ]);
    }
}
