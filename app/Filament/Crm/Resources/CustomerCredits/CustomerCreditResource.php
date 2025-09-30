<?php

namespace App\Filament\Crm\Resources\CustomerCredits;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Crm\Resources\CustomerCredits\RelationManagers\PaymentsRelationManager;
use App\Filament\Crm\Resources\CustomerCredits\Pages\ListCustomerCredits;
use App\Filament\Crm\Resources\CustomerCredits\Pages\CreateCustomerCredit;
use App\Filament\Crm\Resources\CustomerCredits\Pages\EditCustomerCredit;
use App\Filament\Crm\Resources\CustomerCreditResource\Pages;
use App\Filament\Crm\Resources\CustomerCreditResource\RelationManagers;
use App\Filament\Crm\Resources\Customers\CustomerResource;
use App\Filament\Crm\Resources\Customers\Forms\CustomerForm;
use App\Filament\Schemas\Components\CustomerSelect;
use App\Models\Appointment;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Voucher;
use Filament\Forms;
use Filament\Forms\Components\MorphToSelect;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerCreditResource extends Resource
{
    protected static ?string $model = CustomerCredit::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-currency-euro';

    public static function getNavigationGroup(): ?string
    {
        return __('Accounts');
    }

    public static function getModelLabel(): string
    {
        return __('Credit');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Credits');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                CustomerSelect::make()
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                MorphToSelect::make('source')
                    ->disabled()
                    ->dehydrated()
                    ->types([
                        Type::make(Contract::class)
                            ->modifyOptionsQueryUsing(fn (Builder $query, Get $get) => $query->where('customer_id', $get('customer_id')))
                            ->getOptionLabelFromRecordUsing(fn (Contract $record) => "{$record->title}"),
                        Type::make(Voucher::class)
                            ->modifyOptionsQueryUsing(fn (Builder $query, Get $get) => $query->where('customer_id', $get('customer_id')))
                            ->getOptionLabelFromRecordUsing(fn (Voucher $record) => "{$record->voucher_nr}"),
                        Type::make(Appointment::class)
                            ->modifyOptionsQueryUsing(fn (Builder $query, Get $get) => $query->where('customer_id', $get('customer_id')))
                            ->getOptionLabelFromRecordUsing(fn (Appointment $record) => "{$record->title}"),
                    ]),
                DateTimePicker::make('spent_at'),
                Textarea::make('description')
                    ->nullable(),
                KeyValue::make('meta')
                    ->hidden(fn () => auth()->user()->cannot('admin', CustomerCredit::class)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime(getDateTimeFormat())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime(getDateTimeFormat())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime(getDateTimeFormat())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('customer.full_name')
                    ->url(fn (CustomerCredit $record): string => isset($record->customer) ? CustomerResource::getUrl('edit',
                        ['record' => $record->customer]) : '')
                    ->sortable()
                    ->searchable(['firstname', 'lastname']),
                TextColumn::make('amount')
                    ->numeric()
                    ->money('Eur', 0, 'de')
                    ->sortable(),
                TextColumn::make('open_amount')
                    ->label(__('Open'))
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
            PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomerCredits::route('/'),
            'create' => CreateCustomerCredit::route('/create'),
            'edit' => EditCustomerCredit::route('/{record}/edit'),
        ];
    }
}
