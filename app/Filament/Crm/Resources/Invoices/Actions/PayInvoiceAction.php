<?php

namespace App\Filament\Crm\Resources\Invoices\Actions;

use App\Enums\Invoices\InvoiceStatus;
use App\Events\Invoices\InvoicePaidEvent;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;

class PayInvoiceAction
{
    public static function make(): Action
    {
        return Action::make('pay_invoice')
            ->label(__('invoice.action.paid'))
            ->icon(InvoiceStatus::Paid->getIcon())
            ->color(InvoiceStatus::Paid->getColor())
            ->requiresConfirmation()
            ->schema([
                Toggle::make('send_notification'),
            ])
            ->hidden(fn (Invoice $record) => $record->status == InvoiceStatus::Paid)
            ->action(function (array $data, Invoice $record) {
                $record->status = InvoiceStatus::Paid;
                $record->paid_total = $record->gross_total;
                $record->save();
                InvoicePaidEvent::dispatch($record, auth()->user(), $data['send_notification']);

                Notification::make()
                    ->title(__('Invoice has been :status', ['status' => InvoiceStatus::Paid->getLabel()]))
                    ->success()
                    ->send();
            });
    }
}