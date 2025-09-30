<?php

namespace App\Filament\Cms\Resources\HeaderContactResource\Pages;

use App\Filament\Cms\Resources\HeaderContactResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHeaderContact extends EditRecord
{
    protected static string $resource = HeaderContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}