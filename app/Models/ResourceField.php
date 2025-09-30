<?php

namespace App\Models;

use App\Enums\Resources\ResourceFieldType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceField extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'type' => ResourceFieldType::class,
        'options' => 'array',
        'meta' => 'array',
    ];

    public function systemResourceType() : BelongsTo
    {
        return $this->belongsTo(SystemResourceType::class);
    }
}
