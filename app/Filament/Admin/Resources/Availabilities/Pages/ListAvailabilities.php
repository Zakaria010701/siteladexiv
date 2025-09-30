<?php

namespace App\Filament\Admin\Resources\Availabilities\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Resources\Availabilities\AvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAvailabilities extends ListRecords
{
    protected static string $resource = AvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
