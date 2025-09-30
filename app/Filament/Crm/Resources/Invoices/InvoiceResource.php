<?php

namespace App\Filament\Crm\Resources\Invoices;

use App\Filament\Crm\Resources\InvoiceResource\Pages;
use App\Filament\Crm\Resources\Invoices\Pages\CreateInvoice;
use App\Filament\Crm\Resources\Invoices\Pages\EditInvoice;
use App\Filament\Crm\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Crm\Resources\Invoices\Schemas\InvoiceForm;
use App\Filament\Crm\Resources\Invoices\Tables\InvoicesTable;
use App\Models\Appointment;
use App\Models\Invoice;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'invoice_number';

    public static function getNavigationGroup(): ?string
    {
        return __('Accounts');
    }

    public static function getModelLabel(): string
    {
        return __('Invoice');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Invoices');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'invoice_number',
            'recipient.firstname',
            'recipient.lastname',
        ];
    }

    /**
     * @param  Appointment  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('Recipient') => $record->recipient?->full_name,
            __('Date') => $record->invoice_date->format(getDateFormat()),
            __('Price') => formatMoney($record->gross_total),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return InvoiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
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
            'index' => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'edit' => EditInvoice::route('/{record}/edit'),
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
