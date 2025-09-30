<?php

namespace App\Filament\Admin\Clusters\Notification\Resources\NotificationTemplates\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Admin\Clusters\Notification\Resources\NotificationTemplates\NotificationTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNotificationTemplate extends EditRecord
{
    protected static string $resource = NotificationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
