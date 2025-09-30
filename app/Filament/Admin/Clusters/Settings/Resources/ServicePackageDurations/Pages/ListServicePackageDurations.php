<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\ServicePackageDurations\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\Settings\Resources\ServicePackageDurations\ServicePackageDurationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServicePackageDurations extends ListRecords
{
    protected static string $resource = ServicePackageDurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
