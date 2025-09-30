<?php

namespace App\Filament\Crm\Resources\Appointments\RelationManagers;

use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use App\Filament\Crm\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use App\Notifications\Invoices\InvoiceInfoNotification;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerInvoiceRelationManager extends RelationManager
{
    protected static string $relationship = 'customerInvoices';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Invoices');
    }

    public static function getModelLabel(): string
    {
        return __('Invoice');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Invoices');
    }

    public function form(Schema $schema): Schema
    {
        return InvoiceResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return InvoiceResource::table($table)
            ->headerActions([
                CreateAction::make()
                    ->url(fn () => InvoiceResource::getUrl('create')),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (Invoice $record) => InvoiceResource::getUrl('edit', ['record' => $record])),
                ActionGroup::make([
                    Action::make('Download')
                        ->icon('heroicon-m-arrow-down-tray')
                        ->action(fn (Invoice $record) => response()->streamDownload(function () use ($record) {
                            echo Pdf::loadView('pdf.invoice', ['invoice' => $record])->stream();
                        }, "$record->invoice_number.pdf")),
                    Action::make('send_notification')
                        ->label(__('Send'))
                        ->icon('heroicon-m-envelope')
                        ->requiresConfirmation()
                        ->action(function (Invoice $record) {
                            $record->recipient->notify(new InvoiceInfoNotification($record));
                            Notification::make()
                                ->title(__('status.result.success'))
                                ->success()
                                ->send();
                        }),
                    DeleteAction::make(),
                ]),
            ]);
    }
}
