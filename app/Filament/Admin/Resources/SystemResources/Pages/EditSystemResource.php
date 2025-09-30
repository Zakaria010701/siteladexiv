<?php

namespace App\Filament\Admin\Resources\SystemResources\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Admin\Resources\SystemResources\SystemResourceResource;
use App\Models\ResourceField;
use App\Models\SystemResource;
use Exception;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class EditSystemResource extends EditRecord
{
    protected static string $resource = SystemResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('dependencies')
                ->url(SystemResourceResource::getUrl('dependencies', ['record' => $this->record])),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): SystemResource
    {
        if(!$record instanceof SystemResource) {
            throw new Exception("Parameter \$record has to be a SystemResource");
        }

        $record->update($data);

        $record->systemResourceType->resourceFields->each(function (ResourceField $field) use ($data, $record) {
            $value = $data[$field->name] ?? null;

            $record->resourceValues()->updateOrCreate([
                'resource_field_id' => $field->id,
            ], [
                'value' => $value,
            ]);
        });

        return $record;
    }

    protected function afterSave(): void
    {
        Cache::forget('appointment_resource_types');
    }
}
