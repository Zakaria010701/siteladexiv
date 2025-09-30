<?php

namespace App\Filament\Crm\Resources\Transactions\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Crm\Resources\Transactions\TransactionResource;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('description')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('type')
                    ->sortable()
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('account.name')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.lastname')
                    ->url(fn (Transaction $record): string => isset($record->customer) ? CustomerResource::getUrl('edit', ['record' => $record->customer]) : '')
                    ->formatStateUsing(fn (Transaction $record) => $record->customer?->full_name)
                    ->sortable()
                    ->searchable(['firstname', 'lastname']),
                TextColumn::make('account.iban')
                    ->label(__('Iban'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('bank.name')
                    ->sortable(),
                TextColumn::make('status')
                    ->sortable()
                    ->badge(),
                TextColumn::make('bookable')
                    ->url(fn (Transaction $record) => $record->getBookableUrl())
                    ->formatStateUsing(fn (Transaction $record) => $record->getBookableTitle()),
                TextColumn::make('date')
                    ->dateTime(getDateFormat())
                    ->sortable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => formatMoney($state))
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable()
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (Transaction $record) => TransactionResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
