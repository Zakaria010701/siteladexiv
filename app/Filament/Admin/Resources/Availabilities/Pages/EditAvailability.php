<?php

namespace App\Filament\Admin\Resources\Availabilities\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Admin\Resources\Availabilities\AvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAvailability extends EditRecord
{
    protected static string $resource = AvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
