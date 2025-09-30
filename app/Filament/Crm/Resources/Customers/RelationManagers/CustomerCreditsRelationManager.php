<?php

namespace App\Filament\Crm\Resources\Customers\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Crm\Resources\CustomerCredits\CustomerCreditResource;
use App\Models\CustomerCredit;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerCreditsRelationManager extends RelationManager
{
    protected static string $relationship = 'customerCredits';

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
                TextColumn::make('amount')
                    ->numeric()
                    ->money('Eur', 0, 'de')
                    ->sortable(),
                TextColumn::make('open_amount')
                    ->numeric()
                    ->money('Eur', 0, 'de'),
                TextColumn::make('description')
                    ->wrap(),
                TextColumn::make('spent_at')
                    ->dateTime(getDateTimeFormat())
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('spent_at')
                    ->nullable(),
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (CustomerCredit $record) => CustomerCreditResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
