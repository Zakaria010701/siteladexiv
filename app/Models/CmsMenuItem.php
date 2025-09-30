<?php

namespace App\Models;

use App\Enums\Cms\CmsMenuItemType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CmsMenuItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'type' => CmsMenuItemType::class,
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CmsMenuItem::class, 'parent_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo('reference');
    }

    public function childItems(): HasMany
    {
        return $this->hasMany(CmsMenuItem::class, 'parent_id')->orderBy('position');
    }

    public function getReferenceUrl(): string
    {
        if($this->reference instanceof CmsPage) {
            return route('cms.page', ['slug' => $this->reference->slug]);
        }

        if($this->reference instanceof HeaderContact) {
            return '#header-contact';
        }

        return '#';
    }

    public function getUrl(): string
    {
        return match($this->type) {
            CmsMenuItemType::Page => $this->getReferenceUrl(),
            CmsMenuItemType::Icon => $this->url ?? '#',
            default => $this->url ?? '#',
        };
    }
}
