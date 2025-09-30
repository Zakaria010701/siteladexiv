<?php

namespace App\Filament\Admin\Resources\Users\Widgets;

use App\Filament\Widgets\BaseCalendarWidget;
use App\Models\Availability;
use App\Models\SystemResource;
use App\Models\User;
use Carbon\CarbonImmutable;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder;

class UserCalendar extends BaseCalendarWidget
{
    public function getConfig(): array
    {
        return [
            'initialView' => 'timeGridWeek',
            'resourceOrder' => 'sorting',
            'slotLabelInterval' => '00:30',
            'slotDuration' => '00:15',
            'datesAboveResources' => true,
            'dayMinWidth' => 100,
            'height' => 'auto',
            'nowIndicator' => true,
            'hiddenDays' => [0],
            'weekends' => false,
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'timeGridMonth,timeGridWeek,timeGridDay',
            ],
            'selectConstraint' => 'availability',
            'eventConstraint' => 'availability',
            'slotLabelFormat' => [
                [
                    'hour' => 'numeric',
                    'minute' => '2-digit',
                    'omitZeroMinute' => false,
                    'hour12' => false,
                ],
            ],
        ];
    }

    public function fetchResources(array $info): array
    {
        return [];
    }

    public function fetchEvents(array $info): array
    {
        $start = CarbonImmutable::parse($info['start']);
        $end = CarbonImmutable::parse($info['end']);
        return $this->getRecord()->availabilities()
            ->with([
                'availabilityShifts' => fn (EloquentBuilder $query) => $query,
                'availabilityAbsences' => fn (EloquentBuilder $query) => $query
                    ->where('start_date', '<=', $end)
                    ->where('end_date', '>=', $start),
                'availabilityExceptions' => fn (EloquentBuilder $query) => $query
                    ->where('date', '>=', $start)
                    ->where('date', '<=', $end)
                    ->whereNotNull('room_id')
                    ->whereNotNull('start'),
            ])
            ->where('start_date', '<=', $end)
            ->where(fn (Builder $query) => $query
                ->where('end_date', '>=', $start)
                ->orWhereNull('end_date'))
            ->get()
            ->map(function (Availability $record) use ($start, $end) {
                return $record->getRecordsBetween($start, $end)->map(function (array $event) use ($record) {
                    $events = collect();
                    $events->push([
                        'id' => $record->id,
                        'title' => sprintf("%s %s-%s",$event['room']?->name ?? __('No Room'), $event['start']->format('H:i'), $event['end']->format('H:i')),
                        'start' => $event['start'],
                        'end' => $event['end'],
                        'color' => $event['record']->getAvailabilityType()->color ?? $event['record']->color,
                        'allDay' => true,
                    ]);
                    $events->push([
                        'id' => $record->id,
                        'start' => $event['start'],
                        'end' => $event['end'],
                        'display' => 'inverse-background',
                        'groupId' => 'availability',
                    ]);
                    return $events;
                })->flatten(1);
            })
            ->flatten(1)
            ->toArray();
    }
}
