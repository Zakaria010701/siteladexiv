<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\ServicePackageDurations\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Admin\Clusters\Settings\Resources\ServicePackageDurations\ServicePackageDurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServicePackageDuration extends EditRecord
{
    protected static string $resource = ServicePackageDurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
