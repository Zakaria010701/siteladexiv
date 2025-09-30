<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\Services\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\Settings\Resources\Services\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('listDependencies')
                ->label(__('Dependencies'))
                ->color('gray')
                ->url(ServiceResource::getUrl('dependencies')),
            CreateAction::make(),
        ];
    }
}
