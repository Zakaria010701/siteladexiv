<?php

namespace App\Models;

use App\Models\Concerns\CanBeVerified;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentComplaint extends Model
{
    use CanBeVerified;
    
    protected $guarded = ['id'];

    protected $casts = [
        'meta' => 'array',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function appointmentComplaintType(): BelongsTo
    {
        return $this->belongsTo(AppointmentComplaintType::class);
    }
}
