<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypes\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypes\SystemResourceTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditSystemResourceType extends EditRecord
{
    protected static string $resource = SystemResourceTypeResource::class;

    public static function getNavigationLabel(): string
    {
        return __('Edit :name', ['name' => __('Type')]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        Cache::forget('appointment_resource_types');
    }
}
