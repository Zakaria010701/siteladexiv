<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media_items_legacy';

    protected $fillable = [
        'name',
        'alt',
        'description',
        'type',
        'tags',
        'collection',
        'is_public',
        'files',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_public' => 'boolean',
        'files' => 'array',
    ];
}
