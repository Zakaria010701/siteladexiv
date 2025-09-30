<?php

namespace App\Support;

use App\Models\Availability;
use App\Models\Contracts\AvailabilityEvent;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Database\Eloquent\Builder;

class AvailabilitySupport
{

    public static function findCalendarAvailabilityEventForTime(CarbonImmutable $time, int $room): ?AvailabilityEvent
    {
        return Availability::query()
            ->with([
                'availabilityShifts' => fn (Builder $query) => $query
                    ->where('room_id', $room)
                    ->where('start_date', '<=', $time)
                    ->whereNotNull('start'),
                'availabilityAbsences' => fn (Builder $query) => $query
                    ->where('start_date', '<=', $time)
                    ->where('end_date', '>=', $time),
                'availabilityExceptions' => fn (Builder $query) => $query
                    ->where('date', $time->startOfDay())
                    ->where('room_id', $room)
                    ->whereHas('availabilityType', fn (Builder $query) => $query
                        ->where('is_hidden', false)
                        ->where('is_background_inverted', true)
                        ->where('is_all_day', true))
                    ->whereNotNull('start'),
            ])
            ->whereDoesntHave('availabilityAbsences', fn(Builder $query) => $query
                ->where('start_date', '<=', $time)
                ->where('end_date', '>=', $time))
            ->where('planable_type', User::class)
            ->where('is_background_inverted', true)
            ->where('is_hidden', false)
            ->where('start_date', '<=', $time)
            ->where(fn (Builder $query) => $query
                ->where('end_date', '>=', $time)
                ->orWhereNull('end_date'))
            ->get()
            ->map(fn (Availability $record) => $record->getRecordsForDate($time))
            ->flatten(1)
            ->first();
    }

}
