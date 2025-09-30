<?php

namespace App\Filament\Crm\Resources\Invoices\Actions;

use App\Enums\Invoices\InvoiceStatus;
use App\Events\Invoices\InvoiceCanceledEvent;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;

class CancelInvoiceAction
{
    public static function make(): Action
    {
        return Action::make('cancel_invoice')
            ->label(__('invoice.action.cancel'))
            ->icon(InvoiceStatus::Canceled->getIcon())
            ->color(InvoiceStatus::Canceled->getColor())
            ->requiresConfirmation()
            ->schema([
                Toggle::make('send_notification'),
            ])
            ->hidden(fn (Invoice $record) => $record->status == InvoiceStatus::Canceled)
            ->action(function (array $data, Invoice $record) {
                $record->status = InvoiceStatus::Canceled;
                $record->save();
                InvoiceCanceledEvent::dispatch($record, auth()->user(), $data['send_notification']);
                Notification::make()
                    ->title(__('Invoice has been :status', ['status' => InvoiceStatus::Canceled->getLabel()]))
                    ->success()
                    ->send();
            });
    }
}