<?php

namespace App\Filament\Cms\Resources\MediaResource\Pages;

use App\Filament\Cms\Resources\MediaResource;
use App\Filament\Cms\Resources\MediaResource\Schemas\MediaForm;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewMedia extends ViewRecord
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function schema(Schema $schema): Schema
    {
        return MediaForm::viewSchema($schema);
    }
}