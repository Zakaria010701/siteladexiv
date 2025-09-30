<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypes\Pages;

use App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypes\SystemResourceTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Cache;

class CreateSystemResourceType extends CreateRecord
{
    protected static string $resource = SystemResourceTypeResource::class;

    protected function afterCreate(): void
    {
        Cache::forget('appointment_resource_types');
    }
}
