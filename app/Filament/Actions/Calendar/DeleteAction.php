<?php

namespace App\Filament\Actions\Calendar;

use App\Filament\Widgets\BaseCalendarWidget;
use Filament\Actions\DeleteAction as BaseDeleteAction;

class DeleteAction extends BaseDeleteAction
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

        $this->after(
            function (BaseCalendarWidget $livewire) {
                $livewire->record = null;
                $livewire->refreshRecords();
            }
        );

        $this->cancelParentActions();
    }
}
