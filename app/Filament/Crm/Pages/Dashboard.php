<?php

namespace App\Filament\Crm\Pages;

use App\Filament\Actions\Appointments\CreateRoomblockAction;
use App\Filament\Actions\Appointments\QuickbookAction;
use App\Filament\Actions\ReportBugAction;
use Filament\Support\Enums\Width;
use App\Filament\Crm\Widgets\AppointmentCalendarWidget;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            QuickbookAction::make()
                ->after(fn () => $this->dispatch('filament-fullcalendar--refresh')),
            CreateRoomblockAction::make()
                ->after(fn () => $this->dispatch('filament-fullcalendar--refresh')),
            ReportBugAction::make()
                ->reportUrl(Dashboard::getUrl()),
        ];
    }

    public function getWidgets(): array
    {
        return [
            AppointmentCalendarWidget::class,
        ];
    }
}
