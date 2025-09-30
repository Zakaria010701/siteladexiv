<?php

namespace App\Models;

use App\Enums\Appointments\Extras\HairType;
use App\Enums\Appointments\Extras\PigmentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnergySetting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'hair_type' => HairType::class,
        'pigment_type' => PigmentType::class,
    ];

    public function treatmentType(): BelongsTo
    {
        return $this->belongsTo(TreatmentType::class);
    }

    public function scopeHairType(Builder $query, HairType $hairType): void
    {
        $query->where('hair_type', $hairType->value);
    }

    public function scopePigmentType(Builder $query, PigmentType $pigmentType): void
    {
        $query->where('pigment_type', $pigmentType->value);
    }
}
