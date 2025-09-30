<?php

namespace App\Filament\Crm\Resources\Invoices\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Enums\Invoices\InvoiceType;
use App\Filament\Crm\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    public function mount(): void
    {
        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'invoice' => Tab::make()
                ->label(__('Invoice'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', InvoiceType::Invoice)),
            'proforma' => Tab::make()
                ->label(__('Proforma'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', InvoiceType::Proforma)),
            'offer' => Tab::make()
                ->label(__('Offer'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', InvoiceType::Offer)),
            'due' => Tab::make()
                ->label(__('Due'))
                ->badge(Cache::flexible('due_invoice_count', [10, 60], fn () => Invoice::query()->due()->where('type', InvoiceType::Invoice)->count()))
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', InvoiceType::Invoice)->due()),
            'all' => Tab::make()
                ->label(__('All')),
        ];
    }
}
