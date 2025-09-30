<?php

namespace App\Filament\Crm\Resources\Invoices\Actions;

use App\Models\Invoice;
use App\Notifications\Invoices\InvoiceInfoNotification;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class SendInvoiceAction
{
    public static function make(): Action
    {
        return Action::make('send_notification')
            ->label(__('Send'))
            ->icon('heroicon-m-envelope')
            ->requiresConfirmation()
            ->action(function (Invoice $record) {
                $record->recipient->notify(new InvoiceInfoNotification($record));
                Notification::make()
                    ->title(__('status.result.success'))
                    ->success()
                    ->send();
            });
    }
}