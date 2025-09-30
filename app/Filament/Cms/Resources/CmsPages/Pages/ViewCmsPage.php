<?php

namespace App\Filament\Cms\Resources\CmsPages\Pages;

use App\Filament\Cms\Resources\CmsPages\CmsPageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCmsPage extends ViewRecord
{
    protected static string $resource = CmsPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
