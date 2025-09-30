<?php

namespace App\Filament\Cms\Resources\CmsPages\Pages;

use App\Filament\Cms\Resources\CmsPages\CmsPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCmsPage extends CreateRecord
{
    protected static string $resource = CmsPageResource::class;
}
