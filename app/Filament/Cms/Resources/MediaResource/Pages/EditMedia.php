<?php

namespace App\Filament\Cms\Resources\MediaResource\Pages;

use App\Filament\Cms\Resources\MediaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMedia extends EditRecord
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $data = $this->form->getState();

        // Handle file uploads if any files were uploaded
        if (isset($data['files']) && is_array($data['files'])) {
            $this->record->handleFileUploads($data['files']);
            $this->record->save();
        }
    }
}