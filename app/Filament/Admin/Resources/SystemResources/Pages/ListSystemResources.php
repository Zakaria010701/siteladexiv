<?php

namespace App\Filament\Admin\Resources\SystemResources\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\SystemResources\SystemResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSystemResources extends ListRecords
{
    protected static string $resource = SystemResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
