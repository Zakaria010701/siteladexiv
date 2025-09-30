<?php

namespace App\Filament\Cms\Resources\AllMediaResource\Pages;

use App\Filament\Cms\Resources\AllMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAllMedia extends ListRecords
{
    protected static string $resource = AllMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action for viewing all media
        ];
    }
}