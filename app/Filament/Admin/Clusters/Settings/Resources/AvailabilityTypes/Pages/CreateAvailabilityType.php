<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\AvailabilityTypes\Pages;

use App\Filament\Admin\Clusters\Settings\Resources\AvailabilityTypes\AvailabilityTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAvailabilityType extends CreateRecord
{
    protected static string $resource = AvailabilityTypeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['group'] = 'availability';

        return $data;
    }
}
