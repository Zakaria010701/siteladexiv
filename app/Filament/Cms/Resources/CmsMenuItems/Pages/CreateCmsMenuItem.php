<?php

namespace App\Filament\Cms\Resources\CmsMenuItems\Pages;

use App\Enums\Cms\CmsMenuItemType;
use App\Filament\Cms\Resources\CmsMenuItems\CmsMenuItemResource;
use App\Models\CmsPage;
use Filament\Resources\Pages\CreateRecord;

class CreateCmsMenuItem extends CreateRecord
{
    protected static string $resource = CmsMenuItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if($data['type'] == CmsMenuItemType::Page) {
            $data['reference_id'] = $data['page'];
            $data['reference_type'] = CmsPage::class;
        } else {
            $data['reference_id'] = null;
            $data['reference_type'] = null;
        }

        return $data;
    }
}
