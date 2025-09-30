<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TreatmentType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function appointmentExtras(): BelongsToMany
    {
        return $this->belongsToMany(AppointmentExtra::class)->withTimestamps();
    }

    public function energySettings(): HasMany
    {
        return $this->hasMany(EnergySetting::class);
    }

    public function spotSizeOptions(): HasMany
    {
        return $this->hasMany(TreatmentTypeSpotSizeOption::class);
    }
}
