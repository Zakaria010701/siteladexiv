<?php

namespace App\Filament\Admin\Clusters\Notification\Resources\NotificationTemplates\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Admin\Clusters\Notification\Resources\NotificationTemplates\NotificationTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotificationTemplates extends ListRecords
{
    protected static string $resource = NotificationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
