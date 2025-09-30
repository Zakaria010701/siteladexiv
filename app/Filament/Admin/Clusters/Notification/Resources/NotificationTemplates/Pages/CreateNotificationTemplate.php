<?php

namespace App\Filament\Admin\Clusters\Notification\Resources\NotificationTemplates\Pages;

use App\Filament\Admin\Clusters\Notification\Resources\NotificationTemplates\NotificationTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNotificationTemplate extends CreateRecord
{
    protected static string $resource = NotificationTemplateResource::class;
}
