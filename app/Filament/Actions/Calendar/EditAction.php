<?php

namespace App\Filament\Actions\Calendar;

use App\Filament\Widgets\BaseCalendarWidget;
use Filament\Actions\EditAction as BaseEditAction;

class EditAction extends BaseEditAction
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

        $this->after(
            fn (BaseCalendarWidget $livewire) => $livewire->refreshRecords()
        );

        $this->cancelParentActions();
    }
}
