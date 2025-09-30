<?php

namespace App\Filament\Admin\Resources\Todos\Pages;

use App\Filament\Admin\Resources\Todos\TodoResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTodo extends CreateRecord
{
    protected static string $resource = TodoResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Eine Todo wurde erstellt';
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Todo wurde erstellt.')
            ->body('Die Todo wurde erfolgreich erstellt.');
    }
}
