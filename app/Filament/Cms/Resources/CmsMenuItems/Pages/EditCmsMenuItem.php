<?php

namespace App\Filament\Cms\Resources\CmsMenuItems\Pages;

use App\Filament\Cms\Resources\CmsMenuItems\CmsMenuItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCmsMenuItem extends EditRecord
{
    protected static string $resource = CmsMenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
