<?php

namespace App\Models;

use App\Enums\Notifications\NotificationChannel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CustomerContact extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'channel' => NotificationChannel::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo('subject');
    }
}
