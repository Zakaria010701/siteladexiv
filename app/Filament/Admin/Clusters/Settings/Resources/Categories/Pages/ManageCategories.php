<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\Categories\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\Settings\Resources\Categories\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
