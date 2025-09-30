<?php

namespace App\Filament\Admin\Resources\Todos\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Admin\Resources\Todos\TodoResource;
use App\Filament\Concerns\HasSaveAndCloseAction;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTodo extends EditRecord
{
    use HasSaveAndCloseAction;

    protected static string $resource = TodoResource::class;

    public function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getSaveAndCloseFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Todo wurde aktualisiert.')
            ->body('Die Todo wurde erfolgreich aktualisiert.');
    }
}
