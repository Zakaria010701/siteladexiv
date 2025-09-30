<?php

namespace App\Filament\Crm\Resources\Vouchers;

use App\Filament\Crm\Resources\Customers\CustomerResource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use App\Filament\Crm\Resources\Vouchers\Pages\ListVouchers;
use App\Filament\Crm\Resources\Vouchers\Pages\CreateVoucher;
use App\Filament\Crm\Resources\Vouchers\Pages\EditVoucher;
use App\Actions\Vouchers\RedeemVoucher;
use App\Filament\Crm\Resources\Customers\Forms\CustomerForm;
use App\Filament\Crm\Resources\VoucherResource\Pages;
use App\Filament\Schemas\Components\CustomerSelect;
use App\Models\Customer;
use App\Models\Voucher;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-ticket';

    public static function getNavigationGroup(): ?string
    {
        return __('Accounts');
    }

    public static function getModelLabel(): string
    {
        return __('Voucher');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Vouchers');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                CustomerSelect::make()
                    ->required(fn (Get $get) => is_null($get('purchaser_id'))),
                Select::make('purchaser_id')
                    ->relationship('purchaser', 'lastname')
                    ->getOptionLabelFromRecordUsing(fn (Customer $record) => $record->full_name)
                    ->searchable(['firstname', 'lastname'])
                    ->createOptionForm(fn (Schema $schema) => CustomerForm::compact($schema))
                    ->required(fn (Get $get) => is_null($get('customer_id'))),
                TextInput::make('voucher_nr')
                    ->required()
                    ->numeric(),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Textarea::make('description')
                    ->columnSpanFull(),
                KeyValue::make('meta')
                    ->visible(auth()->user()->can('admin_voucher'))
                    ->columnSpanFull(),
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
                    ->url(fn (Voucher $record): string => isset($record->customer) ? CustomerResource::getUrl('edit', ['record' => $record->customer]) : '')
                    ->searchable(['firstname', 'lastname']),
                TextColumn::make('purchaser.full_name')
                    ->url(fn (Voucher $record): string => isset($record->purchaser) ? CustomerResource::getUrl('edit', ['record' => $record->purchaser]) : '')
                    ->searchable(['firstname', 'lastname']),
                TextColumn::make('voucher_nr')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('customerCredit.open_amount')
                    ->label(__('Open'))
                    ->money('EUR')
                    ->sortable(),
                IconColumn::make('redeemed')
                    ->boolean()
                    ->state(fn (Voucher $record): bool => $record->is_redeemed),
            ])
            ->filters([
                TernaryFilter::make('redeemed')
                    ->queries(
                        true: fn (Builder $query) => $query->redeemed(),
                        false: fn (Builder $query) => $query->unredeemed()
                    ),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                ActionGroup::make([
                    Action::make('Download')
                        ->icon('heroicon-m-arrow-down-tray')
                        ->action(fn (Voucher $record) => response()->streamDownload(function () use ($record) {
                            echo Pdf::loadView('pdf.voucher', ['voucher' => $record])->stream();
                            //echo view('pdf.voucher', ['voucher' => $record]);
                        }, 'voucher.pdf')),
                    //}, 'voucher.html')),
                    Action::make('redeem')
                        ->icon('heroicon-m-gift')
                        ->schema([
                            CustomerSelect::make()
                                ->required(),
                        ])
                        ->hidden(fn (Voucher $record) => isset($record->customer))
                        ->action(function (array $data, Voucher $record) {
                            RedeemVoucher::make($record, Customer::findOrFail($data['customer_id']))->execute();
                        }),
                    DeleteAction::make(),
                ]),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
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
            'index' => ListVouchers::route('/'),
            'create' => CreateVoucher::route('/create'),
            'edit' => EditVoucher::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
