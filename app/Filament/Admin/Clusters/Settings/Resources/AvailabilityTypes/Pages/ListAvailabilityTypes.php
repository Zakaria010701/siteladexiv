<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\AvailabilityTypes\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\Settings\Resources\AvailabilityTypes\AvailabilityTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAvailabilityTypes extends ListRecords
{
    protected static string $resource = AvailabilityTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
