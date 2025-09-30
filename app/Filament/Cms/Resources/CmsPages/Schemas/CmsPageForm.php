<?php

namespace App\Filament\Cms\Resources\CmsPages\Schemas;

use App\Filament\Cms\Resources\CmsPages\Schemas\Components\CmsPageBuilder;
use App\Filament\Cms\Resources\CmsPages\Schemas\Components\CmsPageContentEditor;
use App\Filament\Cms\Resources\CmsPages\Schemas\Components\CmsPageSettingTabs;
use Filament\Schemas\Schema;

class CmsPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                CmsPageSettingTabs::make(),
                CmsPageBuilder::make(),
            ]);
    }
}
