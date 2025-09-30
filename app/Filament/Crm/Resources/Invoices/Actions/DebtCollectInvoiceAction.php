<?php

namespace App\Filament\Crm\Resources\Invoices\Actions;

use App\Enums\Invoices\InvoiceStatus;
use App\Events\Invoices\InvoiceDebtCollectionEvent;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;

class DebtCollectInvoiceAction
{
    public static function make(): Action
    {
        return  Action::make('debt_collect_invoice')
            ->label(__('invoice.action.debt_collect'))
            ->icon(InvoiceStatus::DebtCollection->getIcon())
            ->color(InvoiceStatus::DebtCollection->getColor())
            ->requiresConfirmation()
            ->schema([
                Toggle::make('send_notification'),
            ])
            ->hidden(fn (Invoice $record) => $record->status == InvoiceStatus::DebtCollection)
            ->action(function (array $data, Invoice $record) {
                $record->status = InvoiceStatus::DebtCollection;
                $record->save();
                InvoiceDebtCollectionEvent::dispatch($record, auth()->user(), $data['send_notification']);

                Notification::make()
                    ->title(__('Invoice has been :status', ['status' => InvoiceStatus::DebtCollection->getLabel()]))
                    ->success()
                    ->send();
            });
    }
}