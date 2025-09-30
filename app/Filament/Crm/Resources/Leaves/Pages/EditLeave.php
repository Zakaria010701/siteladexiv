<?php

namespace App\Filament\Crm\Resources\Leaves\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Actions\TimeReport\ApproveLeave;
use App\Actions\TimeReport\DenyLeave;
use App\Filament\Crm\Resources\Leaves\LeaveResource;
use App\Models\Leave;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditLeave extends EditRecord
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->requiresConfirmation()
                ->color('success')
                ->visible(fn (Leave $record) => auth()->user()->can('admin_leave') && ! $record->is_approved)
                ->action(function (Leave $record) {
                    ApproveLeave::make($record, auth()->user())->execute();

                    Notification::make()
                        ->success()
                        ->title(__('status.result.success'))
                        ->send();
                }),
            Action::make('deny')
                ->requiresConfirmation()
                ->color('warning')
                ->visible(fn (Leave $record) => auth()->user()->can('admin_leave') && ! $record->is_denied)
                ->action(function (Leave $record) {
                    DenyLeave::make($record, auth()->user())->execute();

                    Notification::make()
                        ->success()
                        ->title(__('status.result.success'))
                        ->send();
                }),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
