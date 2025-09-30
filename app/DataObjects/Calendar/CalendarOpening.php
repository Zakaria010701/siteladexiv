<?php

namespace App\DataObjects\Calendar;

use App\Models\Contracts\AvailabilityEvent;
use App\Models\Room;
use App\Models\User;
use Carbon\CarbonImmutable;

class CalendarOpening {

    public function __construct(
        public readonly CarbonImmutable $start,
        public readonly CarbonImmutable $end,
        public readonly AvailabilityEvent $availability,
        public readonly Room $room,
        public readonly User $user,
        public readonly array $resources,
    )
    {

    }

    public function toArray(): array
    {
        return [
            'start' => $this->start,
            'end' => $this->end,
            'availability' => $this->availability->toArray(),
            'room' => $this->room->toArray(),
            'user' => $this->user->toArray(),
        ];
    }

}
