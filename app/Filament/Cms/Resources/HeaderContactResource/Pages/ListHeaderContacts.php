<?php

namespace App\Filament\Cms\Resources\HeaderContactResource\Pages;

use App\Filament\Cms\Resources\HeaderContactResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHeaderContacts extends ListRecords
{
    protected static string $resource = HeaderContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}