<?php

namespace App\Models;

use App\Models\Contracts\AvailabilityEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class AvailabilityException extends Model implements AvailabilityEvent
{
    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
    ];

    protected $with = ['availabilityType', 'room'];

    public function getStartTime(): ?string
    {
        return $this->start;
    }

    public function getRoomId(): ?int
    {
        return $this->room_id;
    }

    public function getEndTime(): ?string
    {
        return Carbon::parse($this->start)->addMinutes($this->target_minutes)->format('H:i');
    }

    public function getTargetMinutes(): ?int
    {
        return $this->target_minutes;
    }

    public function getAvailabilityType(): ?AvailabilityType
    {
        return $this->availabilityType ?? $this->availability->availabilityType;
    }

    public function availability(): BelongsTo
    {
        return $this->belongsTo(Availability::class);
    }

    public function availabilityType(): BelongsTo
    {
        return $this->belongsTo(AvailabilityType::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
