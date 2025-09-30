<?php

namespace App\Filament\Crm\Resources\TimeReports\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Crm\Resources\TimeReports\TimeReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimeReport extends EditRecord
{
    protected static string $resource = TimeReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
