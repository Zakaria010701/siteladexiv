<?php

namespace App\Filament\Actions\Calendar;

use App\Filament\Widgets\BaseCalendarWidget;
use Filament\Actions\ViewAction as BaseViewAction;

class ViewAction extends BaseViewAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->model(
            fn (BaseCalendarWidget $livewire) => $livewire->getModel()
        );

        $this->record(
            fn (BaseCalendarWidget $livewire) => $livewire->getRecord()
        );

        $this->schema(
            fn (BaseCalendarWidget $livewire) => $livewire->getFormSchema()
        );

        $this->modalFooterActions(
            fn (ViewAction $action, BaseCalendarWidget $livewire) => [
                ...$livewire->getCachedModalActions(),
                $action->getModalCancelAction(),
            ]
        );

        $this->after(
            fn (BaseCalendarWidget $livewire) => $livewire->refreshRecords()
        );

        $this->cancelParentActions();
    }
}
