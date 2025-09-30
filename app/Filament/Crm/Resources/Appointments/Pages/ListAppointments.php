<?php

namespace App\Filament\Crm\Resources\Appointments\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Enums\Appointments\AppointmentStatus;
use App\Filament\Actions\ReportBugAction;
use App\Filament\Crm\Resources\Appointments\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ReportBugAction::make()
                ->reportUrl($this->getUrl()),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'pending' => Tab::make()
                /** @phpstan-ignore-next-line */
                ->modifyQueryUsing(fn (Builder $query) => $query->status(AppointmentStatus::Pending)),
        ];
    }
}
