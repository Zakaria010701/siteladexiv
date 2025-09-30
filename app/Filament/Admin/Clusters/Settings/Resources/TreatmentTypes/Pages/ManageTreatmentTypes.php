<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\TreatmentTypes\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\Settings\Resources\TreatmentTypes\TreatmentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTreatmentTypes extends ManageRecords
{
    protected static string $resource = TreatmentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
