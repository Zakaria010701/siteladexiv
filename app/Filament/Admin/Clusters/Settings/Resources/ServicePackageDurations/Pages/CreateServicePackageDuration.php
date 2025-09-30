<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\ServicePackageDurations\Pages;

use App\Filament\Admin\Clusters\Settings\Resources\ServicePackageDurations\ServicePackageDurationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServicePackageDuration extends CreateRecord
{
    protected static string $resource = ServicePackageDurationResource::class;
}
