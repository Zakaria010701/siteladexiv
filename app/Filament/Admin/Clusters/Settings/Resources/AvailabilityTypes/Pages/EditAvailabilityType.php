<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\AvailabilityTypes\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Admin\Clusters\Settings\Resources\AvailabilityTypes\AvailabilityTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAvailabilityType extends EditRecord
{
    protected static string $resource = AvailabilityTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
