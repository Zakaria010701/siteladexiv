<?php

namespace App\Models;

use App\Enums\Verifications\VerificationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Verification extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'status' => VerificationStatus::class,
    ];

    public function scopeStatus(Builder $query, VerificationStatus $status)
    {
        $query->where('status', $status->value);
    }

    public function controllable(): MorphTo
    {
        return $this->morphTo('controllable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
