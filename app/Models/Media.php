<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class Media extends Model implements HasMedia
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

        static::saved(function ($media) {
            // Handle file uploads when the model is saved
            if ($media->files && is_array($media->files)) {
                foreach ($media->files as $filePath) {
                    try {
                        $fullPath = storage_path('app/' . $filePath);
                        if (file_exists($fullPath)) {
                            $mediaFile = $media
                                ->addMediaFromPath($fullPath)
                                ->usingName($media->name)
                                ->usingFileName(basename($fullPath))
                                ->toMediaCollection($media->collection ?: 'default');

                            \Illuminate\Support\Facades\Log::info('Auto-created Spatie Media: ' . $mediaFile->id . ' for file: ' . $filePath);
                        } else {
                            \Illuminate\Support\Facades\Log::warning('File does not exist at path: ' . $fullPath);
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to auto-create Spatie Media for file ' . $filePath . ': ' . $e->getMessage());
                    }
                }
            }
        });
    }

    /**
     * Get the media files associated with this media item
     */
    public function mediaFiles()
    {
        return $this->morphMany(SpatieMedia::class, 'model');
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default');
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
              ->nonQueued(); // Generate immediately

        $this->addMediaConversion('preview')
              ->width(600)
              ->height(600)
              ->sharpen(10)
              ->optimize()
              ->nonQueued(); // Generate immediately
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
