<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\Services\Pages;

use App\Filament\Admin\Clusters\Settings\Resources\Services\ServiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;
}
