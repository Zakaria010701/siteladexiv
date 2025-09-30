<?php

namespace App\Filament\Crm\Resources\Invoices\Pages;

use App\Filament\Crm\Resources\Invoices\Actions\CancelInvoiceAction;
use App\Filament\Crm\Resources\Invoices\Actions\DebtCollectInvoiceAction;
use App\Filament\Crm\Resources\Invoices\Actions\DownloadInvoiceAction;
use App\Filament\Crm\Resources\Invoices\Actions\PayInvoiceAction;
use App\Filament\Crm\Resources\Invoices\Actions\RemindInvoiceAction;
use App\Filament\Crm\Resources\Invoices\Pages\Concerns\MutatesInvoiceItemData;
use App\Support\Calculator;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Enums\Invoices\InvoiceStatus;
use App\Events\Invoices\InvoiceCanceledEvent;
use App\Events\Invoices\InvoiceDebtCollectionEvent;
use App\Events\Invoices\InvoicePaidEvent;
use App\Events\Invoices\InvoiceReminderEvent;
use App\Filament\Actions\ReportBugAction;
use App\Filament\Crm\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditInvoice extends EditRecord
{
    use MutatesInvoiceItemData;

    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                CancelInvoiceAction::make(),
                RemindInvoiceAction::make(),
                DebtCollectInvoiceAction::make(),
                PayInvoiceAction::make(),
            ]),
            DownloadInvoiceAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
            ReportBugAction::make()
                ->reportUrl($this->getUrl(['record' => $this->getRecord()])),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {


        return $this->mutateInvoiceItemData($data);
    }
}
