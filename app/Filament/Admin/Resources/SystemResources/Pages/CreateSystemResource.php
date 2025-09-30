<?php

namespace App\Filament\Admin\Resources\SystemResources\Pages;

use App\Filament\Admin\Resources\SystemResources\SystemResourceResource;
use App\Models\ResourceField;
use App\Models\SystemResource;
use App\Models\SystemResourceType;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CreateSystemResource extends CreateRecord
{
    protected static string $resource = SystemResourceResource::class;

    protected function handleRecordCreation(array $data): SystemResource
    {
        /** @var SystemResourceType $type */
        $type = SystemResourceType::findOrFail($data['system_resource_type_id']);

        /** @var SystemResource $record */
        $record = $type->systemResources()->create($data);

        $type->resourceFields->each(function (ResourceField $field) use ($data, $record) {
            $value = $data[$field->name] ?? null;

            $record->resourceValues()->create([
                'resource_field_id' => $field->id,
                'value' => $value,
            ]);
        });

        return $record;
    }

    protected function afterCreate(): void
    {
        Cache::forget('appointment_resource_types');
    }
}
