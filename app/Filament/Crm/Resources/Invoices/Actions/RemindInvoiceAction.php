<?php

namespace App\Filament\Crm\Resources\Invoices\Actions;

use App\Enums\Invoices\InvoiceStatus;
use App\Events\Invoices\InvoiceReminderEvent;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;

class RemindInvoiceAction
{
    public static function make(): Action
    {
        return Action::make('remind_invoice')
            ->label(__('invoice.action.reminder'))
            ->icon(InvoiceStatus::Reminder->getIcon())
            ->color(InvoiceStatus::Reminder->getColor())
            ->requiresConfirmation()
            ->schema([
                Toggle::make('send_notification'),
            ])
            ->hidden(fn (Invoice $record) => $record->status == InvoiceStatus::Reminder)
            ->action(function (array $data, Invoice $record) {
                $record->status = InvoiceStatus::Reminder;
                $record->save();
                InvoiceReminderEvent::dispatch($record, auth()->user(), $data['send_notification']);
                Notification::make()
                    ->title(__('Invoice has been :status', ['status' => InvoiceStatus::Reminder->getLabel()]))
                    ->success()
                    ->send();
            });
    }
}