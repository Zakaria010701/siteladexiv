<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Clusters\Settings\Resources\Branches\BranchResource;
use App\Filament\Admin\Clusters\Settings\Resources\Rooms\RoomResource;
use App\Filament\Admin\Clusters\Settings\Resources\SystemResourceTypes\SystemResourceTypeResource;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Filament\Widgets\BaseCalendarWidget;
use App\Models\Availability;
use App\Models\Branch;
use App\Models\Room;
use App\Models\SystemResource;
use App\Models\User;
use Carbon\CarbonImmutable;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder;

class ResourceTimelineCalendarWidget extends BaseCalendarWidget
{
    public function getConfig(): array
    {
        return [
            'initialView' => 'resourceTimelineWeek',
            'resourceGroupField' => 'group',
            'resourceOrder' => 'sorting',
            'resourcesInitiallyExpanded' => false,
            'height' => 'auto',
            'nowIndicator' => true,
            'hiddenDays' => [0],
            'weekends' => true,
            'displayEventTime' => false,
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'resourceTimelineWeek,resourceTimelineMonth'
            ],
        ];
    }

    public function fetchResources(array $info): array
    {
        $resources = collect();

        $resources->push(Branch::all()->map(fn (Branch $record) => [
            'id' => "branch-$record->id",
            'group' => __('Branch'),
            'title' => $record->name,
            'url' => BranchResource::getUrl('index', ['record' => $record]),
        ]));

        $resources->push(Room::all()->map(fn (Room $record) => [
            'id' => "room-$record->id",
            'group' => __('Room'),
            'title' => $record->name,
            'url' => RoomResource::getUrl('index', ['record' => $record]),
        ]));

        $resources->push(User::all()->map(fn (User $record) => [
            'id' => "user-$record->id",
            'group' => __('User'),
            'title' => $record->full_name,
            'url' => UserResource::getUrl('edit', ['record' => $record]),
        ]));

        $resources->push(SystemResource::all()->map(fn (SystemResource $record) => [
            'id' => "sresource-$record->id",
            'group' => $record->systemResourceType->name,
            'title' => $record->name,
            'url' => SystemResourceTypeResource::getUrl('resources', ['record' => $record->systemResourceType]),
        ]));

        return $resources->flatten(1)->toArray();
    }

    public function fetchEvents(array $info): array
    {
        $start = CarbonImmutable::parse($info['start']);
        $end = CarbonImmutable::parse($info['end']);
        $events = Availability::query()
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
            ->where('is_hidden', '=', false)
            ->where('start_date', '<=', $end)
            ->where(fn (Builder $query) => $query
                ->where('end_date', '>=', $start)
                ->orWhereNull('end_date'))
            ->get()
            ->map(fn (Availability $record) => $record->getRecordsBetween($start, $end)->map(fn (array $event) => [
                'id' => $record->id,
                'title' => $event['start']->format('H:i'),
                'start' => $event['start'],
                'end' => $event['end'],
                'resourceId' => match($record->planable_type) {
                    User::class => "user-$record->planable_id",
                    SystemResource::class => "sresource-$record->planable_id",
                    default => $record->planable_id,
                },
                'color' => $event['record']->getAvailabilityType()->color ?? $event['record']->color,
                'type' => 'availability',
            ]))
            ->flatten(1)
            ->toArray();

        return $events;
    }
}
