<?php

namespace App\Filament\Crm\Resources\WorkTimeGroups\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Crm\Resources\WorkTimeGroups\WorkTimeGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkTimeGroups extends ListRecords
{
    protected static string $resource = WorkTimeGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
