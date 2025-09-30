<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\DiscountTemplates\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\Settings\Resources\DiscountTemplates\DiscountTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDiscountTemplates extends ManageRecords
{
    protected static string $resource = DiscountTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
