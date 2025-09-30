<?php

namespace App\Filament\Admin\Resources\Availabilities\Pages;

use App\Filament\Admin\Resources\Availabilities\AvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAvailability extends CreateRecord
{
    protected static string $resource = AvailabilityResource::class;
}
