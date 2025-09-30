<?php

namespace App\Filament\Crm\Resources\WorkTimes\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Crm\Resources\WorkTimes\WorkTimeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageWorkTimes extends ManageRecords
{
    protected static string $resource = WorkTimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
