<?php

namespace App\Filament\Cms\Resources\HeaderContactResource\Pages;

use App\Filament\Cms\Resources\HeaderContactResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHeaderContact extends ViewRecord
{
    protected static string $resource = HeaderContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}