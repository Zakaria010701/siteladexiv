<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceValues extends Model
{
    protected $guarded = ['id'];

    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn (string $value): mixed => json_decode($value),
            set: fn(mixed $value): string => json_encode($value),
        );
    }

    public function systemResource() : BelongsTo
    {
        return $this->belongsTo(SystemResource::class);
    }

    public function resourceField(): BelongsTo
    {
        return $this->belongsTo(ResourceField::class);
    }
}
