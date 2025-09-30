<?php

namespace App\Filament\Crm\Resources\Invoices\Tables;

use App\Enums\Invoices\InvoiceStatus;
use App\Enums\Invoices\InvoiceType;
use App\Filament\Crm\Resources\Invoices\Actions\CancelInvoiceAction;
use App\Filament\Crm\Resources\Invoices\Actions\DebtCollectInvoiceAction;
use App\Filament\Crm\Resources\Invoices\Actions\DownloadInvoiceAction;
use App\Filament\Crm\Resources\Invoices\Actions\PayInvoiceAction;
use App\Filament\Crm\Resources\Invoices\Actions\RemindInvoiceAction;
use App\Filament\Crm\Resources\Invoices\Actions\SendInvoiceAction;
use App\Filament\Crm\Resources\Invoices\Schemas\Components\InvoiceRecipientSelect;
use App\Filament\Crm\Resources\Invoices\Schemas\Pdf;
use App\Filament\Crm\Resources\Invoices\Tables\Columns\InvoiceRecipientColumn;
use App\Filament\Crm\Resources\Invoices\Tables\Filters\InvoiceDateFilter;
use App\Filament\Crm\Resources\Invoices\Tables\Groups\InvoiceDateGroup;
use App\Filament\Crm\Resources\Invoices\Tables\Groups\InvoiceRecipientGroup;
use App\Filament\Tables\Columns\CreatedAtColumn;
use App\Filament\Tables\Columns\DeletedAtColumn;
use App\Filament\Tables\Columns\IdColumn;
use App\Filament\Tables\Columns\UpdatedAtColumn;
use App\Forms\Components\FusedMorphToSelect;
use App\Models\Customer;
use App\Models\Invoice;
use App\Notifications\Invoices\InvoiceInfoNotification;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\FusedGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoicesTable
{
    public static function configure(Table $table)
    {
        return $table
            ->columns([
                IdColumn::make(),
                CreatedAtColumn::make(),
                UpdatedAtColumn::make(),
                DeletedAtColumn::make(),
                InvoiceRecipientColumn::make(),
                TextColumn::make('source')
                    ->url(fn (Invoice $record) => $record->getSourceUrl())
                    ->formatStateUsing(fn (Invoice $record) => $record->getSourceTitle()),
                TextColumn::make('status')
                    ->sortable()
                    ->badge(),
                TextColumn::make('invoice_number')
                    ->searchable(),
                TextColumn::make('invoice_date')
                    ->date(getDateFormat())
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date(getDateFormat())
                    ->color(fn (Invoice $record) => ($record->due_date->lte(today()) && $record->status->isOpen()) ? 'danger' : null)
                    ->sortable(),
                TextColumn::make('base_total')
                    ->numeric()
                    ->money('Eur', 0, 'de')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('discount_total')
                    ->numeric()
                    ->money('Eur', 0, 'de')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('net_total')
                    ->numeric()
                    ->money('Eur', 0, 'de')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tax_total')
                    ->numeric()
                    ->money('Eur', 0, 'de')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gross_total')
                    ->numeric()
                    ->money('Eur', 0, 'de')
                    ->sortable(),
                TextColumn::make('paid_total')
                    ->numeric()
                    ->money('Eur', 0, 'de')
                    ->sortable(),
            ])
            ->defaultSort('invoice_date', 'desc')
            ->groups([
                InvoiceDateGroup::make(),
                InvoiceRecipientGroup::make(),
            ])
            ->filters([
                TernaryFilter::make('due')
                    ->queries(
                        true: fn (Builder $query) => $query->due(),
                        false: fn (Builder $query) => $query->notDue(),
                        blank: fn (Builder $query) => $query, // In this example, we do not want to filter the query when it is blank.
                    ),
                SelectFilter::make('type')
                    ->options(InvoiceType::class),
                SelectFilter::make('status')
                    ->options(InvoiceStatus::class),
                Filter::make('recipient')
                    ->schema([
                        InvoiceRecipientSelect::make(),
                    ]),
                InvoiceDateFilter::make(),
                TrashedFilter::make(),
            ], FiltersLayout::Modal)
            ->filtersFormColumns(2)
            ->columnManagerColumns(2)
            ->recordActions([
                EditAction::make()
                    ->iconButton(),
                ActionGroup::make([
                    EditAction::make(),
                    ActionGroup::make([
                        DownloadInvoiceAction::make(),
                        SendInvoiceAction::make(),
                    ])->dropdown(false),
                    ActionGroup::make([
                        CancelInvoiceAction::make(),
                        RemindInvoiceAction::make(),
                        DebtCollectInvoiceAction::make(),
                        PayInvoiceAction::make(),
                    ])->dropdown(false),
                    ActionGroup::make([
                        DeleteAction::make(),
                        ForceDeleteAction::make(),
                        RestoreAction::make(),
                    ])->dropdown(false),
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
}