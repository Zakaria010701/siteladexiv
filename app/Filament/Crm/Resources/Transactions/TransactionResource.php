<?php

namespace App\Filament\Crm\Resources\Transactions;

use App\Filament\Crm\Resources\Customers\CustomerResource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Crm\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Crm\Resources\Transactions\Pages\CreateFintsImport;
use App\Filament\Crm\Resources\Transactions\Pages\WatchFintsImport;
use App\Filament\Crm\Resources\Transactions\Pages\CreateTransaction;
use App\Filament\Crm\Resources\Transactions\Pages\EditTransaction;
use App\Enums\Invoices\InvoiceStatus;
use App\Enums\Transactions\TransactionStatus;
use App\Enums\Transactions\TransactionType;
use App\Filament\Crm\Resources\TransactionResource\Pages;
use App\Filament\Crm\Resources\TransactionResource\RelationManagers;
use App\Models\Invoice;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function getNavigationGroup(): ?string
    {
        return __('Accounts');
    }

    public static function getModelLabel(): string
    {
        return __('Transaction');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Transactions');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('account_id')
                    ->relationship('account', 'name')
                    ->required(),
                Select::make('bank_id')
                    ->relationship('bank', 'name')
                    ->required(),
                MorphToSelect::make('bookable')
                    ->types([
                        Type::make(Invoice::class)
                            ->titleAttribute('invoice_number'),
                    ])
                    ->required(),
                DatePicker::make('date')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('description')
                    ->maxLength(1024)
                    ->default(null),
                Select::make('type')
                    ->required()
                    ->options(TransactionType::class),
                Select::make('status')
                    ->required()
                    ->options(TransactionStatus::class),
                KeyValue::make('meta'),
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
            ->groups([
                Group::make('date')
                    ->label(__('Date'))
                    ->getTitleFromRecordUsing(fn (Transaction $record): string => formatDate($record->date))
                    ->titlePrefixedWithLabel(false),
                Group::make('account_id')
                    ->label(__('Account'))
                    ->getTitleFromRecordUsing(fn (Transaction $record): string => $record->account->name)
                    ->titlePrefixedWithLabel(false),
                Group::make('account.iban')
                    ->label(__('Iban'))
                    ->getTitleFromRecordUsing(fn (Transaction $record): string => $record->account->iban)
                    ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('date')
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(TransactionStatus::class),
                SelectFilter::make('type')
                    ->options(TransactionType::class),
                SelectFilter::make('account')
                    ->relationship('account', 'name')
                    ->searchable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('book')
                        ->schema(function (Transaction $record)  {
                            $invoices = collect([]);
                            if(isset($record->customer)) {
                                $invoices = $invoices->merge($record->customer->invoices()->open()->get());
                            }

                            $invoices = $invoices->mapWithKeys(fn (Invoice $invoice) => [
                                $invoice->id => sprintf(
                                    "%s %s (%s)",
                                    $invoice->invoice_number,
                                    formatDate($invoice->invoice_date),
                                    formatMoney($invoice->gross_total)
                                )
                            ]);

                            return [
                                Select::make('invoice')
                                    ->options($invoices->toArray())
                                    ->searchable()
                                    ->preload(),
                            ];
                        })
                        ->action(function (Transaction $record, array $data) {
                            $record->bookable_type = Invoice::class;
                            $record->bookable_id = $data['invoice'];
                            $record->status = TransactionStatus::Booked;
                            $record->save();

                            $invoice = $record->bookable;
                            $invoice->paid_total += $record->amount;
                            if($invoice->paid_total >= $invoice->gross_total) {
                                $invoice->status = InvoiceStatus::Paid;
                            }
                            $invoice->save();
                        }),
                ]),

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
            'index' => ListTransactions::route('/'),
            'create_fints_import' => CreateFintsImport::route('/import/fints/create'),
            'watch_fints_import' => WatchFintsImport::route('/import/fints/{record}/watch'),
            'create' => CreateTransaction::route('/create'),
            'edit' => EditTransaction::route('/{record}/edit'),
        ];
    }
}
