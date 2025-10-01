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

    protected $fillable = [
        'type', 'title', 'parent_id', 'url', 'page', 'icon', 'icon_svg', 'header_contact', 'position'
    ];

    protected $casts = [
        'type' => CmsMenuItemType::class,
        'icon' => 'array', // Handle file upload as array
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

    public function getIcon(): string
    {
        // Handle file upload (array format from Filament)
        if ($this->icon && is_array($this->icon) && !empty($this->icon[0] ?? null)) {
            return asset('storage/' . $this->icon[0]);
        }

        // Handle legacy string format
        if ($this->icon && is_string($this->icon)) {
            return asset('storage/' . $this->icon);
        }

        // Return SVG code if provided
        if ($this->icon_svg) {
            return $this->icon_svg;
        }

        // Return default icon or empty string
        return '';
    }

    public function hasIcon(): bool
    {
        return !empty($this->icon) || !empty($this->icon_svg);
    }
}
