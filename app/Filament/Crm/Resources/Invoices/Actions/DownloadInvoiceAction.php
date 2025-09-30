<?php

namespace App\Filament\Crm\Resources\Invoices\Actions;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class DownloadInvoiceAction
{
    public static function make(): Action
    {
        return Action::make('download')
            ->label(__('Download'))
            ->icon(Heroicon::ArrowDownTray)
            ->action(fn (Invoice $record) => response()->streamDownload(function () use ($record) {
                echo Pdf::loadView('pdf.invoice', ['invoice' => $record])->stream();
            }, "$record->invoice_number.pdf"));
    }
}