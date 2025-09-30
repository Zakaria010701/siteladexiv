<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MediaItem extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'media_items';

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

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
    }

    /**
     * Get the media files associated with this media item
     */
    public function mediaFiles()
    {
        return $this->morphMany(SpatieMedia::class, 'model')->orderBy('created_at', 'desc');
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')
             ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
             ->useDisk('public');
    }

    /**
     * Register media conversions
     */
    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
              ->width(300)
              ->height(300)
              ->sharpen(10)
              ->optimize()
              ->nonQueued();

        $this->addMediaConversion('preview')
              ->width(600)
              ->height(600)
              ->sharpen(10)
              ->optimize()
              ->nonQueued();
    }

    /**
     * Scope for public media
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for specific collection
     */
    public function scopeInCollection($query, $collection)
    {
        return $query->where('collection', $collection);
    }
}
