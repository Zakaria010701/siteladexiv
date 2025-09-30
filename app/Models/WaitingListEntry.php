<?php

namespace App\Models;

use App\Enums\Appointments\AppointmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaitingListEntry extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'appointment_type' => AppointmentType::class,
        'wish_date' => 'date',
        'wish_date_till' => 'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    public function servicePackage(): BelongsToMany
    {
        return $this->belongsToMany(ServicePackage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
