<?php

namespace App\Models;

use App\Enums\User\UserAvailabilityType;
use App\Enums\User\WageType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailabilityUserOption extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'user_availability_type' => UserAvailabilityType::class,
        'wage_type' => WageType::class,
    ];

    public function availability(): BelongsTo
    {
        return $this->belongsTo(Availability::class);
    }

}
