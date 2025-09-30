<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\UserWorkTypes\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\Settings\Resources\UserWorkTypes\UserWorkTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageUserWorkTypes extends ManageRecords
{
    protected static string $resource = UserWorkTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
