<?php

namespace App\Filament\Crm\Resources\Contracts\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Crm\Resources\Contracts\ContractResource;
use App\Models\Contract;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ListContracts extends ListRecords
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function mount(): void
    {
        parent::mount();

        $this->tableGroupingDirection = 'desc';
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
                ->label(__('All')),
            'notVerified' => Tab::make()
                ->label(__('Not verified'))
                ->badge(Cache::remember('contracts_not_verified_count', 120, fn () => Contract::notVerified()->unused()->count()))
                ->modifyQueryUsing(fn (Builder $query) => $query->notVerified()->unused()),
            'notPaid' => Tab::make()
                ->label(__('Not paid'))
                ->badge(Cache::remember('contracts_not_paid_count', 120, fn () => Contract::notPaid()->count()))
                ->modifyQueryUsing(fn (Builder $query) => $query->notPaid()),
        ];
    }
}
