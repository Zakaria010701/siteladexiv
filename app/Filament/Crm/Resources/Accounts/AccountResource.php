<?php

namespace App\Filament\Crm\Resources\Accounts;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Crm\Resources\Accounts\Pages\ListAccounts;
use App\Filament\Crm\Resources\Accounts\Pages\CreateAccounts;
use App\Filament\Crm\Resources\Accounts\Pages\EditAccounts;
use App\Filament\Crm\Resources\AccountResource\Pages;
use App\Filament\Crm\Resources\AccountResource\RelationManagers;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use App\Models\Account;
use App\Models\Appointment;
use App\Models\Bank;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    public static function getNavigationGroup(): ?string
    {
        return __('Accounts');
    }

    public static function getModelLabel(): string
    {
        return __('Account');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Accounts');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'iban',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('iban')
                    ->required()
                    ->maxLength(34)
                    ->unique(Bank::class, 'iban', ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime(getDateTimeFormat())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime(getDateTimeFormat())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.full_name')
                    ->numeric()
                    ->url(fn (Account $record): string => isset($record->customer) ? CustomerResource::getUrl('edit', ['record' => $record->customer]) : '')
                    ->sortable()
                    ->searchable(['firstname', 'lastname']),
                TextColumn::make('iban')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccounts::route('/'),
            'create' => CreateAccounts::route('/create'),
            'edit' => EditAccounts::route('/{record}/edit'),
        ];
    }
}
