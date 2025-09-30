<?php

namespace App\Filament\Crm\Resources\CustomerCredits\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Models\Payment;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('amount')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                TextColumn::make('payable')
                    ->url(fn (Payment $record) => $record->getPayableUrl())
                    ->formatStateUsing(fn (Payment $record) => $record->getPayableTitle()),
                TextColumn::make('type'),
                TextColumn::make('amount')
                    ->money('Eur', 0, 'de'),
            ])
            ->filters([
            ])
            ->headerActions([
                //Tables\Actions\CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (Payment $record) => $record->getPayableUrl()),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
