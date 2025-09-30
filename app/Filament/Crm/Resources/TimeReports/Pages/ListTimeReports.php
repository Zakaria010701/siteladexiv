<?php

namespace App\Filament\Crm\Resources\TimeReports\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Crm\Resources\TimeReports\TimeReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTimeReports extends ListRecords
{
    protected static string $resource = TimeReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
