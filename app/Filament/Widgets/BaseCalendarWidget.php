<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\Calendar\InteractsWithEvents;
use App\Filament\Widgets\Concerns\Calendar\InteractsWithHeaderActions;
use App\Filament\Widgets\Concerns\Calendar\InteractsWithModalActions;
use App\Filament\Widgets\Concerns\Calendar\InteractsWithRecords;
use App\Filament\Actions\Calendar\CreateAction;
use App\Filament\Actions\Calendar\EditAction;
use App\Filament\Actions\Calendar\DeleteAction;
use App\Filament\Actions\Calendar\ViewAction;
use App\Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;

class BaseCalendarWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithEvents;
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithRecords;
    use InteractsWithHeaderActions;
    use InteractsWithModalActions;

    protected string $view = 'filament.crm.widgets.appointment-calendar-widget';

    protected int|string|array $columnSpan = 'full';

    protected function viewAction(): Action
    {
        return ViewAction::make();
    }

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views.
     *
     * @param  array{start: string, end: string, timezone: string}  $info
     */
    public function fetchEvents(array $info): array
    {
        return [];
    }

    public function getFormSchema(): array
    {
        return [];
    }
}
