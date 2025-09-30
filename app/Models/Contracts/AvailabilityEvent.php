<?php

namespace App\Models\Contracts;

use App\Models\AvailabilityType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface AvailabilityEvent {

    public function getStartTime(): ?string;

    public function getEndTime(): ?string;

    public function getTargetMinutes(): ?int;

    public function getRoomId(): ?int;

    public function getAvailabilityType(): ?AvailabilityType;

    public function availability(): BelongsTo;

    public function room(): BelongsTo;

    public function toArray();

}
