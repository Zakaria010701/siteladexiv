<?php

namespace App\Filament\Cms\Resources\MediaResource\Pages;

use App\Filament\Cms\Resources\MediaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    protected function afterCreate(): void
    {
        // The Media model will automatically handle Spatie Media creation in its saved event
        \Illuminate\Support\Facades\Log::info('CreateMedia afterCreate completed for media: ' . $this->record->id);
    }
}