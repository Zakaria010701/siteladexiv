<?php

namespace App\Filament\Crm\Resources\Transactions\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Schemas\Components\Tabs\Tab;
use App\Enums\Transactions\TransactionType;
use App\Filament\Crm\Resources\Transactions\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    public function mount(): void
    {
        parent::mount();

        $this->tableGroupingDirection = 'desc';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('import')
                ->label(__('Import'))
                ->url(TransactionResource::getUrl('create_fints_import')),
        ];
    }

    public function getTabs(): array
    {
        return [
            'deposit' => Tab::make()
                ->label(__('Deposit'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', TransactionType::Deposit)),
            'withdrawal' => Tab::make()
                ->label(__('Withdrawal'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', TransactionType::Withdrawal)),
            'transfer' => Tab::make()
                ->label(__('Transfer'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', TransactionType::Transfer)),
            'all' => Tab::make()
                ->label(__('All')),
        ];
    }
}
