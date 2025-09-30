<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\ServicePackages\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Admin\Clusters\Settings\Resources\ServicePackages\ServicePackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageServicePackages extends ManageRecords
{
    protected static string $resource = ServicePackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'universal' => Tab::make()
                ->label(__('Universal'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('customer_id')),
            'customer' => Tab::make()
                ->label(__('Customer'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('customer_id')),
            'all' => Tab::make()
                ->label(__('All')),
        ];
    }
}
